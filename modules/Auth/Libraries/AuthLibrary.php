<?php

namespace Modules\Auth\Libraries;

use ci4commonmodel\Models\CommonModel;
use CodeIgniter\Events\Events;
use CodeIgniter\I18n\Time;
use Config\Services;
use Config\Cookie as confCookie;
use Modules\Auth\Config\AuthConfig;
use Modules\Auth\Exceptions\AuthException;
use Modules\Backend\Models\UserModel;

class AuthLibrary
{
    protected $userModel;
    protected $config;
    public $error;
    protected $user;
    protected $commonModel;
    protected $ipAddress;
    protected $now;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->config = new AuthConfig();
        $this->commonModel = new CommonModel();
        $this->user = null;
        $this->config->userTable = 'users';
        $this->ipAddress = Services::request()->getIPAddress();
        // Fixed typo: "appTimzezone" → "appTimezone"
        $timezone = getenv('app.appTimezone') ?: 'UTC';
        $this->now = Time::createFromFormat('Y-m-d H:i:s', (new Time('now'))->toDateTimeString(), $timezone);
        $settings = (object)cache()->get('settings');
        if (empty($settings)) {
            $settings = $this->commonModel->lists('settings');
            $set = [];
            $formatRules = new \CodeIgniter\Validation\FormatRules();
            foreach ($settings as $setting) {
                if ($formatRules->valid_json($setting->content) === true)
                    $set[$setting->option] = (object)json_decode($setting->content, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                else
                    $set[$setting->option] = $setting->content;
            }
            cache()->save('settings', $set, 86400);
        }
    }

    // --- NEW PUBLIC METHOD ---
    /**
     * Get the current logged-in user object (if any)
     *
     * @return object|null
     */
    public function getUser()
    {
        if ($this->isLoggedIn() && is_object($this->user)) {
            return $this->user;
        }
        return null;
    }

    public function login(object $user, bool $remember = false): bool
    {
        if (empty($user)) {
            $this->user = null;
            return false;
        }
        $this->user = $user;
        $groupSefLink = $this->commonModel->selectOne('auth_groups', ['id' => $this->user->group_id], 'seflink');

        $where_or = ['username' => $user->email, 'ip_address' => $this->ipAddress];
        $set_data = ['islocked' => false];
        $this->userModel->updateManyOr('locked', ['islocked' => true], $set_data, '*', $where_or);

        $this->recordLoginAttempt($this->user->email, true);

        session()->set([
            $this->config->logged_in => $this->user->id,
            'group_id' => $this->user->group_id
        ]);

        Services::response()->noCache();

        if ($remember && $this->config->allowRemembering) {
            $this->rememberUser($this->user->id);
        }
        if (mt_rand(1, 100) < 20) {
            $this->userModel->purgeOldRememberTokens();
        }

        // trigger login event, in case anyone cares
        Events::trigger('backend/login', $user);

        return true;
    }

    public function rememberUser(string $userID)
    {
        $selector = bin2hex(random_bytes(12));
        $validator = bin2hex(random_bytes(20));
        $expires = date('Y-m-d H:i:s', time() + $this->config->rememberLength);

        $token = $selector . ':' . $validator;

        // Store it in the database
        $this->userModel->rememberUser($userID, $selector, $validator, $expires);

        $response = Services::response();
        $appConfig = new confCookie();

        // Create the cookie
        $response->setCookie(
            $this->config->rememberCookie,                     // Cookie Name
            $token,                         // Value
            $this->config->rememberLength,
            $appConfig->domain,
            $appConfig->path,
            $appConfig->prefix,
            false,
            true
        );
    }

    public function isLoggedIn(): bool
    {
        if ($userID = session($this->config->logged_in)) {
            if ($this->user === null) {
                $this->user = $this->commonModel->selectOne($this->config->userTable, ['id' => $userID]);
            
            }
            return $this->user !== null;
        }

        return false;
    }

    public function logout()
    {
        $appConfig = new confCookie();
        $response = Services::response();
        $response->deleteCookie($this->config->rememberCookie, $appConfig->domain, $appConfig->path, $appConfig->prefix);
        $oid = session($this->config->logged_in);
        $user = null;
        if ($oid) {
            $user = $this->commonModel->selectOne($this->config->userTable, ['id' => $oid]);
        }

        // Destroy the session data
        if (isset($_SESSION)) {
            foreach ($_SESSION as $key => $value) {
                $_SESSION[$key] = NULL;
                unset($_SESSION[$key]);
            }
        }

        session()->regenerate(true);

        // Clean up remember tokens
        if ($user) {
            $this->commonModel->remove('auth_tokens', ['user_id' => $user->id]);
        }

        // trigger logout event
        if ($user) {
            Events::trigger('logout', $user);
        }
    }

