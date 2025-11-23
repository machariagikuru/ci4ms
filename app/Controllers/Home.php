<?php

namespace App\Controllers;

use App\Libraries\CommonLibrary;
use App\Models\Ci4ms;
use CodeIgniter\I18n\Time;
use Modules\Backend\Models\AjaxModel;
use Modules\Users\Models\UserscrudModel;

class Home extends BaseController
{
    private $commonLibrary;
    private $ci4msModel;

    public function __construct()
    {
        $this->commonLibrary = new CommonLibrary();
        $this->ci4msModel = new Ci4ms();
    }

    // --- Helper: Generate arithmetic CAPTCHA ---
    protected function generateMathCaptcha()
    {
        $num1 = random_int(1, 10);
        $num2 = random_int(1, 10);
        $answer = $num1 + $num2;
        session()->set('math_captcha_answer', $answer);
        return "$num1 + $num2";
    }

    // --- Structured Homepage (Partials) ---
    public function home()
    {
        // Fetch editable sections (navbar, hero, etc.)
        $sections = ['navbar', 'hero', 'features', 'testimonials'];
        foreach ($sections as $section) {
            $this->defData[$section] = $this->commonModel->selectOne('pages', ['seflink' => $section]);
        }

        // Fetch dynamic data
        $this->defData['latestBlogs'] = $this->commonModel->lists('blog', '*', ['isActive' => true], 'id DESC', 3);
        $this->defData['categories'] = $this->commonModel->lists('categories', '*', ['isActive' => true], 'title ASC');
        $this->defData['tags'] = $this->commonModel->lists('tags', '*', [], 'tag ASC', 30); // top 30 tags

        // SEO
        $this->defData['seo'] = $this->ci4msseoLibrary->metaTags(
            'Home Page',
            'Welcome to our site',
            '/',
            ['keywords' => ['home', 'welcome']],
            ''
        );

        return view('templates/default/home', $this->defData);
    }

    // --- Public Pages ---
    public function index(string $seflink = '/')
    {
        $page = $this->commonModel->selectOne('pages', ['seflink' => $seflink]);
        if (!empty($page)) {
            $this->defData['pageInfo'] = $page;
            $this->defData['pageInfo']->content = $this->commonLibrary->parseInTextFunctions($this->defData['pageInfo']->content);

            $seo = !empty($page->seo) ? json_decode($page->seo, false) : (object) [];
            if (!is_object($seo)) $seo = (object) [];
            $this->defData['pageInfo']->seo = $seo;

            $keywords = [];
            if (!empty($seo->keywords) && is_array($seo->keywords)) {
                foreach ($seo->keywords as $keyword) {
                    if (isset($keyword->value)) $keywords[] = $keyword->value;
                }
            }

            $description = $seo->description ?? '';
            $coverImage = $seo->coverImage ?? '';

            $this->defData['seo'] = $this->ci4msseoLibrary->metaTags(
                $this->defData['pageInfo']->title,
                $description,
                $seflink,
                ['keywords' => $keywords],
                $coverImage
            );

            $this->defData['schema'] = $this->ci4msseoLibrary->ldPlusJson('Organization', [
                'url' => site_url(),
                'logo' => $this->defData['settings']->logo ?? '',
                'name' => $this->defData['settings']->siteName ?? '',
                'children' => [
                    'ContactPoint' => [
                        'ContactPoint' => [
                            'telephone' => $this->defData['settings']->company->phone ?? '',
                            'contactType' => 'customer support'
                        ]
                    ]
                ],
                'sameAs' => array_map(fn($sN) => $sN['link'] ?? '', (array)($this->defData['settings']->socialNetwork ?? []))
            ]);

            if ($seflink !== '/') {
                $this->defData['breadcrumbs'] = $this->commonLibrary->get_breadcrumbs((int)$this->defData['pageInfo']->id, 'page');
            }

            return view('templates/' . ($this->defData['settings']->templateInfos->path ?? 'default') . '/pages', $this->defData);
        }

        return show_404();
    }

    public function maintenanceMode()
    {
        if (!((bool)($this->defData['settings']->maintenanceMode->scalar ?? false))) {
            return redirect()->route('home');
        }
        return view('maintenance', $this->defData);
    }


    // --- Public Auth: Login & Register (Frontend) ---

