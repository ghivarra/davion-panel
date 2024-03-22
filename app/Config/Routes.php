<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// dynamic image routes
$routes->group('assets', static function ($routes) {
    $routes->match(['head', 'options', 'get'], 'images/(:any)', 'Assets\\ImageController::serve/$1');
});