    public function attempt(array $credentials, bool $remember = false): bool
    {
        $this->user = $this->validate($credentials, true);
        $falseLogin = $this->commonModel->selectOne('auth_logins', ['ip_address' => $this->ipAddress], '*', 'id DESC');
        $settings = (object)cache('settings');

        // Kalan deneme hakkı hesaplanıyor.
        if ($falseLogin && $falseLogin->isSuccess === false) {
            if ($falseLogin->counter && ((int)$falseLogin->counter + 1) >= (int)$settings->locked->try) $falseCounter = -1;
            else $falseCounter = $falseLogin->counter;
        } else {
            $falseCounter = 0;
        }

        if (empty($this->user)) {
            $this->recordLoginAttempt($credentials['email'], false, (int)$falseCounter);
            $this->user = null;
            return false;
        }

        if ($this->isBanned($this->user->id)) {
            $this->recordLoginAttempt($credentials['email'], false, (int)$falseCounter);
            $this->error = lang('Auth.userIsBanned');
            $this->user = null;
            return false;
        }

        if (!$this->isActivated($this->user->id)) {
            $this->recordLoginAttempt($credentials['email'], false, (int)$falseCounter);

            $param = http_build_query([
                'login' => urlencode($credentials['email'] ?? $credentials['username'])
            ]);

            $this->error = lang('Auth.notActivated') . ' ' . anchor(route_to('backend/resend-activate-account') . '?' . $param, lang('Auth.activationResend'));

            $this->user = null;
            return false;
        }

        return $this->login($this->user, $remember);
    }

    protected function recordLoginAttempt(string $email, bool $success, int $falseCounter = 0)
    {
        $ipAddress = Services::request()->getIPAddress();
        $user_agent = Services::request()->getUserAgent();

        $agent = null;
        if ($user_agent->isBrowser())
            $agent = $user_agent->getBrowser() . ':' . $user_agent->getVersion();
        elseif ($user_agent->isMobile())
            $agent = $user_agent->getMobile();
        else
            $agent = 'nothing';

        $time = new Time('now');

        return $this->commonModel->create('auth_logins', [
            'ip_address' => $ipAddress,
            'email' => $email,
            'trydate' => $time->toDateTimeString(),
            'isSuccess' => $success,
            'user_agent' => $agent,
            'session_id' => session_id(),
            'counter' => ($success === false) ? $falseCounter + 1 : null,
        ]);
    }

    public function validate(array $credentials, bool $returnUser = false)
    {
        if (empty($credentials['password']) || count($credentials) < 2) return false;

        $password = $credentials['password'];
        unset($credentials['password']);

        if (count($credentials) > 1) throw AuthException::forTooManyCredentials();

        if (!in_array(key($credentials), $this->config->validFields)) throw AuthException::forInvalidFields(key($credentials));

        $user = $this->commonModel->selectOne($this->config->userTable, $credentials);

        if (!$user) {
            $this->error = lang('Auth.badAttempt');
            return false;
        }

        $result = password_verify(base64_encode(
            hash('sha384', $password, true)
        ), $user->password_hash);

        if (!$result) {
            $this->error = sprintf(lang('Auth.invalidPassword'), '<br><b>Kalan deneme hakkınız ' . $this->remainingEntryCalculation() . ' tanedir.<b></b>');
            return false;
        }

        if (password_needs_rehash($user->password_hash, $this->config->hashAlgorithm)) {
            $user->password_hash = $this->setPassword($password);
            $this->commonModel->edit('users', (array)$user, ['id' => $user->id]);
        }

        return $returnUser ? $user : true;
    }

    public function isBanned($pk): bool
    {
        $userStatus = $this->commonModel->selectOne($this->config->userTable, ['id' => $pk], 'status');
        return isset($userStatus->status) && $userStatus->status === 'banned';
    }

    public function isActivated($pk): bool
    {
        $userStatus = $this->commonModel->selectOne($this->config->userTable, ['id' => $pk], 'status');
        return isset($userStatus->status) && $userStatus->status == 'active';
    }

    public function error()
    {
        return $this->error;
    }

