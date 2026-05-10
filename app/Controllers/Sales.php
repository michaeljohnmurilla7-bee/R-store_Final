<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SalesModel;
use App\Models\SaleItemsModel;
use App\Models\CustomersModel;
use App\Models\ProductsModel;
use App\Models\UserModel;

class Sales extends BaseController
{
    protected $salesModel;
    protected $saleItemsModel;
    protected $customerModel;
    protected $productModel;
    protected $userModel;
    
    public function __construct()
    {
        $this->salesModel = new SalesModel();
        $this->saleItemsModel = new SaleItemsModel();
        $this->customerModel = new CustomersModel();
        $this->productModel = new ProductsModel();
        $this->userModel = new UserModel();
    }
    
    /**
     * Display sales list page
     */
    public function index()
    {
        $data = [
            'title' => 'Sales Management',
            'page_title' => 'Sales Orders'
        ];
        
        return view('sales/index', $data);
    }
    
    /**
     * Get customers for dropdown (AJAX)
     */
    public function getCustomers()
    {
        $customers = $this->customerModel->where('status', 'active')->findAll();
        
        return $this->response->setJSON([
            'success' => true,
            'customers' => $customers ?: []
        ]);
    }
    
    /**
     * Get sales data for DataTable
     */
    public function getSalesData()
    {   
        $request = service('request');
        
        $draw = $request->getPost('draw') ?? 1;
        $start = $request->getPost('start') ?? 0;
        $length = $request->getPost('length') ?? 10;
        $search = $request->getPost('search')['value'] ?? '';
        
        $paymentStatus = $request->getPost('payment_status');
        $orderStatus = $request->getPost('order_status');
        $startDate = $request->getPost('start_date');
        $endDate = $request->getPost('end_date');
        
        $db = \Config\Database::connect();
        $builder = $db->table('sales');
        $builder->select('sales.*, customers.name as customer_name')
                ->join('customers', 'customers.id = sales.customer_id', 'left');
        
        // Apply search
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('sales.invoice_number', $search)
                    ->orLike('customers.name', $search)
                    ->groupEnd();
        }
        
        // Apply filters
        if (!empty($paymentStatus)) {
            $builder->where('sales.payment_status', $paymentStatus);
        }
        
        if (!empty($orderStatus)) {
            $builder->where('sales.status', $orderStatus);
        }
        
        if (!empty($startDate)) {
            $builder->where('DATE(sales.sale_date) >=', $startDate);
        }
        
        if (!empty($endDate)) {
            $builder->where('DATE(sales.sale_date) <=', $endDate);
        }
        
        // Get total count
        $totalRecords = $builder->countAllResults(false);
        
        // Apply ordering
        $orderColumnIndex = $request->getPost('order')[0]['column'] ?? 3;
        $orderDir = $request->getPost('order')[0]['dir'] ?? 'DESC';
        
        $columns = [
            1 => 'sales.invoice_number',
            2 => 'customer_name',
            3 => 'sales.sale_date',
            4 => 'sales.total_amount',
            5 => 'sales.amount_paid',
            6 => 'sales.due_amount',
            7 => 'sales.payment_status',
            8 => 'sales.status'
        ];
        
        $orderBy = $columns[$orderColumnIndex] ?? 'sales.sale_date';
        $builder->orderBy($orderBy, $orderDir);
        $builder->limit((int)$length, (int)$start);
        
        $sales = $builder->get()->getResultArray();
        
