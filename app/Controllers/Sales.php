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
    protected $session;
    protected $validation;
    
    public function __construct()
    {
        $this->salesModel = new SalesModel();
        $this->saleItemsModel = new SaleItemsModel();
        $this->customerModel = new CustomersModel();
        $this->productModel = new ProductsModel();
        $this->userModel = new UserModel();
        $this->session = \Config\Services::session();
        $this->validation = \Config\Services::validation();
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
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $search = $request->getPost('search')['value'];
    
        $db = \Config\Database::connect();
        $builder = $db->table('sales');

        // COMMENT OUT OR REMOVE the status filter section
        /*
       if ($request->getPost('order_status')) {
        $builder->where('status', $request->getPost('order_status'));  // ← This line causes error
        }
       */
        // Get request variables from DataTable
        $draw = $this->request->getVar('draw') ?? 1;
        $start = $this->request->getVar('start') ?? 0;
        $length = $this->request->getVar('length') ?? 10;
        $search = $this->request->getVar('search')['value'] ?? null;
        
        // Get order column and direction
        $orderColumn = $this->request->getVar('order')[0]['column'] ?? 0;
        $orderDir = $this->request->getVar('order')[0]['dir'] ?? 'DESC';
        
        // Map column indexes to database fields
        $columns = [
            0 => 'id',
            1 => 'invoice_number',
            2 => 'customer_name',
            3 => 'sale_date',
            4 => 'total_amount',
            5 => 'amount_paid',
            6 => 'due_amount',
            7 => 'payment_status',
            8 => 'status',
            9 => 'created_at'
        ];
        
        $orderBy = $columns[$orderColumn] ?? 'id';
        
        // Build query
        $builder = $this->salesModel->builder();
        $builder->select('sales.*, customers.name as customer_name, customers.phone as customer_phone');
        $builder->join('customers', 'customers.id = sales.customer_id', 'left');
        
        // Apply search
        if ($search) {
            $builder->groupStart()
                ->like('sales.invoice_number', $search)
                ->orLike('customers.name', $search)
                ->orLike('customers.phone', $search)
                ->orLike('sales.payment_status', $search)
                ->orLike('sales.status', $search)
                ->groupEnd();
        }
        
        // Get total records count
        $totalRecords = $builder->countAllResults(false);
        
        // Get filtered records count
        $filteredRecords = $totalRecords;
        
        // Apply ordering and limits
        $builder->orderBy($orderBy, $orderDir)
            ->limit($length, $start);
        
        $sales = $builder->get()->getResult();
        
        // Prepare data for DataTable
        $data = [];
        foreach ($sales as $sale) {
            $data[] = [
                'id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'customer_name' => $sale->customer_name ?? 'Walk-in Customer',
                'customer_phone' => $sale->customer_phone ?? '-',
                'sale_date' => date('Y-m-d H:i:s', strtotime($sale->sale_date)),
                'total_amount' => number_format($sale->total_amount, 2),
                'discount' => number_format($sale->discount ?? 0, 2),
                'amount_paid' => number_format($sale->amount_paid, 2),
                'due_amount' => number_format($sale->due_amount ?? 0, 2),
                'payment_status' => $this->getPaymentStatusBadge($sale->payment_status),
                'status' => $this->getOrderStatusBadge($sale->status),
                'created_at' => date('Y-m-d H:i:s', strtotime($sale->created_at)),
                'action' => $this->generateActionButtons($sale->id)
            ];
        }
        
        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
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
            $items = $this->saleItemsModel->getSaleItemsWithProducts($id);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $sale,
                'items' => $items
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Sale not found'
        ])->setStatusCode(404);
    }
    
    /**
     * Get today's sales
     */
    public function getTodaySales()
    {
        $sales = $this->salesModel->where('DATE(sale_date)', date('Y-m-d'))
            ->orderBy('sale_date', 'DESC')
            ->findAll();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $sales,
            'count' => count($sales)
        ]);
    }
    
    /**
     * Get sales by date range
     */
    public function getSalesByDateRange()
    {
        $startDate = $this->request->getVar('start_date');
        $endDate = $this->request->getVar('end_date');
        
        if (!$startDate || !$endDate) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Start date and end date are required'
            ])->setStatusCode(400);
        }
        
        $sales = $this->salesModel->getSalesByDateRange($startDate, $endDate);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $sales,
            'total' => count($sales)
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
            'customers' => $this->customerModel->where('status', 'active')->findAll(),
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
            'customer_id' => 'permit_empty|integer',
            'sale_date' => 'required|valid_date',
            'total_amount' => 'required|decimal|greater_than[0]',
            'discount' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'amount_paid' => 'required|decimal|greater_than_equal_to[0]',
            'status' => 'required|in_list[pending,completed,cancelled]',
            'notes' => 'permit_empty|max_length[500]'
        ];
        
        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $this->validator->getErrors()
                ])->setStatusCode(422);
            }
            
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
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
        
        // Calculate due amount
        $netAmount = $data['total_amount'] - $data['discount'];
        $data['due_amount'] = $netAmount - $data['amount_paid'];
        
        // Set payment status
        if ($data['amount_paid'] <= 0) {
            $data['payment_status'] = 'unpaid';
        } elseif ($data['amount_paid'] >= $netAmount) {
            $data['payment_status'] = 'paid';
        } else {
            $data['payment_status'] = 'partial';
        }
        
        // Save sale
        if ($this->salesModel->insert($data)) {
            $saleId = $this->salesModel->getInsertID();
            
            // Save sale items if provided
            $items = $this->request->getPost('items');
            if ($items && is_array($items)) {
                foreach ($items as $item) {
                    $this->saleItemsModel->addItem(
                        $saleId,
                        $item['product_id'],
                        $item['quantity'],
                        $item['unit_price']
                    );
                }
            }
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Sale created successfully',
                    'sale_id' => $saleId,
                    'invoice_number' => $this->salesModel->find($saleId)->invoice_number
                ]);
            }
            
            $this->session->setFlashdata('success', 'Sale created successfully');
            return redirect()->to('/sales/invoice/' . $saleId);
        }
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create sale'
            ])->setStatusCode(500);
        }
        
        $this->session->setFlashdata('error', 'Failed to create sale');
        return redirect()->back()->withInput();
    }
    
    /**
     * Display edit sale page
     */
    public function edit($id)
    {
        $sale = $this->salesModel->find($id);
        
        if (!$sale) {
            $this->session->setFlashdata('error', 'Sale not found');
            return redirect()->to('/sales');
        }
        
        $data = [
            'title' => 'Edit Sale',
            'page_title' => 'Edit Sale Order',
            'subtitle' => 'Update sale information',
            'sale' => $sale,
            'items' => $this->saleItemsModel->getSaleItemsWithProducts($id),
            'customers' => $this->customerModel->where('status', 'active')->findAll(),
            'products' => $this->productModel->where('status', 'active')->findAll()
        ];
        
        return view('sales/edit', $data);
    }
    
    /**
     * Update sale
     */
    public function update($id)
    {
        // Check if sale exists
        $sale = $this->salesModel->find($id);
        if (!$sale) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Sale not found'
                ])->setStatusCode(404);
            }
            
            $this->session->setFlashdata('error', 'Sale not found');
            return redirect()->to('/sales');
        }
        
        // Validate request
        $rules = [
            'customer_id' => 'permit_empty|integer',
            'sale_date' => 'required|valid_date',
            'total_amount' => 'required|decimal|greater_than[0]',
            'discount' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'amount_paid' => 'required|decimal|greater_than_equal_to[0]',
            'status' => 'required|in_list[pending,completed,cancelled,refunded]',
            'notes' => 'permit_empty|max_length[500]'
        ];
        
        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $this->validator->getErrors()
                ])->setStatusCode(422);
            }
            
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Prepare data
        $data = [
            'customer_id' => $this->request->getPost('customer_id') ?: null,
            'sale_date' => $this->request->getPost('sale_date'),
            'total_amount' => $this->request->getPost('total_amount'),
            'discount' => $this->request->getPost('discount') ?: 0,
            'amount_paid' => $this->request->getPost('amount_paid'),
            'status' => $this->request->getPost('status'),
            'notes' => $this->request->getPost('notes')
        ];
        
        // Calculate due amount
        $netAmount = $data['total_amount'] - $data['discount'];
        $data['due_amount'] = $netAmount - $data['amount_paid'];
        
        // Set payment status
        if ($data['amount_paid'] <= 0) {
            $data['payment_status'] = 'unpaid';
        } elseif ($data['amount_paid'] >= $netAmount) {
            $data['payment_status'] = 'paid';
        } else {
            $data['payment_status'] = 'partial';
        }
        
        // Update sale
        if ($this->salesModel->update($id, $data)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Sale updated successfully'
                ]);
            }
            
            $this->session->setFlashdata('success', 'Sale updated successfully');
            return redirect()->to('/sales');
        }
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update sale'
            ])->setStatusCode(500);
        }
        
        $this->session->setFlashdata('error', 'Failed to update sale');
        return redirect()->back()->withInput();
    }
    
    /**
     * Update payment status
     */
    public function updatePaymentStatus()
    {
        $saleId = $this->request->getPost('sale_id');
        $paymentStatus = $this->request->getPost('payment_status');
        
        if (!$saleId || !in_array($paymentStatus, ['paid', 'partial', 'unpaid'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ])->setStatusCode(400);
        }
        
        if ($this->salesModel->update($saleId, ['payment_status' => $paymentStatus])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Payment status updated successfully'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update payment status'
        ])->setStatusCode(500);
    }
    
    /**
     * Delete sale
     */
    public function delete($id)
    {
        // Check if sale exists
        $sale = $this->salesModel->find($id);
        if (!$sale) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Sale not found'
                ])->setStatusCode(404);
            }
            
            $this->session->setFlashdata('error', 'Sale not found');
            return redirect()->to('/sales');
        }
        
        // Delete sale items first
        $this->saleItemsModel->where('sale_id', $id)->delete();
        
        // Delete sale
        if ($this->salesModel->delete($id)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Sale deleted successfully'
                ]);
            }
            
            $this->session->setFlashdata('success', 'Sale deleted successfully');
            return redirect()->to('/sales');
        }
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete sale'
            ])->setStatusCode(500);
        }
        
        $this->session->setFlashdata('error', 'Failed to delete sale');
        return redirect()->to('/sales');
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
            $this->session->setFlashdata('error', 'Sale not found');
            return redirect()->to('/sales');
        }
        
        $items = $this->saleItemsModel->getSaleItemsWithProducts($id);
        
        $data = [
            'title' => 'Invoice',
            'page_title' => 'Sale Invoice',
            'sale' => $sale,
            'items' => $items,
            'user' => $this->userModel->find($sale->user_id)
        ];
        
        return view('sales/invoice', $data);
    }
    
    /**
     * Generate receipt
     */
    public function receipt($id)
    {
        $sale = $this->salesModel->select('sales.*, customers.name as customer_name')
            ->join('customers', 'customers.id = sales.customer_id', 'left')
            ->where('sales.id', $id)
            ->first();
        
        if (!$sale) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sale not found'
            ])->setStatusCode(404);
        }
        
        $items = $this->saleItemsModel->getSaleItemsWithProducts($id);
        
        return view('sales/receipt', [
            'sale' => $sale,
            'items' => $items
        ]);
    }
    
    /**
     * Get sales summary for dashboard
     */
    public function getSalesSummary()
    {
        $today = $this->salesModel->getSalesSummary(date('Y-m-d'));
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $weekEnd = date('Y-m-d', strtotime('sunday this week'));
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');
        
        $weekSales = $this->salesModel->getSalesByDateRange($weekStart, $weekEnd);
        $monthSales = $this->salesModel->getSalesByDateRange($monthStart, $monthEnd);
        
        $weekTotal = array_sum(array_column($weekSales, 'total_amount'));
        $monthTotal = array_sum(array_column($monthSales, 'total_amount'));
        
        return $this->response->setJSON([
            'success' => true,
            'today' => [
                'total_sales' => $today['total_sales'],
                'total_revenue' => $today['total_revenue'],
                'total_paid' => $today['total_paid']
            ],
            'week' => [
                'total_revenue' => $weekTotal,
                'total_orders' => count($weekSales)
            ],
            'month' => [
                'total_revenue' => $monthTotal,
                'total_orders' => count($monthSales)
            ]
        ]);
    }
    
    /**
     * Get top products
     */
    public function getTopProducts()
    {
        $limit = $this->request->getVar('limit') ?? 10;
        $products = $this->saleItemsModel->getBestSellingProducts($limit);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $products
        ]);
    }
    
    /**
     * Get sales chart data
     */
    public function getSalesChart()
    {
        $days = $this->request->getVar('days') ?? 7;
        $data = $this->salesModel->getDailySalesChart($days);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $data
        ]);
    }
    
    /**
     * Get recent sales
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
        
        $sales = $this->salesModel->getExportData($startDate, $endDate);
        
        if (empty($sales)) {
            $this->session->setFlashdata('error', 'No sales data to export');
            return redirect()->to('/sales');
        }
        
        $filename = 'sales_export_' . date('Y-m-d_His') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Add CSV headers
        fputcsv($output, ['ID', 'Invoice No.', 'Customer', 'Date', 'Total Amount', 'Discount', 'Amount Paid', 'Due Amount', 'Payment Status', 'Status', 'Notes', 'Created At']);
        
        // Add data rows
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
                $sale->notes,
                $sale->created_at
            ]);
        }
        
        fclose($output);
        exit();
    }
    
    /**
     * Generate action buttons for DataTable
     */
    private function generateActionButtons($id)
    {
        $buttons = '
            <div class="btn-group" role="group">
                <a href="' . site_url('sales/invoice/' . $id) . '" class="btn btn-sm btn-info" target="_blank">
                    <i class="fa fa-file-invoice"></i> Invoice
                </a>
                <a href="' . site_url('sales/edit/' . $id) . '" class="btn btn-sm btn-warning">
                    <i class="fa fa-edit"></i> Edit
                </a>
                <button type="button" class="btn btn-sm btn-danger" onclick="deleteSale(' . $id . ')">
                    <i class="fa fa-trash"></i> Delete
                </button>
            </div>
        ';
        
        return $buttons;
    }
    
    /**
     * Get payment status badge
     */
    private function getPaymentStatusBadge($status)
    {
        switch ($status) {
            case 'paid':
                return '<span class="badge badge-success">Paid</span>';
            case 'partial':
                return '<span class="badge badge-warning">Partial</span>';
            case 'unpaid':
                return '<span class="badge badge-danger">Unpaid</span>';
            default:
                return '<span class="badge badge-secondary">' . ucfirst($status) . '</span>';
        }
    }
    
    /**
     * Get order status badge
     */
    private function getOrderStatusBadge($status)
    {
        switch ($status) {
            case 'completed':
                return '<span class="badge badge-success">Completed</span>';
            case 'pending':
                return '<span class="badge badge-warning">Pending</span>';
            case 'cancelled':
                return '<span class="badge badge-danger">Cancelled</span>';
            case 'refunded':
                return '<span class="badge badge-info">Refunded</span>';
            default:
                return '<span class="badge badge-secondary">' . ucfirst($status) . '</span>';
        }
    }
    
    /**
     * Legacy methods for backward compatibility
     */
    public function save()
    {
        return $this->store();
    }
    
    public function fetchRecords()
    {
        return $this->getSalesData();
    }
    
    public function jsonList()
    {
        return $this->getSalesData();
    }
    
    public function processPayment($id)
    {
        // Process payment method
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
    
    public function recordPayment()
    {
        $saleId = $this->request->getPost('sale_id');
        $amount = $this->request->getPost('amount');
        
        return $this->processPayment($saleId);
    }
    
    public function cancelSale($id)
    {
        $sale = $this->salesModel->find($id);
        if (!$sale) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sale not found'
            ]);
        }
        
        // Return stock to products
        $items = $this->saleItemsModel->where('sale_id', $id)->findAll();
        foreach ($items as $item) {
            $product = $this->productModel->find($item->product_id);
            if ($product) {
                $this->productModel->update($item->product_id, [
                    'stock' => $product->stock + $item->quantity
                ]);
            }
        }
        
        if ($this->salesModel->update($id, ['status' => 'cancelled'])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sale cancelled successfully'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to cancel sale'
        ]);
    }
    
    public function generateReport()
    {
        $reportType = $this->request->getPost('report_type');
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        
        // Report generation logic here
        $data = [];
        
        if ($reportType == 'daily') {
            $data = $this->salesModel->getSalesSummary($startDate);
        } elseif ($reportType == 'monthly') {
            $year = date('Y', strtotime($startDate));
            $month = date('m', strtotime($startDate));
            $data = $this->salesModel->getMonthlyReport($year, $month);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $data
        ]);
    }
}