    public function check(): bool
    {
        if ($this->isLoggedIn()) return true;

        $remember = Services::request()->getCookie(getenv('cookie.prefix') . $this->config->rememberCookie);

        if (empty($remember)) return false;

        [$selector, $validator] = explode(':', $remember);
        $validator = hash('sha256', $validator);

        $token = $this->commonModel->selectOne('auth_tokens', ['selector' => $selector]);

        if (empty($token)) return false;

        if ($token->expires > date('Y-m-d H:i:s') && hash_equals($token->hashedValidator, $validator)) {
            $user = $this->commonModel->selectOne($this->config->userTable, ['id' => $token->user_id]);
            if ($user) {
                $this->refreshRemember($user->id, $selector);
                $this->login($user);
                return true;
            }
        }

        return false;
    }

    public function refreshRemember(int $userID, string $selector)
    {
        $existing = $this->commonModel->selectOne('auth_tokens', ['selector' => $selector]);

        if (empty($existing)) return $this->rememberUser($userID);

        $validator = bin2hex(random_bytes(20));

        $this->userModel->updateRememberValidator($selector, $validator);

        helper('cookie');
        $appConfig = new confCookie();
        delete_cookie(
            $this->config->rememberCookie,
            $appConfig->domain,
            $appConfig->path,
            $appConfig->prefix
        );
        set_cookie(
            $this->config->rememberCookie,
            $selector . ':' . $validator,
            $this->config->rememberLength,
            $appConfig->domain,
            $appConfig->path,
            $appConfig->prefix,
            false,
            true
        );
    }

    public function setPassword(string $password)
    {
        if ((defined('PASSWORD_ARGON2I') && $this->config->hashAlgorithm == PASSWORD_ARGON2I) || (defined('PASSWORD_ARGON2ID') && $this->config->hashAlgorithm == PASSWORD_ARGON2ID))
            $hashOptions = ['memory_cost' => $this->config->hashMemoryCost, 'time_cost' => $this->config->hashTimeCost, 'threads' => $this->config->hashThreads];
        else
            $hashOptions = ['cost' => $this->config->hashCost];

        $passwordHash = password_hash(
            base64_encode(
                hash('sha384', $password, true)
            ),
            $this->config->hashAlgorithm,
            $hashOptions
        );

        return $passwordHash;
    }

