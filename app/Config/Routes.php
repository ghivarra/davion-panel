<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// login routes
$routes->group($_ENV['LOGIN_PAGE'], static function($routes) {
    $routes->get('/', 'Admin\\LoginController::index', ['as' => 'login']);
    $routes->match(['options', 'post'], 'authenticate', 'Admin\\LoginController::authenticate');
});

// dashboard routes
$routes->group($_ENV['PANEL_PAGE'], static function($routes) {

    // dashboard
    $routes->get('/', 'Admin\\DashboardController::index', ['as' => 'panel.dashboard']);

    // public routes
    $routes->group('public', static function($routes) {
        $routes->match(['options', 'get'], 'logout', 'Admin\\PublicController::logout');
        $routes->match(['options', 'get'], 'session-data', 'Admin\\PublicController::sessionData');
        $routes->match(['options', 'get'], 'menu', 'Admin\\PublicController::menu');
        $routes->match(['options', 'post'], 'menu/search', 'Admin\\PublicController::searchMenu');
    });

    // Account routes
    $routes->group('account', static function($routes) {
        $routes->match(['options', 'post'], 'change-password', 'Admin\\AccountController::changePassword');
        $routes->match(['options', 'post'], 'update', 'Admin\\AccountController::update');
    });

    // Website routes
    $routes->group('website', static function($routes) {
        $routes->match(['options', 'get'], 'data', 'Admin\\WebsiteController::data');
        $routes->match(['options', 'post'], 'main-form-update', 'Admin\\WebsiteController::mainFormUpdate');
        $routes->match(['options', 'post'], 'logo-update', 'Admin\\WebsiteController::logoUpdate');
        $routes->match(['options', 'post'], 'icon-update', 'Admin\\WebsiteController::iconUpdate');
    });

    // Module Routes
    $routes->group('module', static function($routes) {
        $routes->match(['options', 'get'], 'group-list', 'Admin\\ModuleController::groupList');
        $routes->match(['options', 'get'], 'get', 'Admin\\ModuleController::get');
        $routes->match(['options', 'post'], 'datatable', 'Admin\\ModuleController::datatable');
        $routes->match(['options', 'post'], 'create', 'Admin\\ModuleController::create');
        $routes->match(['options', 'post'], 'update', 'Admin\\ModuleController::update');
        $routes->match(['options', 'post'], 'update-status', 'Admin\\ModuleController::updateStatus');
        $routes->match(['options', 'post'], 'delete', 'Admin\\ModuleController::delete');
    });

    // Menu Routes
    $routes->group('menu', static function($routes) {
        $routes->match(['options', 'get'], 'list', 'Admin\\MenuController::list');
        $routes->match(['options', 'get'], 'get', 'Admin\\MenuController::get');
        $routes->match(['options', 'post'], 'sort', 'Admin\\MenuController::sort');
        $routes->match(['options', 'post'], 'create', 'Admin\\MenuController::create');
        $routes->match(['options', 'post'], 'update', 'Admin\\MenuController::update');
        $routes->match(['options', 'post'], 'update-status', 'Admin\\MenuController::updateStatus');
        $routes->match(['options', 'post'], 'delete', 'Admin\\MenuController::delete');
        

        $routes->group('group', static function($routes) {
            $routes->match(['options', 'post'], 'create', 'Admin\\MenuController::groupCreate');
            $routes->match(['options', 'post'], 'update', 'Admin\\MenuController::groupUpdate');
            $routes->match(['options', 'post'], 'update-status', 'Admin\\MenuController::groupUpdateStatus');
            $routes->match(['options', 'post'], 'delete', 'Admin\\MenuController::groupDelete');
        });
    });

    // fallback SPA routes
    $routes->get('(:any)', 'Admin\\PublicController::singlePageApplication', ['as' => 'panel.fallback']);
});

// dynamic image routes
$routes->group('assets', static function($routes) {
    $routes->get('images/(:any)', 'Assets\\ImageController::serve/$1');
});