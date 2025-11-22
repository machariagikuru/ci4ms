<?php

namespace Modules\Auth\Controllers;

use App\Libraries\CommonLibrary;
use CodeIgniter\I18n\Time;
use Modules\Backend\Models\UserModel;

class AuthController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // --- Helper: Generate arithmetic CAPTCHA ---
    protected function generateMathCaptcha()
    {
        $num1 = random_int(1, 10);
        $num2 = random_int(1, 10);
        $answer = $num1 + $num2;
        session()->set('admin_math_captcha_answer', $answer);
        return "$num1 + $num2";
    }

    public function login()
    {
        $session = session();

        if ($this->request->is('post')) {
            $rules = [
                'email' => 'required|valid_email',
                'password' => 'required',
                'captcha' => 'required'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $userAnswer = (int) $this->request->getPost('captcha');
            $correctAnswer = (int) $session->get('admin_math_captcha_answer');

            if ($userAnswer !== $correctAnswer) {
                // Remove CAPTCHA after checking failure
                $session->remove('admin_math_captcha_answer');
                return redirect()->back()->withInput()->with('error', lang('Auth.badCaptcha') ?: 'Incorrect CAPTCHA answer.');
            }

            $login = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $remember = (bool) $this->request->getPost('remember');

            if ($this->authLib->isBlockedAttempt($login)) {
                return redirect()->back()->withInput()->with('error', $this->authLib->error() ?? lang('Auth.loginBlock'));
            }

            if (!$this->authLib->attempt(['email' => $login, 'password' => $password], $remember)) {
                return redirect()->back()->withInput()->with('error', $this->authLib->error() ?? lang('Auth.badAttempt'));
            }

            // Clear CAPTCHA after successful login
            $session->remove('admin_math_captcha_answer');
            $redirectURL = $session->get('redirect_url') ?? 'backend';
            $session->remove('redirect_url');

            return redirect()->route($redirectURL)->withCookies()->with('message', lang('Auth.loginSuccess'));
        }

        // Generate CAPTCHA only if it does not exist
        if (!$session->has('admin_math_captcha_answer')) {
            $mathCaptcha = $this->generateMathCaptcha();
        } else {
            // Optional: regenerate for display consistency
            $mathCaptcha = $this->generateMathCaptcha();
        }

        return view('Modules\Auth\Views\login', [
            'config' => $this->config,
            'mathCaptcha' => $mathCaptcha
        ]);
    }

    /**
     * Log the user out.
     */
    public function logout()
    {
        if ($this->authLib->check()) {
            $this->authLib->logout();
        }
        return redirect()->route('login');
    }

    // --- Password reset & activation ---
    public function forgotPassword()
    {
        if ($this->request->is('post')) {
            helper('debug');
            $rules = ['email' => 'required|valid_email'];
            if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

            if ($this->config->activeResetter === false) return redirect()->route('login')->with('error', lang('Auth.forgotDisabled'));

            $user = $this->commonModel->selectOne('users', ['email' => $this->request->getPost('email')]);
            if (is_null($user)) return redirect()->back()->with('error', lang('Auth.forgotNoUser'));

            $this->commonModel->edit('users', [
                'reset_hash' => $this->authLib->generateActivateHash(),
                'reset_expires' => date('Y-m-d H:i:s', time() + $this->config->resetTime)
            ], ['id' => $user->id]);

            $user = $this->commonModel->selectOne('users', ['id' => $user->id]);
            $commonLibrary = new CommonLibrary();
            $mailResult = $commonLibrary->phpMailer(
                'noreply@' . $_SERVER['HTTP_HOST'],
                'noreply@' . $_SERVER['HTTP_HOST'],
                ['mail' => $user->email],
                'noreply@' . $_SERVER['HTTP_HOST'],
                'Information',
                lang('Auth.membershipPasswordReset'),
                lang('passwordResetMessage', [date('d-m-Y H:i:s', strtotime($user->reset_expires)), site_url('backend/reset-password/' . $user->reset_hash)])
            );

            if ($mailResult === true) {
                return redirect()->route('login')->with('message', lang('Auth.forgotEmailSent'));
            } else {
                return redirect()->back()->withInput()->with('error', $mailResult ?? lang('Auth.unknownError'));
            }
        }

        if ($this->config->activeResetter === false) {
            return redirect()->route('login')->with('error', lang('Auth.forgotDisabled'));
        }

        return view($this->config->views['forgot'], ['config' => $this->config]);
    }

    public function resetPassword($token)
    {
        if ($this->request->is('post')) {
            if ($this->config->activeResetter === false) return redirect()->route('login')->with('error', lang('Auth.forgotDisabled'));

            $this->commonModel->create('auth_reset_password_attempts', [
                'email' => $this->request->getPost('email'),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => (string)$this->request->getUserAgent(),
                'token' => $token,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $rules = [
                'email' => 'required|valid_email',
                'password' => 'required|min_length[8]',
                'pass_confirm' => 'required|matches[password]',
            ];

            if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

            $user = $this->commonModel->selectOne('users', ['email' => $this->request->getPost('email'), 'reset_hash' => $token]);
            if (is_null($user)) return redirect()->back()->with('error', lang('Auth.forgotNoUser'));

            $time = Time::parse($user->reset_expires);
            if (!empty($user->reset_expires) && time() > $time->getTimestamp()) {
                return redirect()->back()->withInput()->with('error', lang('Auth.resetTokenExpired'));
            }

            $this->commonModel->edit('users', [
                'password_hash' => $this->authLib->setPassword($this->request->getPost('password')),
                'reset_hash' => null,
                'reset_expires' => null,
                'force_pass_reset' => false,
                'reset_at' => new Time('now'),
            ], ['id' => $user->id]);

            return redirect()->route('login')->with('message', lang('Auth.resetSuccess'));
        }

        if ($this->config->activeResetter === false) return redirect()->route('login')->with('error', lang('Auth.forgotDisabled'));

        return view($this->config->views['reset'], ['config' => $this->config, 'token' => $token]);
    }

    public function activateAccount($token)
    {
        $this->commonModel->create('auth_email_activation_attempts', [
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => (string)$this->request->getUserAgent(),
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $throttler = service('throttler');
        if ($throttler->check($this->request->getIPAddress(), 2, MINUTE) === false) {
            return $this->response->setStatusCode(429)->setBody(lang('Auth.tooManyRequests', [$throttler->getTokentime()]));
        }

        $user = $this->commonModel->selectOne('users', ['activate_hash' => $token, 'status' => 'deactive']);
        if (is_null($user)) return redirect()->route('login')->with('error', lang('Auth.activationNoUser'));

        $this->commonModel->edit('users', ['status' => 'active', 'activate_hash' => null], ['id' => $user->id]);

        return redirect()->route('login')->with('message', lang('Auth.registerSuccess'));
    }

    public function activateEmail($token)
    {
        $this->commonModel->createOne('auth_email_activation_attempts', [
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => (string)$this->request->getUserAgent(),
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $throttler = service('throttler');
        if ($throttler->check($this->request->getIPAddress(), 2, MINUTE) === false) {
            return $this->response->setStatusCode(429)->setBody(lang('Auth.tooManyRequests', [$throttler->getTokentime()]));
        }

        $user = $this->commonModel->selectOne('users', ['activate_hash' => $token, 'status' => 'deactive']);
        if (is_null($user)) return redirect()->route('login')->with('error', lang('Auth.activationNoUser'));

        $this->commonModel->edit('users', ['status' => 'active', 'activate_hash' => null], ['id' => $user->id]);

        return redirect()->route('login')->with('message', lang('Auth.emailActivationuccess'));
    }
}