    function randomPassword()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }

    public function generateActivateHash()
    {
        return bin2hex(random_bytes(16));
    }

    public function isBlockedAttempt($username): bool
    {
        $settings = (object)cache('settings');
        if ($settings->locked->isActive) {
            $whitelist = $this->commonModel->selectOne('login_rules', ['type' => 'whitelist']);
            if ($whitelist) {
                foreach ($whitelist->username as $locked_username)
                    if ($locked_username === $username) return false;

                foreach ($whitelist->line as $line)
                    if ($line === $this->ipAddress) return false;

                foreach ($whitelist->range as $range)
                    if ($this->ipRangeControl($range, $this->ipAddress)) return false;
            }

            $blacklist = $this->commonModel->selectOne('login_rules', ['type' => 'blacklist']);
            if ($blacklist) {
                foreach ($blacklist->username as $locked_username)
                    if ($locked_username === $username) return true;

                foreach ($blacklist->username as $line)
                    if ($line === $this->ipAddress) return true;

                foreach ($blacklist->range as $range)
                    if ($this->ipRangeControl($range, $this->ipAddress)) return true;
            }

            $where = ['islocked' => true];
            $where_or = ['username' => $username, 'ip_address' => $this->ipAddress];
            $countLocked = $this->userModel->getOneOr('locked', $where, 'id DESC', 'id,counter,expiry_date', $where_or);

            if (!$countLocked) $countLockedValue = 0;
            else $countLockedValue = $countLocked->counter;

            if ((int)$settings->locked->record <= $countLockedValue) {
                $this->commonModel->edit('locked', ['id' => $countLocked->id], ['counter' => 0]);
                return false;
            }

            $where = ['islocked' => true, 'expiry_date' => ['$gte' => $this->now->toDateTimeString()]];
            $where_or = ['username' => $username, 'ip_address' => $this->ipAddress];
            $lockedNow = $this->userModel->countOr('locked', $where, $where_or);
            if ($lockedNow !== 0) {
                $this->error = "Hesabınız saat : <b>" . Time::createFromFormat('Y-m-d H:i:s', new Time($countLocked->expiry_date), 'Europe/Istanbul')->toLocalizedString('d-MMMM hh:mm z') . "</b> tariğine kadar bloklanmıştır.";
                return true;
            }

            $loginAttempts = $this->userModel->getOneOr('auth_logins', ['isSuccess' => false], 'id DESC', 'id,counter', $where_or);

            if ($loginAttempts && isset($loginAttempts->counter) && ($loginAttempts->counter + 1) >= (int)$settings->locked->try) {
                if (($countLockedValue + 1) < ((int)$settings->locked->record))
                    $expiry_date = Time::createFromFormat('Y-m-d H:i:s', $this->now->addMinutes((int)$settings->locked->min));
                else {
                    $countLockedValue = -1;
                    $expiry_date = Time::createFromFormat('Y-m-d H:i:s', $this->now->addMinutes(1440)); // 24 hours
                }

                $this->commonModel->create('locked', [
                    'type' => null,
                    'ip_address' => $this->ipAddress,
                    'username' => $username,
                    'isLocked' => true,
                    'counter' => ($countLockedValue + 1),
                    'locked_at' => $this->now->toDateTimeString(),
                    'expiry_date' => $expiry_date->toDateTimeString(),
                ]);

                return false;
            } else return false;
        } else return false;
    }

    public function ipRangeControl($range, $ipAddress): bool
    {
        $parseRange = explode('-', $range);
        if (
            $this->ipFormatContol($ipAddress, $parseRange[0], $parseRange[1]) &&
            $this->ip2long_vX($ipAddress) >= $this->ip2long_vX($parseRange[0]) &&
            $this->ip2long_vX($ipAddress) <= $this->ip2long_vX($parseRange[1])
        )
            return true;
        else
            return false;
    }

    public function ipFormatContol($ipAddress, $rangeStart, $rangeEnd): bool
    {
        $ips = array('ipAddress' => $ipAddress, 'rangeStart' => $rangeStart, 'rangeEnd' => $rangeEnd);
        $ipsFormat = [];
        foreach ($ips as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) $ipsFormat[] = 'ip4';
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) $ipsFormat[] = 'ip6';
        }
        if (count(array_unique($ipsFormat)) === 1) return true;
        else return false;
    }

    public function ip2long_vX($ip)
    {
        $ip_n = inet_pton($ip);
        $bin = '';
        for ($bit = strlen($ip_n) - 1; $bit >= 0; $bit--) {
            $bin = sprintf('%08b', ord($ip_n[$bit])) . $bin;
        }
        if (function_exists('gmp_init')) return (int)gmp_strval(gmp_init($bin, 2), 10);
        elseif (function_exists('bcadd')) {
            $dec = '0';
            for ($i = 0; $i < strlen($bin); $i++) {
                $dec = bcmul($dec, '2', 0);
                $dec = bcadd($dec, $bin[$i], 0);
            }
            return (int)$dec;
        } else trigger_error('GMP or BCMATH extension not installed!', E_USER_ERROR);
    }

    public function remainingEntryCalculation()
    {
        $falseLogin = $this->commonModel->selectOne('auth_logins', ['ip_address' => $this->ipAddress], '*', 'id DESC');
        $settings = (object)cache('settings');
        if ($falseLogin) return (int)$settings->locked->try - (int)$falseLogin->counter - 1;
        else return (int)$settings->locked->try - 1;
    }

    public function has_perm(string $module, string $method = ''): mixed
    {
        if ($method == 'error_403') return true;
        $cache = (array)$this->userModel->getPermissionsForUser(session()->get($this->config->logged_in), session()->get('group_id'));
        if (empty($cache)) return false;
        $searchValues = [str_replace('\\', '-', $module), $method];
        $perms = array_filter($cache, function ($item) use ($searchValues) {
            return $item['className'] === $searchValues[0] && $item['methodName'] === $searchValues[1] && (bool)$item['isActive'] === true;
        });
        $perms = reset($perms);
        if ($perms === false) return false;
        if (empty($perms['typeOfPermissions'])) return redirect()->route('backend_404');
        $typeOfPermissions = (array)json_decode($perms['typeOfPermissions']);
        $intersect = array_intersect_assoc($typeOfPermissions, $perms);

        if (!empty($intersect)) return true;
        else return false;
    }

    public function sidebarNavigation()
    {
        $searchValues = [1];
        $navigation = array_filter(
            cache(session()->get($this->config->logged_in) . '_permissions') ?? [],
            fn($item) => (bool) $item['inNavigation'] === (bool) $searchValues[0]
        );
        $nav = array_filter($navigation, fn($item) => $this->has_perm($item['className'], $item['methodName']));
        usort($nav, fn($a, $b) => $a['pageSort'] <=> $b['pageSort']);
        $nav = array_map(fn($item) => (object) $item, $nav);
        return $nav;
    }
}