    public function register()
    {
        $authLib = new \Modules\Auth\Libraries\AuthLibrary();
        if ($authLib->isLoggedIn()) {
            return redirect()->to('/');
        }

        $mathCaptcha = $this->generateMathCaptcha();
        return view('auth/register', array_merge($this->defData, ['mathCaptcha' => $mathCaptcha]));
    }

    public function registerPost()
    {
        $authLib = new \Modules\Auth\Libraries\AuthLibrary();
        if ($authLib->isLoggedIn()) {
            return redirect()->to('/');
        }

        $rules = [
            'firstname' => 'required|min_length[2]',
            'sirname'   => 'required|min_length[2]',
            'email'     => 'required|valid_email|is_unique[users.email]',
            'password'  => 'required|min_length[6]',
            'captcha'   => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userAnswer = (int) $this->request->getPost('captcha');
        $correctAnswer = (int) session()->get('math_captcha_answer');
        if ($userAnswer !== $correctAnswer) {
            return redirect()->back()->withInput()->with('error', lang('Auth.badCaptcha') ?: 'Incorrect CAPTCHA answer.');
        }

        $data = [
            'firstname'     => $this->request->getPost('firstname'),
            'sirname'       => $this->request->getPost('sirname'),
            'email'         => $this->request->getPost('email'),
            'username'      => null,
            'password_hash' => $authLib->setPassword($this->request->getPost('password')),
            'group_id'      => 2,
            'status'        => 'active',
            'created_at'    => date('Y-m-d H:i:s'),
        ];

        if ($this->commonModel->create('users', $data)) {
            session()->remove('math_captcha_answer');
            return redirect()->to('/login')->with('message', 'Registration successful!');
        }

        return redirect()->back()->with('error', 'An error occurred during registration.');
    }

    public function login()
    {
        $authLib = new \Modules\Auth\Libraries\AuthLibrary();
        if ($authLib->isLoggedIn()) {
            return redirect()->to('/');
        }

        $mathCaptcha = $this->generateMathCaptcha();
        return view('auth/login', array_merge($this->defData, ['mathCaptcha' => $mathCaptcha]));
    }

    public function loginPost()
    {
        $authLib = new \Modules\Auth\Libraries\AuthLibrary();
        if ($authLib->isLoggedIn()) {
            return redirect()->to('/');
        }

        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
            'captcha'  => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userAnswer = (int) $this->request->getPost('captcha');
        $correctAnswer = (int) session()->get('math_captcha_answer');
        if ($userAnswer !== $correctAnswer) {
            return redirect()->back()->withInput()->with('error', lang('Auth.badCaptcha') ?: 'Incorrect CAPTCHA answer.');
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $remember = (bool) $this->request->getPost('remember');

        if ($authLib->attempt(['email' => $email, 'password' => $password], $remember)) {
            session()->remove('math_captcha_answer');
            return redirect()->to('/');
        }

        return redirect()->back()->withInput()->with('error', $authLib->error() ?? 'Invalid credentials.');
    }

    // --- Frontend Forgot Password ---
    public function forgotPassword()
    {
        $authLib = new \Modules\Auth\Libraries\AuthLibrary();
        if ($authLib->isLoggedIn()) {
            return redirect()->to('/');
        }
        return view('auth/forgot', $this->defData);
    }

    public function forgotPasswordPost()
    {
        $authLib = new \Modules\Auth\Libraries\AuthLibrary();
        if ($authLib->isLoggedIn()) {
            return redirect()->to('/');
        }

        $rules = ['email' => 'required|valid_email'];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $user = $this->commonModel->selectOne('users', ['email' => $email, 'group_id !=' => 1]);

        if (! $user) {
            return redirect()->back()->with('error', lang('Auth.forgotNoUser'));
        }

        $token = $authLib->generateActivateHash();
        $this->commonModel->edit('users', [
            'reset_hash' => $token,
            'reset_expires' => date('Y-m-d H:i:s', time() + 3600)
        ], ['id' => $user->id]);

        $commonLibrary = new \App\Libraries\CommonLibrary();
        $mailResult = $commonLibrary->phpMailer(
            'noreply@' . $_SERVER['HTTP_HOST'],
            'noreply@' . $_SERVER['HTTP_HOST'],
            ['mail' => $email],
            'Password Reset',
            lang('Auth.membershipPasswordReset'),
            "Click to reset your password: " . site_url("reset-password/$token")
        );

        if ($mailResult === true) {
            return redirect()->to('/login')->with('message', lang('Auth.forgotEmailSent'));
        }

        return redirect()->back()->with('error', lang('Auth.unknownError'));
    }

    // --- Frontend Reset Password ---
    public function resetPassword(string $token)
    {
        $user = $this->commonModel->selectOne('users', ['reset_hash' => $token]);
        if (! $user || strtotime($user->reset_expires) < time()) {
            return redirect()->to('/login')->with('error', lang('Auth.resetTokenExpired'));
        }

        return view('auth/reset', array_merge($this->defData, ['token' => $token]));
    }

    public function resetPasswordPost(string $token)
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
            'pass_confirm' => 'required|matches[password]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $user = $this->commonModel->selectOne('users', [
            'email' => $email,
            'reset_hash' => $token
        ]);

