<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Rotas de autenticação
$routes->group('auth', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('login', 'AuthController::login');
    $routes->post('login', 'AuthController::doLogin');
    $routes->get('signup', 'AuthController::signup');
    $routes->post('signup', 'AuthController::doSignup');
    $routes->get('logout', 'AuthController::logout');
});

// Rotas protegidas (requerem autenticação)
$routes->group('', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('dashboard', 'DashboardController::index');
    
    $routes->group('transactions', function ($routes) {
        $routes->get('/', 'TransactionController::index');
        $routes->get('create', 'TransactionController::create');
        $routes->post('/', 'TransactionController::store');
        $routes->get('(:segment)/edit', 'TransactionController::edit/$1');
        $routes->match(['post', 'patch'], '(:segment)', 'TransactionController::update/$1');
        $routes->get('(:segment)/delete', 'TransactionController::delete/$1');
    });
});
