<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Modules\Auth\Libraries\AuthLibrary;

class AuthController extends BaseController
{
    public function register()
    {
        if (service('auth')->isLoggedIn()) {
            return redirect()->to('/');
        }
        return view('auth/register');
    }

    public function registerPost()
    {
        $rules = [
            'firstname' => 'required|min_length[2]',
            'sirname'   => 'required|min_length[2]',
            'email'     => 'required|valid_email|is_unique[users.email]',
            'password'  => 'required|min_length[6]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $authLib = new AuthLibrary();
        $data = [
            'firstname'     => $this->request->getPost('firstname'),
            'sirname'       => $this->request->getPost('sirname'),
            'email'         => $this->request->getPost('email'),
            'username'      => null, // not required for frontend
            'password_hash' => $authLib->setPassword($this->request->getPost('password')),
            'group_id'      => 2, // ← registered user group
            'status'        => 'active',
            'created_at'    => date('Y-m-d H:i:s'),
        ];

        if ($this->commonModel->create('users', $data)) {
            return redirect()->to('/login')->with('message', 'Registration successful! Please log in.');
        }

        return redirect()->back()->with('error', 'An error occurred. Please try again.');
    }

    public function login()
    {
        if (service('auth')->isLoggedIn()) {
            return redirect()->to('/');
        }
        return view('auth/login');
    }

    public function loginPost()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $authLib = new AuthLibrary();
        if ($authLib->attempt(['email' => $email, 'password' => $password])) {
            return redirect()->to('/');
        }

        return redirect()->back()->withInput()->with('error', $authLib->error());
    }

    public function logout()
    {
        $authLib = new AuthLibrary();
        $authLib->logout();
        return redirect()->to('/');
    }
}