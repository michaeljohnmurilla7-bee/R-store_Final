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
    $routes->get('delete/(:num)', 'Categories::delete/$1'); 
    // Add these routes for the dropdown endpoints
$routes->get('categories/getSelectList', 'Categories::getSelectList');
$routes->get('suppliers/getSelectList', 'Suppliers::getSelectList');
    // Legacy
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
    $routes->get('sales/getData', 'SalesController::getSalesData');
    
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

$routes->group('sales', function($routes) {
    // Main pages
    $routes->get('/', 'Sales::index');
    $routes->get('getCount', 'Sales::getCount');
    
    // DataTable AJAX endpoints
    $routes->post('getSalesData', 'Sales::getSalesData');
    $routes->get('getSale/(:num)', 'Sales::getSale/$1');
    $routes->get('getTodaySales', 'Sales::getTodaySales');
    $routes->get('getSalesByDateRange', 'Sales::getSalesByDateRange');
    $routes->post('updatePaymentStatus', 'Sales::updatePaymentStatus');
    
    // Create/Store
    $routes->get('create', 'Sales::create');
    $routes->post('store', 'Sales::store');
    
    // Edit/Update
    $routes->get('(:num)/edit', 'Sales::edit/$1');
    $routes->post('update/(:num)', 'Sales::update/$1');
    
    // Delete
    $routes->delete('delete/(:num)', 'Sales::delete/$1');
    
    // Invoice/Receipt
    $routes->get('(:num)/invoice', 'Sales::invoice/$1');
    $routes->get('(:num)/receipt', 'Sales::receipt/$1');
    $routes->post('(:num)/sendReceipt', 'Sales::sendReceipt/$1');
    
    // Payment handling
    $routes->post('(:num)/processPayment', 'Sales::processPayment/$1');
    $routes->get('(:num)/paymentHistory', 'Sales::paymentHistory/$1');
    $routes->post('recordPayment', 'Sales::recordPayment');
    
    // Returns/Refunds
    $routes->get('(:num)/return', 'Sales::return/$1');
    $routes->post('processReturn', 'Sales::processReturn');
    $routes->get('getReturnsData', 'Sales::getReturnsData');
    
    // Reports & Analytics
    $routes->get('dailyReport', 'Sales::dailyReport');
    $routes->get('weeklyReport', 'Sales::weeklyReport');
    $routes->get('monthlyReport', 'Sales::monthlyReport');
    $routes->get('yearlyReport', 'Sales::yearlyReport');
    $routes->post('generateReport', 'Sales::generateReport');
    
    // Dashboard widgets
    $routes->get('getSalesSummary', 'Sales::getSalesSummary');
    $routes->get('getTopProducts', 'Sales::getTopProducts');
    $routes->get('getSalesChart', 'Sales::getSalesChart');
    $routes->get('getRecentSales', 'Sales::getRecentSales');
    
    // Export functionality
    $routes->get('export', 'Sales::export');
    $routes->get('exportInvoice/(:num)', 'Sales::exportInvoice/$1');
    $routes->post('exportSalesReport', 'Sales::exportSalesReport');
    
    // Cart operations (for POS)
    $routes->post('addToCart', 'Sales::addToCart');
    $routes->post('removeFromCart', 'Sales::removeFromCart');
    $routes->post('updateCart', 'Sales::updateCart');
    $routes->get('getCart', 'Sales::getCart');
    $routes->post('clearCart', 'Sales::clearCart');
    $routes->post('applyDiscount', 'Sales::applyDiscount');
    
    // Customer related
    $routes->get('getCustomer/(:num)', 'Sales::getCustomer/$1');
    $routes->post('createCustomer', 'Sales::createCustomer');
    
    // Product search for POS
    $routes->get('searchProducts/(:any)', 'Sales::searchProducts/$1');
    $routes->get('getProductByBarcode/(:any)', 'Sales::getProductByBarcode/$1');
    
    // Legacy routes (keep for backward compatibility)
    $routes->post('save', 'Sales::save');
    $routes->get('edit/(:segment)', 'Sales::edit/$1');
    $routes->post('update', 'Sales::update');
    $routes->delete('delete/(:num)', 'Sales::delete/$1');
    $routes->post('fetchRecords', 'Sales::fetchRecords');
    $routes->get('json', 'Sales::jsonList');
});

// Additional sales routes you might need
$routes->group('sales', function($routes) {
    // Multiple currency support
    $routes->get('getExchangeRates', 'Sales::getExchangeRates');
    $routes->post('setCurrency', 'Sales::setCurrency');
    
    // Tax handling
    $routes->get('getTaxRates', 'Sales::getTaxRates');
    $routes->post('calculateTax', 'Sales::calculateTax');
    
    // Discounts and vouchers
    $routes->post('applyVoucher', 'Sales::applyVoucher');
    $routes->get('validateVoucher/(:any)', 'Sales::validateVoucher/$1');
    
    // Sales status updates
    $routes->post('(:num)/complete', 'Sales::complete/$1');
    $routes->post('(:num)/cancel', 'Sales::cancel/$1');
    $routes->post('(:num)/hold', 'Sales::hold/$1');
    $routes->get('getHeldSales', 'Sales::getHeldSales');
    $routes->post('resumeSale/(:num)', 'Sales::resumeSale/$1');
    
    // Bulk operations
    $routes->post('bulkDelete', 'Sales::bulkDelete');
    $routes->post('bulkUpdateStatus', 'Sales::bulkUpdateStatus');
    
    // Print functionality
    $routes->get('(:num)/print', 'Sales::print/$1');
    $routes->get('printDailySummary', 'Sales::printDailySummary');
    
    // Statistics endpoints
    $routes->get('getStatsByCategory', 'Sales::getStatsByCategory');
    $routes->get('getStatsByPaymentMethod', 'Sales::getStatsByPaymentMethod');
    $routes->get('getBestSellingPeriod', 'Sales::getBestSellingPeriod');
    
    // Sales targets and goals
    $routes->get('getSalesTargets', 'Sales::getSalesTargets');
    $routes->post('setSalesTarget', 'Sales::setSalesTarget');
    $routes->get('getAchievement', 'Sales::getAchievement');

    
});