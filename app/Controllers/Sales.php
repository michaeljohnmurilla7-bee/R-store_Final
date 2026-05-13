<?php

namespace App\Controllers;

use App\Models\SalesModel;
use App\Models\SaleItemsModel;
use App\Models\ProductsModel;

class Sales extends BaseController
{
    protected $salesModel;
    protected $saleItemsModel;
    protected $productModel;
    protected $db;  // Add this property

    public function __construct()
    {
        $this->salesModel = new SalesModel();
        $this->saleItemsModel = new SaleItemsModel();
        $this->productModel = new ProductsModel();
        $this->db = \Config\Database::connect();  // Initialize database connection
        helper(['form', 'url']);
    }

    public function index()
    {
        $data['title'] = 'Sales & Inventory Point of Sale';
        return view('sales/index', $data);
    }

    public function getProductsJson()
{
    $products = $this->productModel
        ->select('products.*, categories.name as category_name, suppliers.name as supplier_name')
        ->join('categories', 'categories.id = products.category_id', 'left')
        ->join('suppliers', 'suppliers.id = products.supplier_id', 'left')  // Add this join
        ->findAll();
    
    $formattedProducts = [];
    foreach ($products as $product) {
        $formattedProducts[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'sku' => $product['sku'],
            'price' => (float) $product['price'],
            'stock' => (int) ($product['stock_qty'] ?? 0),
            'reorder_level' => (int) ($product['reorder_level'] ?? 10),
            'category' => $product['category_name'] ?? 'Uncategorized',
            'supplier' => $product['supplier_name'] ?? 'No Supplier'  // Add supplier name
        ];
    }
    
    return $this->response->setJSON([
        'status' => 'success',
        'data' => $formattedProducts
    ]);
}

public function addStock()
{
    // Check if it's an AJAX request
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON([
            'status' => 'error', 
            'message' => 'Invalid request'
        ]);
    }

    // Get JSON data from request
    $data = $this->request->getJSON();
    
    // Validate input
    if (!isset($data->product_id) || !isset($data->quantity)) {
        return $this->response->setJSON([
            'status' => 'error', 
            'message' => 'Product ID and quantity are required'
        ]);
    }

    $productId = $data->product_id;
    $quantityToAdd = (int)$data->quantity;

    if ($quantityToAdd <= 0) {
        return $this->response->setJSON([
            'status' => 'error', 
            'message' => 'Quantity must be greater than 0'
        ]);
    }

    // Load the Product model
    $productModel = new ProductsModel();
    
    // Get current product
    $product = $productModel->find($productId);
    
    if (!$product) {
        return $this->response->setJSON([
            'status' => 'error', 
            'message' => 'Product not found'
        ]);
    }

    // Calculate new stock
    $currentStock = (int)$product['stock_qty'];
    $newStock = $currentStock + $quantityToAdd;

    // Update stock in database
    $updated = $productModel->update($productId, [
        'stock_qty' => $newStock
    ]);

    if ($updated) {
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Stock added successfully',
            'new_stock_qty' => $newStock,
            'product_id' => $productId,
            'added_quantity' => $quantityToAdd
        ]);
    } else {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to update stock'
        ]);
    }
}

 public function getAllSuppliers()
{
    $supplierModel = new \App\Models\SupplierModel();
    $suppliers = $supplierModel->findAll();
    return $this->response->setJSON(['status' => 'success', 'data' => $suppliers]);
}

public function getAllCategories()
{
    $categoryModel = new \App\Models\CategoryModel();
    $categories = $categoryModel->findAll();
    return $this->response->setJSON(['status' => 'success', 'data' => $categories]);
}


