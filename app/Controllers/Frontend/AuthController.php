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
}