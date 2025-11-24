<?php

/**
 * Backend Module Routes
 */

$routes->group('backend', ['namespace' => 'Modules\Backend\Controllers'], function ($routes) {
    // Dashboard
    $routes->get('/', 'Backend::index', ['as' => 'backend', 'role' => 'read']);
    $routes->get('403', 'Errors::error_403', ['as' => '403']);

    // Categories (from Blog module)
    $routes->group('categories', function ($routes) {
        $routes->get('', '\Modules\Blog\Controllers\Categories::index/1', ['as' => 'categories', 'role' => 'read']); // ← no number → page 1
        $routes->get('(:num)', '\Modules\Blog\Controllers\Categories::index/$1', ['role' => 'read']);
        $routes->get('new', '\Modules\Blog\Controllers\Categories::new', ['as' => 'categoryCreate', 'role' => 'create']);
        $routes->post('new', '\Modules\Blog\Controllers\Categories::new', ['role' => 'create']);
        $routes->get('edit/(:num)', '\Modules\Blog\Controllers\Categories::edit/$1', ['as' => 'categoryEdit', 'role' => 'update']);
        $routes->post('edit/(:num)', '\Modules\Blog\Controllers\Categories::edit/$1', ['role' => 'update']);
        $routes->get('delete/(:num)', '\Modules\Blog\Controllers\Categories::delete/$1', ['as' => 'categoryDelete', 'role' => 'delete']);
    });

    // Tags (from Blog module)
    $routes->group('tags', function ($routes) {
        $routes->get('', '\Modules\Blog\Controllers\Tags::index/1', ['as' => 'tags', 'role' => 'read']); // ← no number → page 1
        $routes->get('(:num)', '\Modules\Blog\Controllers\Tags::index/$1', ['role' => 'read']);
        $routes->post('new', '\Modules\Blog\Controllers\Tags::create', ['as' => 'tagCreate', 'role' => 'create']);
        $routes->get('edit/(:num)', '\Modules\Blog\Controllers\Tags::edit/$1', ['as' => 'tagEdit', 'role' => 'update']);
        $routes->post('edit/(:num)', '\Modules\Blog\Controllers\Tags::edit/$1', ['role' => 'update']);
        $routes->get('delete/(:num)', '\Modules\Blog\Controllers\Tags::delete/$1', ['as' => 'tagDelete', 'role' => 'delete']);
    });

     // Exam Papers
    $routes->group('exam-papers', function ($routes) {
        $routes->get('upload', 'ExamPaperController::create', ['as' => 'examPaperUpload', 'role' => 'create']);
        $routes->post('upload', 'ExamPaperController::store', ['role' => 'create']);
        $routes->get('edit/(:num)', 'ExamPaperController::edit/$1', ['as' => 'examPaperEdit', 'role' => 'update']);
        $routes->post('edit/(:num)', 'ExamPaperController::update/$1', ['role' => 'update']);
        $routes->get('delete/(:num)', 'ExamPaperController::delete/$1', ['as' => 'examPaperDelete', 'role' => 'delete']);

        $routes->get('', 'ExamPaperController::index', ['as' => 'examPapers', 'role' => 'read']);
    });

    // Other Pages
    $routes->post('tagify', 'AJAX::limitTags_ajax', ['as' => 'tagify', 'role' => 'delete']);
    $routes->post('checkSeflink', 'AJAX::autoLookSeflinks', ['as' => 'checkSeflink', 'role' => 'delete']);
    $routes->post('isActive', 'AJAX::isActive', ['as' => 'isActive', 'role' => 'delete']);
    $routes->post('maintenance', 'AJAX::maintenance', ['as' => 'maintenance', 'role' => 'update']);

    // Log module
    $routes->group('locked', function ($routes) {
        $routes->get('(:num)', 'Locked::index/$1', ['as' => 'locked', 'role' => 'read,create,update,delete']);
    });
});