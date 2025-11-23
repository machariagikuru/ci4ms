<?php

if (empty(cache('settings'))) {
    $commonModel = new \ci4commonmodel\Models\CommonModel();
    $settings = $commonModel->lists('settings');
    $set = [];
    $formatRules = new \CodeIgniter\Validation\FormatRules();
    foreach ($settings as $setting) {
        if ($formatRules->valid_json($setting->content) === true)
            $set[$setting->option] = (object)json_decode($setting->content, JSON_UNESCAPED_UNICODE);
        else
            $set[$setting->option] = $setting->content;
    }
    cache()->save('settings', $set, 86400);
    $settings = (object)$set;
} else {
    $settings = (object)cache('settings');
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override('App\Controllers\Errors::error404');

/**
 * --------------------------------------------------------------------
 * Environment-specific Routes
 * --------------------------------------------------------------------
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Include Templates Routes
 * --------------------------------------------------------------------
 */
if (!empty($settings->templateInfos->path) && is_dir(APPPATH . 'Config')) {
    $modulesPath = APPPATH . 'Config';
    $modules = scandir($modulesPath . '/templates');
    foreach ($modules as $module) {
        if ($module === '.' || $module === '..') continue;
        if (is_dir($modulesPath . '/' . $module)) {
            $routesPath = $modulesPath . '/templates/' . $settings->templateInfos->path . '/Routes.php';
            if (is_file($routesPath)) require($routesPath);
        }
    }
}

/**
 * --------------------------------------------------------------------
 * Include Modules Routes (Backend / Auth)
 * --------------------------------------------------------------------
 */
if (is_dir(ROOTPATH . 'modules')) {
    $modulesPath = ROOTPATH . 'modules/';
    $modules = scandir($modulesPath);

    foreach ($modules as $module) {
        if ($module === '.' || $module === '..') continue;
        if (is_dir($modulesPath . '/' . $module)) {
            $routesPath = $modulesPath . $module . '/Config/Routes.php';
            if (is_file($routesPath)) require($routesPath);
        }
    }
}

/*
 * @var RouteCollection $routes
 */

// Frontend Default Routes
$routes->get('/', 'Frontend\PageController::home', ['filter' => 'ci4ms', 'as' => 'home']);
$routes->get('maintenance-mode', 'Frontend\PageController::maintenanceMode', ['as' => 'maintenance-mode']);

// Blog
$routes->get('blog', 'Frontend\BlogController::blog', ['filter' => 'ci4ms']);
$routes->get('blog/(:num)', 'Frontend\BlogController::blog/$1', ['filter' => 'ci4ms']);
$routes->get('blog/(:any)', 'Frontend\BlogController::blogDetail/$1', ['filter' => 'ci4ms']);
// Tags & Categories
$routes->get('tag/(:any)', 'Frontend\BlogController::tagList/$1', ['filter' => 'ci4ms', 'as' => 'tag']);
$routes->get('category/(:any)', 'Frontend\BlogController::category/$1', ['filter' => 'ci4ms', 'as' => 'category']);
$routes->get('categories', 'Frontend\BlogController::browseCategories');
$routes->get('categories/(:num)', 'Frontend\BlogController::browseCategories/$1');
$routes->get('tags', 'Frontend\BlogController::browseTags');
$routes->get('tags/(:num)', 'Frontend\BlogController::browseTags/$1');

// Comments
$routes->post('newComment', 'Frontend\CommentController::newComment', ['filter' => 'ci4ms', 'as' => 'newComment']);
$routes->post('repliesComment', 'Frontend\CommentController::repliesComment', ['filter' => 'ci4ms', 'as' => 'repliesComment']);
$routes->post('loadMoreComments', 'Frontend\CommentController::loadMoreComments', ['filter' => 'ci4ms', 'as' => 'loadMoreComments']);
$routes->post('commentCaptcha', 'Frontend\CommentController::commentCaptcha', ['filter' => 'ci4ms', 'as' => 'commentCaptcha']);
$routes->post('search', 'Home::search', ['filter' => 'ci4ms', 'as' => 'search']);

// Frontend Auth Routes (handled by main app routes.php)
$routes->get('register', 'Frontend\AuthController::register');
$routes->post('register', 'Frontend\AuthController::registerPost');
$routes->get('login', 'Frontend\AuthController::login');
$routes->post('login', 'Frontend\AuthController::loginPost');
$routes->get('logout', 'Frontend\AuthController::logout');
$routes->get('forgot-password', 'Frontend\AuthController::forgotPassword');
$routes->post('forgot-password', 'Frontend\AuthController::forgotPasswordPost');
$routes->get('reset-password/(:any)', 'Frontend\AuthController::resetPassword/$1');
$routes->post('reset-password/(:any)', 'Frontend\AuthController::resetPasswordPost/$1');

// My Account
$routes->group('my-account', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'Frontend\AuthController::dashboard');
    $routes->get('profile', 'Frontend\AuthController::profile');
    $routes->post('profile', 'Frontend\AuthController::profileUpdate');
    $routes->get('password', 'Frontend\AuthController::password');
    $routes->post('password', 'Frontend\AuthController::passwordUpdate');
});

// Wildcard route for dynamic pages
$routes->get('/(:any)', 'Frontend\PageController::index/$1', ['filter' => 'ci4ms']);
