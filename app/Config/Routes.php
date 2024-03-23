<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// login routes
$routes->group($_ENV['LOGIN_PAGE'], static function ($routes) {
    $routes->get('/', 'Admin\\LoginController::index', ['as' => 'login']);
});

// dashboard routes
$routes->group($_ENV['PANEL_PAGE'], static function ($routes) {
    $routes->get('/', 'Admin\\DashboardController::index', ['as' => 'panel.dashboard']);
});

// dynamic image routes
$routes->group('assets', static function ($routes) {
    $routes->get('images/(:any)', 'Assets\\ImageController::serve/$1');
});