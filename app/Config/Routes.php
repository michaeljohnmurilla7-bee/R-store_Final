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

// User Accounts routes
$routes->get('/users', 'Users::index');
$routes->post('users/save', 'Users::save');
$routes->get('users/edit/(:segment)', 'Users::edit/$1');
$routes->post('users/update', 'Users::update');
$routes->delete('users/delete/(:num)', 'Users::delete/$1');
$routes->post('users/fetchRecords', 'Users::fetchRecords');

// Logs routes for admin
$routes->get('/log', 'Logs::log');

// ===========================================================================
// Products Routes
// ===========================================================================
$routes->group('products', function($routes) {
    // Main pages
    $routes->get('/', 'Products::index');
    $routes->get('getCount', 'Products::getCount');
    
    // DataTable AJAX endpoints
    $routes->get('getSuppliersData', 'Products::getSuppliersData');
    $routes->get('getProduct/(:num)', 'Products::getProduct/$1');
    $routes->get('getLowStock', 'Products::getLowStock');
    $routes->post('updateStockStatus', 'Products::updateStockStatus');
    
    // Create/Store
    $routes->get('create', 'Products::create');
    $routes->post('store', 'Products::store');
    
    // Edit/Update
    $routes->get('(:num)/edit', 'Products::edit/$1');
    $routes->post('update/(:num)', 'Products::update/$1');
    
    // Delete
    $routes->delete('delete/(:num)', 'Products::delete/$1');
    
    // Restock
    $routes->get('(:num)/restock', 'Products::restock/$1');
    $routes->post('(:num)/restock', 'Products::restock/$1');
    
    // Legacy routes (keep for backward compatibility)
    $routes->post('save', 'Products::save');
    $routes->get('edit/(:segment)', 'Products::edit/$1');
    $routes->post('update', 'Products::update');
    $routes->delete('delete/(:num)', 'Products::delete/$1');
    $routes->post('fetchRecords', 'Products::fetchRecords');
    $routes->get('json', 'Products::jsonList');
});

// ===========================================================================
// Categories Routes
// ===========================================================================
$routes->group('categories', function($routes) {
    $routes->get('/', 'Categories::index');
    $routes->get('create', 'Categories::create');
    $routes->post('store', 'Categories::store');
    $routes->get('getSelectList', 'Categories::getSelectList');
    $routes->get('(:num)/edit', 'Categories::edit/$1');
    $routes->post('update/(:num)', 'Categories::update/$1');
    $routes->delete('delete/(:num)', 'Categories::delete/$1');
    $routes->get('delete/(:num)', 'Categories::delete/$1'); // Legacy
});

// ===========================================================================
// Suppliers Routes
// ===========================================================================
$routes->group('suppliers', function($routes) {
    // Main pages
    $routes->get('/', 'Suppliers::index');
    $routes->get('getCount', 'Suppliers::getCount');
    
    // DataTable AJAX endpoints
    $routes->get('getSuppliers', 'Suppliers::getSuppliers');
    $routes->get('getSupplier/(:num)', 'Suppliers::getSupplier/$1');
    $routes->get('getSelectList', 'Suppliers::getSelectList');
    
    // Export
    $routes->get('export', 'Suppliers::export');
    
    // Create
    $routes->post('create', 'Suppliers::create');
    
    // Update
    $routes->post('update/(:num)', 'Suppliers::update/$1');
    
    // Delete
    $routes->delete('delete/(:num)', 'Suppliers::delete/$1');
    
    // Status toggle
    $routes->post('toggleStatus/(:num)', 'Suppliers::toggleStatus/$1');
});

// Categories Routes
$routes->group('categories', function($routes) {
    // Main pages
    $routes->get('/', 'Categories::index');
    $routes->get('getCount', 'Categories::getCount');
    
    // DataTable AJAX endpoints
    $routes->get('getCategoriesData', 'Categories::getCategoriesData');
    $routes->get('getCategory/(:num)', 'Categories::getCategory/$1');
    $routes->get('getSelectList', 'Categories::getSelectList');
    $routes->get('checkProducts/(:num)', 'Categories::checkProducts/$1');
    $routes->get('getStatistics', 'Categories::getStatistics');
    $routes->get('search', 'Categories::search');
    
    // Export
    $routes->get('export', 'Categories::export');
    
    // Create/Store
    $routes->post('store', 'Categories::store');
    
    // Update
    $routes->post('update/(:num)', 'Categories::update/$1');
    
    // Delete
    $routes->delete('delete/(:num)', 'Categories::delete/$1');
    
    // Status toggle
    $routes->post('toggleStatus/(:num)', 'Categories::toggleStatus/$1');
    
    // Bulk operations
    $routes->post('bulkUpdateStatus', 'Categories::bulkUpdateStatus');
    
    // Legacy routes
    $routes->get('delete/(:num)', 'Categories::delete/$1');
});