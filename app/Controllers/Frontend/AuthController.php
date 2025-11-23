<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Libraries\CommonLibrary;
use Modules\Auth\Libraries\AuthLibrary;

class AuthController extends BaseController
{
    private CommonLibrary $commonLibrary;

    public function __construct()
    {
        $this->commonLibrary = new CommonLibrary();
        // Dependencies like $this->defData, $this->commonModel inherited from BaseController
    }

    public function register()
    {
        $authLib = new AuthLibrary();
        if ($authLib->isLoggedIn()) {
            return redirect()->to('/');
        }

        $mathCaptcha = $this->generateMathCaptcha();
        return view('auth/register', array_merge($this->defData, ['mathCaptcha' => $mathCaptcha]));
    }

    public function registerPost()
    {
        $authLib = new AuthLibrary();
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

    protected function generateMathCaptcha()
    {
        $num1 = random_int(1, 10);
        $num2 = random_int(1, 10);
        $answer = $num1 + $num2;
        session()->set('math_captcha_answer', $answer);
        return "$num1 + $num2";
    }

    public function login()
    {
        $authLib = new AuthLibrary();
        if ($authLib->isLoggedIn()) {
            return redirect()->to('/');
        }

        $mathCaptcha = $this->generateMathCaptcha();
        return view('auth/login', array_merge($this->defData, ['mathCaptcha' => $mathCaptcha]));
    }

    public function loginPost()
    {
        $authLib = new AuthLibrary();
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

    public function forgotPassword()
    {
        $authLib = new AuthLibrary();
        if ($authLib->isLoggedIn()) {
            return redirect()->to('/');
        }
        return view('auth/forgot', $this->defData);
    }

    public function forgotPasswordPost()
    {
        $authLib = new AuthLibrary();
        if ($authLib->isLoggedIn()) {
            return redirect()->to('/');
        }

        $rules = ['email' => 'required|valid_email'];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $user = $this->commonModel->selectOne('users', ['email' => $email, 'group_id !=' => 1]);

        if (!$user) {
            return redirect()->back()->with('error', lang('Auth.forgotNoUser'));
        }

        $token = $authLib->generateActivateHash();
        $this->commonModel->edit('users', [
            'reset_hash' => $token,
            'reset_expires' => date('Y-m-d H:i:s', time() + 3600)
        ], ['id' => $user->id]);

        $commonLibrary = new CommonLibrary();
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

    public function resetPassword(string $token)
    {
        $user = $this->commonModel->selectOne('users', ['reset_hash' => $token]);
        if (!$user || strtotime($user->reset_expires) < time()) {
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

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $user = $this->commonModel->selectOne('users', [
            'email' => $email,
            'reset_hash' => $token
        ]);

        if (!$user) {
            return redirect()->back()->with('error', lang('Auth.forgotNoUser'));
        }

        if (strtotime($user->reset_expires) < time()) {
            return redirect()->back()->with('error', lang('Auth.resetTokenExpired'));
        }

        $authLib = new AuthLibrary();
        $this->commonModel->edit('users', [
            'password_hash' => $authLib->setPassword($this->request->getPost('password')),
            'reset_hash' => null,
            'reset_expires' => null
        ], ['id' => $user->id]);

        return redirect()->to('/login')->with('message', lang('Auth.resetSuccess'));
    }

    public function logout()
    {
        $authLib = new AuthLibrary();
        $authLib->logout();
        return redirect()->to('/');
    }

    public function dashboard()
    {
        $authLib = new AuthLibrary();
        if (!$authLib->isLoggedIn() || session('group_id') == 1) {
            return redirect()->to('/');
        }

        $user = $authLib->getUser();
        return view('auth/dashboard', array_merge($this->defData, ['user' => $user]));
    }

    public function profile()
    {
        $authLib = new AuthLibrary();
        if (!$authLib->isLoggedIn() || session('group_id') == 1) {
            return redirect()->to('/');
        }

        $user = $authLib->getUser();
        return view('auth/profile', array_merge($this->defData, ['user' => $user]));
    }

    public function profileUpdate()
    {
        $authLib = new AuthLibrary();
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
        $authLib = new AuthLibrary();
        if (!$authLib->isLoggedIn() || session('group_id') == 1) {
            return redirect()->to('/');
        }

        return view('auth/password', $this->defData);
    }

    public function passwordUpdate()
    {
        $authLib = new AuthLibrary();
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
}