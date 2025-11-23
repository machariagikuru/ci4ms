<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --------------------------------------------------------------------
// Default Setup
// --------------------------------------------------------------------
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override('App\Controllers\Errors::error404');

// --------------------------------------------------------------------
// Frontend Routes
// --------------------------------------------------------------------
$routes->get('/', 'Frontend\PageController::home', ['as' => 'home']);
$routes->get('maintenance-mode', 'Frontend\PageController::maintenanceMode', ['as' => 'maintenance-mode']);

// Blog
$routes->get('blog', 'Frontend\BlogController::blog');
$routes->get('blog/(:num)', 'Frontend\BlogController::blog/$1');
$routes->get('blog/(:any)', 'Frontend\BlogController::blogDetail/$1');

// Tags & Categories
$routes->get('tag/(:any)', 'Frontend\BlogController::tagList/$1', ['as' => 'tag']);
$routes->get('category/(:any)', 'Frontend\BlogController::category/$1', ['as' => 'category']);
$routes->get('categories', 'Frontend\BlogController::browseCategories', ['as' => 'frontend-categories']);
$routes->get('categories/(:num)', 'Frontend\BlogController::browseCategories/$1');
$routes->get('tags', 'Frontend\BlogController::browseTags', ['as' => 'frontend-tags']);
$routes->get('tags/(:num)', 'Frontend\BlogController::browseTags/$1');

// Auth
$routes->get('register', 'Frontend\AuthController::register');
$routes->post('register', 'Frontend\AuthController::registerPost');
$routes->get('login', 'Frontend\AuthController::login');
$routes->post('login', 'Frontend\AuthController::loginPost');
$routes->get('logout', 'Frontend\AuthController::logout');
$routes->get('forgot-password', 'Frontend\AuthController::forgotPassword');
$routes->post('forgot-password', 'Frontend\AuthController::forgotPasswordPost');
$routes->get('reset-password/(:any)', 'Frontend\AuthController::resetPassword/$1');
$routes->post('reset-password/(:any)', 'Frontend\AuthController::resetPasswordPost/$1');

// My Account (protected)
$routes->group('my-account', ['filter' => 'auth'], static function ($routes) {
    $routes->get('', 'Frontend\AuthController::dashboard');
    $routes->get('profile', 'Frontend\AuthController::profile');
    $routes->post('profile', 'Frontend\AuthController::profileUpdate');
    $routes->get('password', 'Frontend\AuthController::password');
    $routes->post('password', 'Frontend\AuthController::passwordUpdate');
});

// Comments
$routes->post('newComment', 'Frontend\CommentController::newComment', ['as' => 'newComment']);
$routes->post('repliesComment', 'Frontend\CommentController::repliesComment', ['as' => 'repliesComment']);
$routes->post('loadMoreComments', 'Frontend\CommentController::loadMoreComments', ['as' => 'loadMoreComments']);
$routes->post('commentCaptcha', 'Frontend\CommentController::commentCaptcha', ['as' => 'commentCaptcha']);
$routes->post('search', 'Home::search', ['as' => 'search']);

// --------------------------------------------------------------------
// Module Routes (Loaded only in HTTP context to avoid CLI issues)
// --------------------------------------------------------------------
if (!\is_cli()) {
    $modulesPath = ROOTPATH . 'modules/';
    if (is_dir($modulesPath)) {
        $modules = scandir($modulesPath);
        foreach ($modules as $module) {
            if (in_array($module, ['.', '..', '.git', '.DS_Store'])) {
                continue;
            }
            $routesPath = $modulesPath . $module . '/Config/Routes.php';
            if (is_file($routesPath)) {
                require $routesPath;
            }
        }
    }

    // --------------------------------------------------------------------
    // Wildcard Route (MUST be last)
    // --------------------------------------------------------------------
    $routes->get('(:any)', 'Frontend\PageController::index/$1');

}