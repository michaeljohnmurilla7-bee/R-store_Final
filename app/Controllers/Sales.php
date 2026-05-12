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
            'category' => $product['category_name'] ?? 'Uncategorized',
            'supplier' => $product['supplier_name'] ?? 'No Supplier'  // Add supplier name
        ];
    }
    
    return $this->response->setJSON([
        'status' => 'success',
        'data' => $formattedProducts
    ]);
}

    public function getSalesHistoryJson()
{
    $sales = $this->salesModel
        ->select('id, invoice_number, sale_date, total_amount, amount_paid, due_amount, status')
        ->orderBy('id', 'DESC')
        ->limit(20)
        ->findAll();
    
    foreach ($sales as &$sale) {
        $itemCount = $this->saleItemsModel->where('sale_id', $sale['id'])->countAllResults();
        $sale['item_count'] = $itemCount;
        $sale['total'] = $sale['total_amount'];
        $sale['created_at'] = $sale['sale_date'];
        // Make sure order_number uses invoice_number
        $sale['order_number'] = $sale['invoice_number'];  // Add this line
    }
    
    return $this->response->setJSON([
        'status' => 'success',
        'data' => $sales
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
        
        // Validate stock
        foreach ($input['items'] as $item) {
            $product = $this->productModel->find($item['product_id']);
            if (!$product) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => "Product not found"
                ]);
            }
            
            if ($product['stock_qty'] < $item['quantity']) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => "Insufficient stock for {$product['name']}. Available: {$product['stock_qty']}"
                ]);
            }
        }
        
        // Generate invoice number
        $invoiceNumber = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);
        
        $saleData = [
            'invoice_number' => $invoiceNumber,
            'user_id' => session()->get('id') ?? 1,
            'customer_id' => null,
            'sale_date' => date('Y-m-d'),
            'total_amount' => $input['total'],
            'discount' => $input['discount'] ?? 0,
            'amount_paid' => $input['amount_paid'],
            'due_amount' => $input['amount_paid'] - $input['total'],
            'payment_status' => ($input['amount_paid'] - $input['total']) >= 0 ? 'paid' : 'unpaid',
            'status' => 'completed',
            'notes' => $input['notes'] ?? ''
        ];
        
        // Start transaction using $this->db
        $this->db->transStart();
        
        $saleId = $this->salesModel->insert($saleData);
        
        if ($saleId) {
            foreach ($input['items'] as $item) {
                $itemData = [
                    'sale_id' => $saleId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity']
                ];
                
                $this->saleItemsModel->insert($itemData);
                
                // Update stock
                $product = $this->productModel->find($item['product_id']);
                $newStock = $product['stock_qty'] - $item['quantity'];
                $this->productModel->update($item['product_id'], ['stock_qty' => $newStock]);
            }
        }
        
        $this->db->transComplete();
        
        if ($this->db->transStatus() === false) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Transaction failed'
            ]);
        }
        
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Sale completed successfully',
            'order_number' => $invoiceNumber,
            'sale_id' => $saleId
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