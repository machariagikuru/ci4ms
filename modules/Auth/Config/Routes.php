<?php

use CodeIgniter\Router\RouteCollection;

/**
 * --------------------------------------------------------------------
 * Frontend Auth Routes (Public)
 * --------------------------------------------------------------------
 */
$routes->group('', ['namespace' => 'Modules\Auth\Controllers'], function ($routes) {
    // Login/out
    $routes->get('login', 'AuthController::login', ['as' => 'login']);
    $routes->post('login', 'AuthController::loginPost');
    $routes->get('logout', 'AuthController::logout', ['as' => 'logout']);

    // Registration
    $routes->get('register', 'AuthController::register', ['as' => 'register']);
    $routes->post('register', 'AuthController::registerPost');

    // Forgot / Reset
    $routes->get('forgot-password', 'AuthController::forgotPassword', ['as' => 'forgot']);
    $routes->post('forgot-password', 'AuthController::forgotPasswordPost');
    $routes->get('reset-password/(:any)', 'AuthController::resetPassword/$1', ['as' => 'reset-password']);
    $routes->post('reset-password/(:any)', 'AuthController::resetPasswordPost/$1');
});

/**
 * --------------------------------------------------------------------
 * Backend Auth Routes (Protected / Admin)
 * --------------------------------------------------------------------
 */
$routes->group('backend', ['namespace' => 'Modules\Auth\Controllers'], function ($routes) {
    // Admin login/out (optional, separate from frontend)
    $routes->match(['GET', 'POST'], 'login', 'AuthController::login', ['as' => 'backend-login']);
    $routes->get('logout', 'AuthController::logout', ['as' => 'backend-logout']);

    // Activation
    $routes->get('activate-account/(:any)', 'AuthController::activateAccount/$1', ['as' => 'activate-account']);
    $routes->get('activate-email/(:any)', 'AuthController::activateEmail/$1', ['as' => 'activate-email']);

    // Forgot/Resets for backend
    $routes->match(['GET', 'POST'], 'forgot', 'AuthController::forgotPassword', ['as' => 'backend-forgot']);
    $routes->match(['GET', 'POST'], 'reset-password/(:any)', 'AuthController::resetPassword/$1', ['as' => 'backend-reset-password']);
    $routes->post('reset-password/(:any)', 'AuthController::attemptReset/$1', []);
});