        if (! $user) {
            return redirect()->back()->with('error', lang('Auth.forgotNoUser'));
        }

        if (strtotime($user->reset_expires) < time()) {
            return redirect()->back()->with('error', lang('Auth.resetTokenExpired'));
        }

        $authLib = new \Modules\Auth\Libraries\AuthLibrary();
        $this->commonModel->edit('users', [
            'password_hash' => $authLib->setPassword($this->request->getPost('password')),
            'reset_hash' => null,
            'reset_expires' => null
        ], ['id' => $user->id]);

        return redirect()->to('/login')->with('message', lang('Auth.resetSuccess'));
    }

    // --- User Dashboard ---
    public function dashboard()
    {
        $authLib = new \Modules\Auth\Libraries\AuthLibrary();
        if (!$authLib->isLoggedIn() || session('group_id') == 1) {
            return redirect()->to('/');
        }

        $user = $authLib->getUser();
        return view('auth/dashboard', array_merge($this->defData, ['user' => $user]));
    }

    public function profile()
    {
        $authLib = new \Modules\Auth\Libraries\AuthLibrary();
        if (!$authLib->isLoggedIn() || session('group_id') == 1) {
            return redirect()->to('/');
        }

        $user = $authLib->getUser();
        return view('auth/profile', array_merge($this->defData, ['user' => $user]));
    }

    public function profileUpdate()
    {
        $authLib = new \Modules\Auth\Libraries\AuthLibrary();
        if (!$authLib->isLoggedIn() || session('group_id') == 1) {
            return redirect()->to('/');
        }

        $rules = [
            'firstname' => 'required|min_length[2]',
            'sirname'   => 'required|min_length[2]',
            'email'     => "required|valid_email|is_unique[users.email,id,{$authLib->getUser()->id}]"
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'firstname' => $this->request->getPost('firstname'),
            'sirname'   => $this->request->getPost('sirname'),
            'email'     => $this->request->getPost('email')
        ];

        if ($this->commonModel->edit('users', $data, ['id' => $authLib->getUser()->id])) {
            return redirect()->back()->with('message', 'Profile updated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to update profile.');
    }

    public function password()
    {
        $authLib = new \Modules\Auth\Libraries\AuthLibrary();
        if (!$authLib->isLoggedIn() || session('group_id') == 1) {
            return redirect()->to('/');
        }

        return view('auth/password', $this->defData);
    }

    public function passwordUpdate()
    {
        $authLib = new \Modules\Auth\Libraries\AuthLibrary();
        if (!$authLib->isLoggedIn() || session('group_id') == 1) {
            return redirect()->to('/');
        }

        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $user = $authLib->getUser();
        $current = $this->request->getPost('current_password');
        if (!password_verify(base64_encode(hash('sha384', $current, true)), $user->password_hash)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $newHash = $authLib->setPassword($this->request->getPost('new_password'));
        if ($this->commonModel->edit('users', ['password_hash' => $newHash], ['id' => $user->id])) {
            $authLib->logout();
            return redirect()->to('/login')->with('message', 'Password changed. Please log in again.');
        }

        return redirect()->back()->with('error', 'Failed to update password.');
    }

    public function logout()
    {
        $authLib = new \Modules\Auth\Libraries\AuthLibrary();
        $authLib->logout();
        return redirect()->to('/');
    }
}