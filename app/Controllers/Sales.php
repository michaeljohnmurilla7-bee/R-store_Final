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
            'page_title' => 'Sales Orders',
            'subtitle' => 'Manage all sales transactions'
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
            'customers' => $customers
        ]);
    }
    
    /**
     * Get total sales count for dashboard
     */
    public function getCount()
    {
        $total = $this->salesModel->getTotalCount();
        $todayTotal = $this->salesModel->getTodayCount();
        $todayRevenue = $this->salesModel->getTodayTotal();
        
        return $this->response->setJSON([
            'success' => true,
            'total' => $total,
            'today_count' => $todayTotal,
            'today_revenue' => $todayRevenue
        ]);
    }
    
    /**
     * Get sales data for DataTable
     */
    public function getSalesData()
    {   
        $request = service('request');
        
        // DataTable parameters
        $draw = $request->getPost('draw') ?? 1;
        $start = $request->getPost('start') ?? 0;
        $length = $request->getPost('length') ?? 10;
        $search = $request->getPost('search')['value'] ?? '';
        
        // Get filters
        $paymentStatus = $request->getPost('payment_status');
        $orderStatus = $request->getPost('status');
        $startDate = $request->getPost('start_date');
        $endDate = $request->getPost('end_date');
        
        // Build query
        $db = \Config\Database::connect();
        $builder = $db->table('sales');
        $builder->select('sales.*, customers.name as customer_name')
                ->join('customers', 'customers.id = sales.customer_id', 'left');
        
        // Apply search
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('sales.invoice_number', $search)
                    ->orLike('customers.name', $search)
                    ->orLike('sales.payment_status', $search)
                    ->orLike('sales.status', $search)
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
            $builder->where('sales.sale_date >=', $startDate);
        }
        
        if (!empty($endDate)) {
            $builder->where('sales.sale_date <=', $endDate);
        }
        
        // Get total count
        $totalRecords = $builder->countAllResults(false);
        
        // Apply ordering
        $orderColumnIndex = $request->getPost('order')[0]['column'] ?? 0;
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
        
        // Apply limits
        $builder->limit((int)$length, (int)$start);
        
        $sales = $builder->get()->getResultArray();
        
        // Prepare data
        $data = [];
        foreach ($sales as $sale) {
            $data[] = [
                'id' => $sale['id'],
                'invoice_number' => $sale['invoice_number'] ?? 'N/A',
                'customer_name' => $sale['customer_name'] ?? 'Walk-in Customer',
                'sale_date' => date('Y-m-d', strtotime($sale['sale_date'])),
                'total_amount' => (float)$sale['total_amount'],
                'amount_paid' => (float)$sale['amount_paid'],
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
     * Get single sale data
     */
    public function getSale($id)
    {
        $sale = $this->salesModel->select('sales.*, customers.name as customer_name, customers.phone as customer_phone, customers.email as customer_email')
            ->join('customers', 'customers.id = sales.customer_id', 'left')
            ->where('sales.id', $id)
            ->first();
        
        if ($sale) {
            // Get sale items
            $items = $this->saleItemsModel->select('sale_items.*, products.name as product_name')
                ->join('products', 'products.id = sale_items.product_id')
                ->where('sale_items.sale_id', $id)
                ->findAll();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $sale,
                'items' => $items
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Sale not found'
        ]);
    }
    
    /**
 * Cancel a sale
 */
public function cancelSale($id)
{
    $sale = $this->salesModel->find($id);
    if (!$sale) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Sale not found'
        ]);
    }
    
    // Check if already cancelled or completed
    if ($sale->status === 'cancelled') {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Sale is already cancelled'
        ]);
    }
    
    if ($sale->status === 'completed') {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Completed sales cannot be cancelled'
        ]);
    }
    
    // Start transaction
    $db = \Config\Database::connect();
    $db->transStart();
    
    // Return products to stock
    $items = $this->saleItemsModel->where('sale_id', $id)->findAll();
    foreach ($items as $item) {
        $product = $this->productModel->find($item->product_id);
        if ($product) {
            $this->productModel->update($item->product_id, [
                'stock' => $product->stock + $item->quantity
            ]);
        }
    }
    
    // Update sale status
    $result = $this->salesModel->update($id, [
        'status' => 'cancelled',
        'payment_status' => 'unpaid'
    ]);
    
    $db->transComplete();
    
    if ($db->transStatus() === false || !$result) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to cancel sale'
        ]);
    }
    
    return $this->response->setJSON([
        'success' => true,
        'message' => 'Sale cancelled successfully'
    ]);
}

    /**
     * Display create sale page
     */
    public function create()
    {
        $data = [
            'title' => 'New Sale',
            'page_title' => 'Create New Sale',
            'subtitle' => 'Process a new sale transaction',
            'products' => $this->productModel->where('stock >', 0)->where('status', 'active')->findAll()
        ];
        
        return view('sales/create', $data);
    }
    
    /**
     * Store new sale
     */
    public function store()
    {
        // Validate request
        $rules = [
            'sale_date' => 'required',
            'total_amount' => 'required|numeric|greater_than[0]',
            'amount_paid' => 'required|numeric|greater_than_equal_to[0]',
            'status' => 'required|in_list[pending,completed,cancelled]'
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        // Prepare data
        $data = [
            'user_id' => session()->get('user_id') ?? 1,
            'customer_id' => $this->request->getPost('customer_id') ?: null,
            'sale_date' => $this->request->getPost('sale_date'),
            'total_amount' => $this->request->getPost('total_amount'),
            'discount' => $this->request->getPost('discount') ?: 0,
            'amount_paid' => $this->request->getPost('amount_paid'),
            'status' => $this->request->getPost('status'),
            'notes' => $this->request->getPost('notes')
        ];
        
        // Save sale
        if ($this->salesModel->insert($data)) {
            $saleId = $this->salesModel->getInsertID();
            
            // Save sale items
            $items = $this->request->getPost('items');
            if ($items && is_array($items)) {
                foreach ($items as $item) {
                    $this->saleItemsModel->insert([
                        'sale_id' => $saleId,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'subtotal' => $item['quantity'] * $item['price']
                    ]);
                    
                    // Update product stock
                    $product = $this->productModel->find($item['product_id']);
                    if ($product) {
                        $this->productModel->update($item['product_id'], [
                            'stock' => $product->stock - $item['quantity']
                        ]);
                    }
                }
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sale created successfully',
                'sale_id' => $saleId
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to create sale'
        ]);
    }
    
    /**
     * Update sale
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
            'total_amount' => $this->request->getPost('total_amount'),
            'discount' => $this->request->getPost('discount') ?: 0,
            'amount_paid' => $this->request->getPost('amount_paid'),
            'status' => $this->request->getPost('status'),
            'notes' => $this->request->getPost('notes')
        ];
        
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
     * Delete sale
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
        
        // Delete sale items first
        $this->saleItemsModel->where('sale_id', $id)->delete();
        
        // Delete sale
        if ($this->salesModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sale deleted successfully'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to delete sale'
        ]);
    }
    
    /**
     * Display invoice page
     */
    public function invoice($id)
    {
        $sale = $this->salesModel->select('sales.*, customers.name as customer_name, customers.phone as customer_phone, customers.email as customer_email, customers.address as customer_address')
            ->join('customers', 'customers.id = sales.customer_id', 'left')
            ->where('sales.id', $id)
            ->first();
        
        if (!$sale) {
            return redirect()->to('/sales')->with('error', 'Sale not found');
        }
        
        $items = $this->saleItemsModel->select('sale_items.*, products.name as product_name')
            ->join('products', 'products.id = sale_items.product_id')
            ->where('sale_items.sale_id', $id)
            ->findAll();
        
        $data = [
            'title' => 'Invoice',
            'sale' => $sale,
            'items' => $items,
            'user' => $this->userModel->find($sale->user_id)
        ];
        
        return view('sales/invoice', $data);
    }
    
    /**
     * Process payment
     */
    public function processPayment($id)
    {
        $amount = $this->request->getPost('amount');
        
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
        
        $newAmountPaid = $sale->amount_paid + $amount;
        $netAmount = $sale->total_amount - ($sale->discount ?? 0);
        
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
     * Get sales summary for dashboard
     */
    public function getSalesSummary()
    {
        $today = $this->salesModel->getSalesSummary(date('Y-m-d'));
        
        return $this->response->setJSON([
            'success' => true,
            'today' => $today
        ]);
    }
    
    /**
     * Get recent sales for dashboard
     */
    public function getRecentSales()
    {
        $limit = $this->request->getVar('limit') ?? 10;
        $sales = $this->salesModel->getRecentSales($limit);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $sales
        ]);
    }
    
    /**
     * Export sales data
     */
    public function export()
    {
        $startDate = $this->request->getVar('start_date');
        $endDate = $this->request->getVar('end_date');
        
        $db = \Config\Database::connect();
        $builder = $db->table('sales');
        $builder->select('sales.*, customers.name as customer_name')
                ->join('customers', 'customers.id = sales.customer_id', 'left');
        
        if ($startDate) {
            $builder->where('sales.sale_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('sales.sale_date <=', $endDate);
        }
        
        $sales = $builder->orderBy('sale_date', 'DESC')->get()->getResult();
        
        $filename = 'sales_export_' . date('Y-m-d_His') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Headers
        fputcsv($output, ['ID', 'Invoice No.', 'Customer', 'Date', 'Total Amount', 'Discount', 'Amount Paid', 'Due Amount', 'Payment Status', 'Status', 'Notes']);
        
        // Data
        foreach ($sales as $sale) {
            fputcsv($output, [
                $sale->id,
                $sale->invoice_number,
                $sale->customer_name ?? 'Walk-in Customer',
                $sale->sale_date,
                $sale->total_amount,
                $sale->discount ?? 0,
                $sale->amount_paid,
                $sale->due_amount ?? 0,
                $sale->payment_status,
                $sale->status,
                $sale->notes
            ]);
        }
        
        fclose($output);
        exit();
    }
    
    /**
     * Search products for POS
     */
    public function searchProducts($search = null)
    {
        $query = $this->request->getVar('q') ?? $search;
        
        $products = $this->productModel
            ->like('name', $query)
            ->orLike('sku', $query)
            ->where('stock >', 0)
            ->where('status', 'active')
            ->limit(20)
            ->findAll();
        
        return $this->response->setJSON([
            'success' => true,
            'products' => $products
        ]);
    }
}