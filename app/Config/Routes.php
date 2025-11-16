<?php

if (empty(cache('settings'))) {
    $commonModel = new \ci4commonmodel\Models\CommonModel();
    $settings    = $commonModel->lists('settings');
    $set         = [];
    $formatRules = new \CodeIgniter\Validation\FormatRules();
    foreach ($settings as $setting) {
        if ($formatRules->valid_json($setting->content) === true)
            $set[$setting->option] = (object) json_decode($setting->content, JSON_UNESCAPED_UNICODE);
        else
            $set[$setting->option] = $setting->content;
    }
    cache()->save('settings', $set, 86400);
    $settings = (object) $set;
} else {
    $settings = (object) cache('settings');
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

// ── Frontend Public Auth Routes ─────────────────────────────────
$routes->get('register', 'Home::register');
$routes->post('register', 'Home::registerPost');
$routes->get('login', 'Home::login');
$routes->post('login', 'Home::loginPost');
$routes->get('logout', 'Home::logout');
$routes->get('forgot-password', 'Home::forgotPassword');
$routes->post('forgot-password', 'Home::forgotPasswordPost');
$routes->get('reset-password/(:any)', 'Home::resetPassword/$1');
$routes->post('reset-password/(:any)', 'Home::resetPasswordPost/$1');

/**
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Include Templates Routes Files
 * --------------------------------------------------------------------
 */
if (!empty($settings->templateInfos->path) && is_dir(APPPATH . 'Config')) {
    $modulesPath = APPPATH . 'Config';
    $modules     = scandir($modulesPath . '/templates');
    foreach ($modules as $module) {
        if ($module === '.' || $module === '..') continue;
        if (is_dir($modulesPath) . '/' . $module) {
            $routesPath = $modulesPath . '/templates/' . $settings->templateInfos->path . '/Routes.php';
            if (is_file($routesPath)) require($routesPath);
            else continue;
        }
    }
}

/**
 * --------------------------------------------------------------------
 * Include Modules Routes Files (FIXED PATH)
 * --------------------------------------------------------------------
 */
$modulesPath = FCPATH . '../modules/';
if (is_dir($modulesPath)) {
    $modules = scandir($modulesPath);
    foreach ($modules as $module) {
        if ($module === '.' || $module === '..') continue;
        $routesPath = $modulesPath . $module . '/Config/Routes.php';
        if (is_file($routesPath)) {
            require $routesPath;
        }
    }
}

/*
 * @var RouteCollection $routes
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index', ['filter' => 'ci4ms', 'as' => 'home']);
$routes->get('maintenance-mode', 'Home::maintenanceMode', ['as' => 'maintenance-mode']);
$routes->get('blog', 'Home::blog', ['filter' => 'ci4ms']);
$routes->get('blog/(:num)', 'Home::blog/$1', ['filter' => 'ci4ms']);
$routes->get('blog/(:any)', 'Home::blogDetail/$1', ['filter' => 'ci4ms']);
$routes->get('tag/(:any)', 'Home::tagList/$1', ['filter' => 'ci4ms', 'as' => 'tag']);
$routes->get('category/(:any)', 'Home::category/$1', ['filter' => 'ci4ms', 'as' => 'category']);
$routes->post('newComment', 'Home::newComment', ['filter' => 'ci4ms', 'as' => 'newComment']);
$routes->post('repliesComment', 'Home::repliesComment', ['filter' => 'ci4ms', 'as' => 'repliesComment']);
$routes->post('loadMoreComments', 'Home::loadMoreComments', ['filter' => 'ci4ms', 'as' => 'loadMoreComments']);
$routes->post('commentCaptcha', 'Home::commentCaptcha', ['filter' => 'ci4ms', 'as' => 'commentCaptcha']);
$routes->post('search', 'Home::search', ['filter' => 'ci4ms', 'as' => 'search']);
$routes->get('/(:any)', 'Home::index/$1', ['filter' => 'ci4ms']);