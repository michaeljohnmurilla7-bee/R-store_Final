<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Auth::index');
$routes->get('/login', 'Auth::index');
$routes->post('/auth', 'Auth::auth');
$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/logout', 'Auth::logout');


// Products routes
$routes->get('/products', 'Products::index');
$routes->post('products/save', 'Products::save');
$routes->get('products/edit/(:segment)', 'Products::edit/$1');
$routes->post('products/update', 'Products::update');
$routes->delete('products/delete/(:num)', 'Products::delete/$1');
$routes->post('products/fetchRecords', 'Products::fetchRecords');
$routes->get('products/getCount', 'Products::getCount');

// User Acounts routes
$routes->get('/users', 'Users::index');
$routes->post('users/save', 'Users::save');
$routes->get('users/edit/(:segment)', 'Users::edit/$1');
$routes->post('users/update', 'Users::update');
$routes->delete('users/delete/(:num)', 'Users::delete/$1');
$routes->post('users/fetchRecords', 'Users::fetchRecords');

// Logs routes for admin
$routes->get('/log', 'Logs::log');

// Products resource
$routes->get('products', 'Products::index');
$routes->get('products/getCount', 'Products::getCount');
$routes->get('products/create', 'Products::create');
$routes->post('products', 'Products::store');
$routes->get('products/(:num)/edit', 'Products::edit/$1');
$routes->post('products/(:num)', 'Products::update/$1');
$routes->delete('products/(:num)', 'Products::delete/$1');
$routes->post('products/(:num)/restock', 'Products::restock/$1');
$routes->get('products/(:num)/restock', 'Products::restock/$1'); // for modal fetch

// For DataTable AJAX if needed
$routes->get('products/json', 'Products::jsonList');

$routes->get('categories', 'Categories::index');
$routes->get('categories/create', 'Categories::create');
$routes->post('categories', 'Categories::store');
$routes->get('categories/(:num)/edit', 'Categories::edit/$1');
$routes->post('categories/(:num)', 'Categories::update/$1');
$routes->get('categories/delete/(:num)', 'Categories::delete/$1');