<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Login::index');
$routes->get('/login', 'Login::index');
$routes->post('/login', 'Login::auth');
$routes->get('/logout', 'Login::logout');

//$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'auth']);
$routes->get('/cari-kontainer', 'CariKontainer::index', ['filter' => 'auth']);
$routes->get('/cari-kontainer/search', 'CariKontainer::search', ['filter' => 'auth']);

// $routes->get('/cari-kontainer', 'Kontainer::index', ['filter' => 'auth']);
// $routes->post('/cari-kontainer/result', 'Kontainer::search', ['filter' => 'auth']);
$routes->post('/api/upload', 'Api\UploadData::upload');

$routes->get('/test/hash', 'Test::hash');
