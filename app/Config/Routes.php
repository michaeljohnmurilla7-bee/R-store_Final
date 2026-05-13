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

    $routes->post('reactivate/(:num)', 'Products::reactivate/$1');

    
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
    $routes->get('delete/(:num)', 'Categories::delete/$1'); 
    // Add these routes for the dropdown endpoints
$routes->get('categories/getSelectList', 'Categories::getSelectList');
$routes->get('suppliers/getSelectList', 'Suppliers::getSelectList');
    // Legacy
});


// Make sure this is inside your routes file
$routes->group('suppliers', function($routes) {
    // Main page
    $routes->get('/', 'Suppliers::index');
    
    // DataTable - POST for server-side processing
    $routes->post('getSuppliers', 'Suppliers::getSuppliers');  // Use ::

    // Select list for dropdowns (for products, orders, etc.)
    $routes->get('getSelectList', 'Suppliers::getSelectList'); // Use ::

    // CRUD Operations
    $routes->post('addSupplier', 'Suppliers::addSupplier');    // Use ::
    $routes->get('getSupplier/(:num)', 'Suppliers::getSupplier/$1');  // Use ::
    $routes->post('updateSupplier', 'Suppliers::updateSupplier');  // Use ::
    $routes->post('deleteSupplier', 'Suppliers::deleteSupplier');  // Use ::
    
    // CSRF Refresh
    $routes->get('refreshCSRF', 'Suppliers::refreshCSRF');  // Use ::
    
    // Export
    $routes->get('export', 'Suppliers::export');  // Use ::
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

// Customers Routes
$routes->group('customers', function($routes) {
    // Main pages
    $routes->get('/', 'Customers::index');
    $routes->get('getCount', 'Customers::getCount');
    
    // DataTable AJAX endpoints
    $routes->post('getCustomersData', 'Customers::getCustomersData');
    $routes->get('getCustomer/(:num)', 'Customers::getCustomer/$1');
    $routes->get('getRecentCustomers', 'Customers::getRecentCustomers');
    $routes->post('updateCustomerStatus', 'Customers::updateCustomerStatus');
    
    // Create/Store
    $routes->get('create', 'Customers::create');
    $routes->post('store', 'Customers::store');
    
    // Edit/Update
    $routes->get('(:num)/edit', 'Customers::edit/$1');
    $routes->post('update/(:num)', 'Customers::update/$1');
    
    // Delete
    $routes->delete('delete/(:num)', 'Customers::delete/$1');
    
    // Import/Export
    $routes->get('export', 'Customers::export');
    $routes->post('import', 'Customers::import');
    $routes->get('downloadTemplate', 'Customers::downloadTemplate');
    
    // Bulk operations
    $routes->post('bulkDelete', 'Customers::bulkDelete');
    
    // Search
    $routes->get('search/(:any)', 'Customers::search/$1');
    
    // Test route (for debugging)
    $routes->get('testData', 'Customers::testData');
    
    // Legacy routes
    $routes->post('save', 'Customers::save');
    $routes->get('edit/(:segment)', 'Customers::edit/$1');
    $routes->post('update', 'Customers::update');
    $routes->post('fetchRecords', 'Customers::fetchRecords');
    $routes->get('json', 'Customers::jsonList');
});

// // Sales Routes
// $routes->group('sales', ['namespace' => 'App\Controllers'], function($routes) {
//     $routes->get('/', 'Sales::index');
//     $routes->get('getProductsJson', 'Sales::getProductsJson');
//     $routes->get('getSalesHistoryJson', 'Sales::getSalesHistoryJson');
//     $routes->post('saveSale', 'Sales::saveSale');
//     $routes->get('getReceipt/(:num)', 'Sales::getReceipt/$1');
//     $routes->get('salesSummary', 'Sales::salesSummary');
    
// });


// ===========================================================================
// Sales Routes
$routes->group('sales', function($routes) {
    // Main pages
    $routes->get('/', 'Sales::index');
    $routes->get('index', 'Sales::index');
    
    // AJAX endpoints
    $routes->get('getProductsJson', 'Sales::getProductsJson');
    $routes->get('getSalesHistoryJson', 'Sales::getSalesHistoryJson');
    $routes->get('getSale/(:num)', 'Sales::getSale/$1');
    $routes->get('getReceipt/(:num)', 'Sales::getReceipt/$1');
    
    // Create/Store
    $routes->post('saveSale', 'Sales::saveSale');
    
    // ADD STOCK ROUTE - FIXED (no 'sales/' prefix inside group)
    $routes->post('addStock', 'Sales::addStock');  // ✅ This is correct
    
    // Reports
    $routes->get('dailyReport', 'Sales::dailyReport');
    $routes->get('monthlyReport', 'Sales::monthlyReport');
    $routes->get('salesSummary', 'Sales::salesSummary');
    
    // Export
    $routes->get('export/(:any)', 'Sales::export/$1');
    
    // Print receipt
    $routes->get('print/(:num)', 'Sales::printReceipt/$1');
});