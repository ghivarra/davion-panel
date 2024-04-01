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

    // Website routes
    $routes->group('website', static function($routes) {
        $routes->match(['options', 'get'], 'data', 'Admin\\WebsiteController::data');
        $routes->match(['options', 'post'], 'main-form-update', 'Admin\\WebsiteController::mainFormUpdate');
        $routes->match(['options', 'post'], 'logo-update', 'Admin\\WebsiteController::logoUpdate');
    });

    // fallback SPA routes
    $routes->get('(:any)', 'Admin\\PublicController::singlePageApplication', ['as' => 'panel.fallback']);
});

// dynamic image routes
$routes->group('assets', static function($routes) {
    $routes->get('images/(:any)', 'Assets\\ImageController::serve/$1');
});