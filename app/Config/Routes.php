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
        $routes->get('export/excel', 'TransactionController::exportExcel');
        $routes->get('export/pdf', 'TransactionController::exportPdf');
        $routes->get('create', 'TransactionController::create');
        $routes->post('/', 'TransactionController::store');
        $routes->get('(:segment)/edit', 'TransactionController::edit/$1');
        $routes->match(['post', 'patch'], '(:segment)', 'TransactionController::update/$1');
        $routes->get('(:segment)/delete', 'TransactionController::delete/$1');
    });

    $routes->group('vaults', function ($routes) {
        $routes->get('/', 'VaultController::index');
        $routes->get('create', 'VaultController::create');
        $routes->post('/', 'VaultController::store');
        $routes->get('(:num)', 'VaultController::show/$1');
        $routes->get('(:num)/edit', 'VaultController::edit/$1');
        $routes->match(['post', 'patch'], '(:num)', 'VaultController::update/$1');
        $routes->get('(:num)/export/pdf', 'VaultController::exportPdf/$1');
        $routes->get('transfer', 'VaultController::showTransferForm');
        $routes->post('transfer', 'VaultController::transfer');
        $routes->get('(:num)/deposit', 'VaultController::showDepositForm/$1');
        $routes->post('(:num)/deposit', 'VaultController::deposit/$1');
        $routes->get('(:num)/withdraw', 'VaultController::showWithdrawForm/$1');
        $routes->post('(:num)/withdraw', 'VaultController::withdraw/$1');
        $routes->get('(:num)/delete', 'VaultController::showDeleteForm/$1');
        $routes->post('(:num)/delete', 'VaultController::destroy/$1');
    });
});
