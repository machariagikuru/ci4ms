<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authLib = new \Modules\Auth\Libraries\AuthLibrary();
        if (!$authLib->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Please log in to access this page.');
        }

        // Ensure user is NOT admin (group_id = 1)
        if (session('group_id') == 1) {
            return redirect()->to('/backend');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing needed
    }
}