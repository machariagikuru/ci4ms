<?php

$routes->group('backend', ['namespace' => 'Modules\Auth\Controllers'], function ($routes) {
    // Backend Login/Logout
    $routes->match(['GET', 'POST'], 'login', 'AuthController::login', ['as' => 'backend-login']);
    $routes->get('logout', 'AuthController::logout', ['as' => 'backend-logout']);

    // Activation
    $routes->get('activate-account/(:any)', 'AuthController::activateAccount/$1', ['as' => 'activate-account']);
    $routes->get('activate-email/(:any)', 'AuthController::activateEmail/$1', ['as' => 'activate-email']);

    // Forgot/Reset Password
    $routes->match(['GET', 'POST'], 'forgot', 'AuthController::forgotPassword', ['as' => 'backend-forgot']);
    $routes->match(['GET', 'POST'], 'reset-password/(:any)', 'AuthController::resetPassword/$1', ['as' => 'backend-reset-password']);
    $routes->post('reset-password/(:any)', 'AuthController::attemptReset/$1');
});