public function getSalesHistoryJson()
{
    $sales = $this->salesModel
        ->select('id, invoice_number, sale_date, total_amount, amount_paid, due_amount, status')
        ->orderBy('id', 'DESC')
        ->limit(20)
        ->findAll();
    
    $result = [];
    foreach ($sales as $sale) {
        // Get all items for this sale
        $items = $this->saleItemsModel
            ->select('sale_items.*, products.name as product_name')
            ->join('products', 'products.id = sale_items.product_id')
            ->where('sale_id', $sale['id'])
            ->findAll();
        
        $itemCount = count($items);
        
        // Get product names - show multiple with '+ more'
        $productNames = array_column($items, 'product_name');
        if (count($productNames) > 1) {
            $productDisplay = $productNames[0] . ' +' . (count($productNames) - 1) . ' more';
        } else {
            $productDisplay = $productNames[0] ?? 'N/A';
        }
        
        $result[] = [
            'order_number' => $sale['invoice_number'],
            'product_name' => $productDisplay,
            'sale_date' => $sale['sale_date'],
            'item_count' => $itemCount,
            'total_amount' => $sale['total_amount'],
            'status' => $sale['status']
        ];
    }
    
    return $this->response->setJSON([
        'status' => 'success',
        'data' => $result
    ]);
}

    public function saveSale()
{
    // Disable CSRF for this request
    if (method_exists($this->request, 'setCSRFCheck')) {
        $this->request->setCSRFCheck(false);
    }
    
    $input = $this->request->getJSON(true);
    
    if (!$input || empty($input['items'])) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'No items in sale'
        ]);
    }
    
    // Start transaction
    $this->db->transStart();
    
    // Generate invoice number
    $invoiceNumber = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);
    
    $saleData = [
        'invoice_number' => $invoiceNumber,
        'user_id' => session()->get('id') ?? 1,
        'customer_id' => null,
        'sale_date' => date('Y-m-d H:i:s'),
        'total_amount' => $input['total'],
        'discount' => $input['discount'] ?? 0,
        'amount_paid' => $input['amount_paid'],
        'due_amount' => ($input['amount_paid'] - $input['total']) < 0 ? ($input['total'] - $input['amount_paid']) : 0,
        'payment_status' => ($input['amount_paid'] >= $input['total']) ? 'paid' : 'unpaid',
        'status' => 'completed',
        'notes' => $input['notes'] ?? ''
    ];
    
    // Insert sale
    $saleId = $this->salesModel->insert($saleData);
    
    if (!$saleId) {
        $this->db->transRollback();
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to create sale record'
        ]);
    }
    
    // Process each item
    foreach ($input['items'] as $item) {
        // Insert sale item
        $itemData = [
            'sale_id' => $saleId,
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity'],
            'unit_price' => $item['price'],
            'subtotal' => $item['price'] * $item['quantity']
        ];
        
        $this->saleItemsModel->insert($itemData);
        
        // ✅ DEDUCT STOCK ONLY ONCE
        $product = $this->productModel->find($item['product_id']);
        
        // Verify sufficient stock
        if ($product['stock_qty'] < $item['quantity']) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
                'message' => "Insufficient stock for {$product['name']}"
            ]);
        }
        
        // Calculate new stock
        $newStock = $product['stock_qty'] - $item['quantity'];
        
        // Update stock
        $updateResult = $this->productModel->update($item['product_id'], ['stock_qty' => $newStock]);
        
        if (!$updateResult) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
                'message' => "Failed to update stock for {$product['name']}"
            ]);
        }
        
        // Log for debugging
        log_message('debug', "Stock deducted: {$product['name']} from {$product['stock_qty']} to {$newStock}");
    }
    
    // Commit transaction
    $this->db->transComplete();
    
    if ($this->db->transStatus() === false) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Transaction failed'
        ]);
    }
    
    // Get updated stock values for all products in the sale
    $updatedStock = [];
    foreach ($input['items'] as $item) {
        $product = $this->productModel->find($item['product_id']);
        $updatedStock[$item['product_id']] = $product['stock_qty'];
    }
    
    return $this->response->setJSON([
        'status' => 'success',
        'message' => 'Sale completed successfully',
        'order_number' => $invoiceNumber,
        'sale_id' => $saleId,
        'updated_stock' => $updatedStock
    ]);
}

    public function export($type = 'csv')
    {
        $sales = $this->salesModel->findAll();
        
        $filename = 'sales_export_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Invoice #', 'Date', 'Total', 'Discount', 'Amount Paid', 'Due', 'Status']);
        
        foreach ($sales as $sale) {
            fputcsv($output, [
                $sale['invoice_number'],
                $sale['sale_date'],
                $sale['total_amount'],
                $sale['discount'],
                $sale['amount_paid'],
                $sale['due_amount'],
                $sale['status']
            ]);
        }
        
        fclose($output);
        exit();
    }
}