        // Prepare data
        $data = [];
        foreach ($sales as $sale) {
            $data[] = [
                'id' => $sale['id'],
                'invoice_number' => $sale['invoice_number'] ?? 'N/A',
                'customer_name' => $sale['customer_name'] ?? 'Walk-in Customer',
                'sale_date' => date('Y-m-d H:i', strtotime($sale['sale_date'])),
                'total_amount' => (float)($sale['total_amount'] ?? 0),
                'amount_paid' => (float)($sale['amount_paid'] ?? 0),
                'due_amount' => (float)($sale['due_amount'] ?? 0),
                'payment_status' => $sale['payment_status'] ?? 'unpaid',
                'status' => $sale['status'] ?? 'pending'
            ];
        }
        
        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => (int)$totalRecords,
            'recordsFiltered' => (int)$totalRecords,
            'data' => $data
        ]);
    }
    
    /**
     * Get single sale data with items (FIXED)
     */
    public function getSale($id)
    {
        $sale = $this->salesModel
            ->select('sales.*, customers.name as customer_name')
            ->join('customers', 'customers.id = sales.customer_id', 'left')
            ->where('sales.id', $id)
            ->first();
        
        if (!$sale) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sale not found'
            ]);
        }
        
        // Get sale items
        $items = $this->saleItemsModel
            ->select('sale_items.*, products.name as product_name')
            ->join('products', 'products.id = sale_items.product_id')
            ->where('sale_items.sale_id', $id)
            ->findAll();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $sale,
            'items' => $items
        ]);
    }
    
    /**
     * Store new sale (FIXED)
     */
    public function store()
    {
        $request = $this->request;
        
        $totalAmount = (float)$request->getPost('total_amount');
        $amountPaid = (float)$request->getPost('amount_paid');
        $discount = (float)$request->getPost('discount') ?: 0;
        $items = $request->getPost('items');
        
        // Validate
        if (empty($items) || !is_array($items)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please add at least one product'
            ]);
        }
        
        if ($totalAmount <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid total amount'
            ]);
        }
        
        $netTotal = $totalAmount - $discount;
        if ($amountPaid > $netTotal) {
            $amountPaid = $netTotal;
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        // Prepare sale data
        $saleData = [
            'user_id' => session()->get('user_id') ?? 1,
            'customer_id' => $request->getPost('customer_id') ?: null,
            'sale_date' => $request->getPost('sale_date') ?? date('Y-m-d H:i:s'),
            'total_amount' => $totalAmount,
            'discount' => $discount,
            'amount_paid' => $amountPaid,
            'status' => $request->getPost('status') ?? 'pending',
            'notes' => $request->getPost('notes')
        ];
        
        // Insert sale
        $saleId = $this->salesModel->insert($saleData);
        
        if (!$saleId) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create sale: ' . implode(', ', $this->salesModel->errors())
            ]);
        }
        
        // Insert items and update stock
        foreach ($items as $item) {
            $quantity = (int)$item['quantity'];
            $price = (float)$item['price'];
            $subtotal = $quantity * $price;
            
            // Insert item
            $itemData = [
                'sale_id' => $saleId,
                'product_id' => $item['product_id'],
                'quantity' => $quantity,
                'unit_price' => $price,
                'subtotal' => $subtotal
            ];
            
            if (!$this->saleItemsModel->insert($itemData)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to save sale items'
                ]);
            }
            
            // Update product stock (using stock_qty)
            $product = $this->productModel->find($item['product_id']);
            if ($product) {
                $newStock = $product['stock_qty'] - $quantity;
                $this->productModel->update($item['product_id'], ['stock_qty' => $newStock]);
            }
        }
        
        $db->transComplete();
        
        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Transaction failed'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Sale created successfully',
            'sale_id' => $saleId
        ]);
    }
    
    /**
     * Update sale (FIXED)
     */
    public function update($id)
    {
        $sale = $this->salesModel->find($id);
        if (!$sale) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sale not found'
            ]);
        }
        
        $data = [
            'customer_id' => $this->request->getPost('customer_id') ?: null,
            'sale_date' => $this->request->getPost('sale_date'),
            'discount' => (float)$this->request->getPost('discount') ?: 0,
            'amount_paid' => (float)$this->request->getPost('amount_paid'),
            'status' => $this->request->getPost('status'),
            'notes' => $this->request->getPost('notes')
        ];
        
        // Recalculate total from items if provided
        $items = $this->request->getPost('items');
        if ($items && is_array($items)) {
            $totalAmount = 0;
            foreach ($items as $item) {
                $totalAmount += (float)$item['quantity'] * (float)$item['price'];
            }
            $data['total_amount'] = $totalAmount;
        }
        
        if ($this->salesModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sale updated successfully'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update sale'
        ]);
    }
    
    /**
     * Delete sale (FIXED - restore stock)
     */
    public function delete($id)
    {
        $sale = $this->salesModel->find($id);
        if (!$sale) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sale not found'
            ]);
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        // Get items to restore stock
        $items = $this->saleItemsModel->where('sale_id', $id)->findAll();
        
        foreach ($items as $item) {
            // Restore product stock
            $product = $this->productModel->find($item->product_id);
            if ($product) {
                $newStock = $product['stock_qty'] + $item->quantity;
                $this->productModel->update($item->product_id, ['stock_qty' => $newStock]);
            }
        }
        
        // Delete sale items
        $this->saleItemsModel->where('sale_id', $id)->delete();
        
        // Delete sale
        $result = $this->salesModel->delete($id);
        
        $db->transComplete();
        
        if ($db->transStatus() === false || !$result) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete sale'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Sale deleted successfully'
        ]);
    }
    
    /**
     * Process payment (FIXED)
     */
    public function processPayment($id)
    {
        $amount = (float)$this->request->getPost('amount');
        
        if (!$amount || $amount <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid payment amount'
            ]);
        }
        
        $sale = $this->salesModel->find($id);
        if (!$sale) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sale not found'
            ]);
        }
        
        $netAmount = $sale->total_amount - ($sale->discount ?? 0);
        $newAmountPaid = $sale->amount_paid + $amount;
        
        if ($newAmountPaid > $netAmount) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Payment amount exceeds due amount'
            ]);
        }
        
        $data = [
            'amount_paid' => $newAmountPaid,
            'due_amount' => $netAmount - $newAmountPaid
        ];
        
        if ($newAmountPaid >= $netAmount) {
            $data['payment_status'] = 'paid';
        } else {
            $data['payment_status'] = 'partial';
        }
        
        if ($this->salesModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Payment recorded successfully'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to record payment'
        ]);
    }
    
    /**
     * Search products for POS (FIXED)
     */
    public function searchProducts($search = null)
    {
        $query = $this->request->getVar('q') ?? $search;
        
        if (!$query || $query === 'all') {
            $products = $this->productModel
                ->where('is_active', 1)
                ->where('stock_qty >', 0)
                ->orderBy('name', 'ASC')
                ->limit(50)
                ->findAll();
        } else {
            $products = $this->productModel
                ->groupStart()
                    ->like('name', $query)
                    ->orLike('sku', $query)
                ->groupEnd()
                ->where('is_active', 1)
                ->where('stock_qty >', 0)
                ->orderBy('name', 'ASC')
                ->limit(50)
                ->findAll();
        }
        
        return $this->response->setJSON([
            'success' => true,
            'products' => $products ?: []
        ]);
    }
    
    /**
     * Show invoice (FIXED)
     */
    public function invoice($id)
    {
        $sale = $this->salesModel
            ->select('sales.*, customers.name as customer_name, customers.phone as customer_phone, customers.email as customer_email, customers.address as customer_address')
            ->join('customers', 'customers.id = sales.customer_id', 'left')
            ->where('sales.id', $id)
            ->first();
        
        if (!$sale) {
            return redirect()->to('/sales')->with('error', 'Sale not found');
        }
        
        $items = $this->saleItemsModel
            ->select('sale_items.*, products.name as product_name')
            ->join('products', 'products.id = sale_items.product_id')
            ->where('sale_items.sale_id', $id)
            ->findAll();
        
        $data = [
            'title' => 'Invoice #' . $sale->invoice_number,
            'sale' => $sale,
            'items' => $items
        ];
        
        return view('sales/invoice', $data);
    }
    
    /**
     * Export sales to CSV
     */
    public function export()
    {
        $startDate = $this->request->getVar('start_date');
        $endDate = $this->request->getVar('end_date');
        
        $builder = $this->salesModel->select('sales.*, customers.name as customer_name')
            ->join('customers', 'customers.id = sales.customer_id', 'left');
        
        if ($startDate) {
            $builder->where('DATE(sales.sale_date) >=', $startDate);
        }
        if ($endDate) {
            $builder->where('DATE(sales.sale_date) <=', $endDate);
        }
        
        $sales = $builder->orderBy('sale_date', 'DESC')->findAll();
        
        $filename = 'sales_export_' . date('Y-m-d_His') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Add BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Headers
        fputcsv($output, ['Invoice No.', 'Customer', 'Date', 'Total Amount', 'Discount', 'Amount Paid', 'Due Amount', 'Payment Status', 'Status']);
        
        // Data
        foreach ($sales as $sale) {
            fputcsv($output, [
                $sale->invoice_number,
                $sale->customer_name ?? 'Walk-in Customer',
                $sale->sale_date,
                number_format($sale->total_amount, 2),
                number_format($sale->discount ?? 0, 2),
                number_format($sale->amount_paid, 2),
                number_format($sale->due_amount ?? 0, 2),
                $sale->payment_status,
                $sale->status
            ]);
        }
        
        fclose($output);
        exit();
    }
}