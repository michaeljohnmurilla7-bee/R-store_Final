<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesModel extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'user_id',
        'customer_id',
        'sale_date',
        'total_amount',
        'discount',
        'amount_paid',
        'status',
        'notes',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = null;

    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer',
        'customer_id' => 'permit_empty|integer',
        'sale_date' => 'required|valid_date',
        'total_amount' => 'required|decimal|greater_than_equal_to[0]',
        'discount' => 'permit_empty|decimal|greater_than_equal_to[0]',
        'amount_paid' => 'required|decimal|greater_than_equal_to[0]',
        'status' => 'required|in_list[pending,completed,cancelled,refunded]',
        'notes' => 'permit_empty|max_length[500]'
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required',
            'integer' => 'User ID must be a valid number'
        ],
        'sale_date' => [
            'required' => 'Sale date is required',
            'valid_date' => 'Please provide a valid date'
        ],
        'total_amount' => [
            'required' => 'Total amount is required',
            'decimal' => 'Total amount must be a valid number',
            'greater_than_equal_to' => 'Total amount cannot be negative'
        ],
        'amount_paid' => [
            'required' => 'Amount paid is required',
            'decimal' => 'Amount paid must be a valid number',
            'greater_than_equal_to' => 'Amount paid cannot be negative'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Status must be pending, completed, cancelled, or refunded'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateInvoiceNumber', 'calculateDueAmount'];
    protected $beforeUpdate = ['calculateDueAmount'];
    protected $afterInsert = ['updateCustomerPurchase'];
    protected $afterUpdate = ['updateCustomerPurchase'];

    /**
     * Generate invoice number before insert
     */
    protected function generateInvoiceNumber(array $data)
    {
        if (!isset($data['data']['invoice_number']) || empty($data['data']['invoice_number'])) {
            $prefix = 'INV';
            $year = date('Y');
            $month = date('m');
            
            // Get the last invoice number for this month
            $lastSale = $this->select('invoice_number')
                ->like('invoice_number', $prefix . $year . $month, 'after')
                ->orderBy('id', 'DESC')
                ->first();
            
            if ($lastSale && isset($lastSale->invoice_number)) {
                $lastNumber = (int)substr($lastSale->invoice_number, -4);
                $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '0001';
            }
            
            $data['data']['invoice_number'] = $prefix . $year . $month . $newNumber;
        }
        
        return $data;
    }

    /**
     * Calculate due amount before insert/update
     */
    protected function calculateDueAmount(array $data)
    {
        $totalAmount = $data['data']['total_amount'] ?? 0;
        $amountPaid = $data['data']['amount_paid'] ?? 0;
        $discount = $data['data']['discount'] ?? 0;
        
        // Calculate net amount after discount
        $netAmount = $totalAmount - $discount;
        
        // Calculate due amount
        $dueAmount = $netAmount - $amountPaid;
        
        // Update payment status based on amount paid
        if ($amountPaid <= 0) {
            $data['data']['payment_status'] = 'unpaid';
        } elseif ($amountPaid >= $netAmount) {
            $data['data']['payment_status'] = 'paid';
        } else {
            $data['data']['payment_status'] = 'partial';
        }
        
        $data['data']['due_amount'] = max(0, $dueAmount);
        
        return $data;
    }

    /**
     * Update customer purchase statistics
     */
    protected function updateCustomerPurchase(array $data)
    {
        $customerId = $data['data']['customer_id'] ?? null;
        
        if ($customerId && $customerId > 0) {
            $customerModel = new \App\Models\CustomersModel();
            
            // Get total purchases for this customer
            $totalPurchases = $this->where('customer_id', $customerId)
                ->where('status', 'completed')
                ->selectSum('total_amount')
                ->get()
                ->getRow()
                ->total_amount ?? 0;
            
            // Count total orders
            $totalOrders = $this->where('customer_id', $customerId)
                ->where('status', 'completed')
                ->countAllResults();
            
            // Get last purchase date
            $lastPurchase = $this->where('customer_id', $customerId)
                ->where('status', 'completed')
                ->orderBy('sale_date', 'DESC')
                ->first();
            
            $customerModel->update($customerId, [
                'total_spent' => $totalPurchases,
                'total_orders' => $totalOrders,
                'last_order_date' => $lastPurchase ? $lastPurchase->sale_date : null
            ]);
        }
        
        return $data;
    }

    /**
     * Get sales with pagination for DataTable
     */
    public function getDatatableData($search = null, $orderBy = 'id', $orderDir = 'DESC', $start = 0, $length = 10)
    {
        $builder = $this->builder();
        $builder->select('sales.*, users.username as user_name, customers.name as customer_name');
        $builder->join('users', 'users.id = sales.user_id', 'left');
        $builder->join('customers', 'customers.id = sales.customer_id', 'left');
        
        // Apply search
        if ($search) {
            $builder->groupStart()
                ->like('sales.invoice_number', $search)
                ->orLike('customers.name', $search)
                ->orLike('customers.phone', $search)
                ->orLike('sales.status', $search)
                ->orLike('sales.payment_status', $search)
                ->groupEnd();
        }
        
        // Get total count
        $totalCount = $builder->countAllResults(false);
        
        // Apply ordering and limits
        $builder->orderBy($orderBy, $orderDir)
            ->limit($length, $start);
        
        $data = $builder->get()->getResult();
        
        return [
            'data' => $data,
            'totalCount' => $totalCount
        ];
    }

    /**
     * Get total sales count
     */
    public function getTotalCount()
    {
        return $this->countAllResults();
    }

    /**
     * Get today's sales count
     */
    public function getTodayCount()
    {
        return $this->where('DATE(sale_date)', date('Y-m-d'))
            ->countAllResults();
    }

    /**
     * Get today's sales total
     */
    public function getTodayTotal()
    {
        $result = $this->select('SUM(total_amount) as total')
            ->where('DATE(sale_date)', date('Y-m-d'))
            ->where('status', 'completed')
            ->get()
            ->getRow();
        
        return $result->total ?? 0;
    }

    /**
     * Get sales by date range
     */
    public function getSalesByDateRange($startDate, $endDate)
    {
        return $this->select('sales.*, customers.name as customer_name')
            ->join('customers', 'customers.id = sales.customer_id', 'left')
            ->where('sale_date >=', $startDate)
            ->where('sale_date <=', $endDate)
            ->orderBy('sale_date', 'DESC')
            ->findAll();
    }

    /**
     * Get sales summary by date
     */
    public function getSalesSummary($date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }
        
        $result = $this->select('
                COUNT(*) as total_sales,
                SUM(total_amount) as total_revenue,
                SUM(discount) as total_discount,
                SUM(amount_paid) as total_paid,
                AVG(total_amount) as average_sale
            ')
            ->where('DATE(sale_date)', $date)
            ->where('status', 'completed')
            ->get()
            ->getRow();
        
        return [
            'total_sales' => $result->total_sales ?? 0,
            'total_revenue' => $result->total_revenue ?? 0,
            'total_discount' => $result->total_discount ?? 0,
            'total_paid' => $result->total_paid ?? 0,
            'average_sale' => $result->average_sale ?? 0
        ];
    }

    /**
     * Get monthly sales report
     */
    public function getMonthlyReport($year = null, $month = null)
    {
        if (!$year) $year = date('Y');
        if (!$month) $month = date('m');
        
        return $this->select('
                DATE(sale_date) as sale_date,
                COUNT(*) as total_sales,
                SUM(total_amount) as daily_revenue,
                SUM(discount) as daily_discount,
                SUM(amount_paid) as daily_collected
            ')
            ->where('YEAR(sale_date)', $year)
            ->where('MONTH(sale_date)', $month)
            ->where('status', 'completed')
            ->groupBy('DATE(sale_date)')
            ->orderBy('sale_date', 'ASC')
            ->findAll();
    }

    /**
     * Get sales by status
     */
    public function getSalesByStatus()
    {
        return $this->select('status, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('status')
            ->findAll();
    }

    /**
     * Get recent sales
     */
    public function getRecentSales($limit = 10)
    {
        return $this->select('sales.*, customers.name as customer_name')
            ->join('customers', 'customers.id = sales.customer_id', 'left')
            ->orderBy('sale_date', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get sales by user
     */
    public function getSalesByUser($userId)
    {
        return $this->where('user_id', $userId)
            ->orderBy('sale_date', 'DESC')
            ->findAll();
    }

    /**
     * Get user sales performance
     */
    public function getUserPerformance($startDate = null, $endDate = null)
    {
        $builder = $this->select('
                user_id,
                COUNT(*) as total_sales,
                SUM(total_amount) as total_revenue,
                AVG(total_amount) as average_sale
            ')
            ->where('status', 'completed');
        
        if ($startDate && $endDate) {
            $builder->where('sale_date >=', $startDate)
                    ->where('sale_date <=', $endDate);
        }
        
        return $builder->groupBy('user_id')
            ->orderBy('total_revenue', 'DESC')
            ->findAll();
    }

    /**
     * Update sale status
     */
    public function updateStatus($saleId, $status)
    {
        if (!in_array($status, ['pending', 'completed', 'cancelled', 'refunded'])) {
            return false;
        }
        
        return $this->update($saleId, ['status' => $status]);
    }

    /**
     * Get total revenue for date range
     */
    public function getTotalRevenue($startDate = null, $endDate = null)
    {
        $builder = $this->select('SUM(total_amount) as total')
            ->where('status', 'completed');
        
        if ($startDate) {
            $builder->where('sale_date >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('sale_date <=', $endDate);
        }
        
        $result = $builder->get()->getRow();
        
        return $result->total ?? 0;
    }

    /**
     * Get daily sales chart data
     */
    public function getDailySalesChart($days = 7)
    {
        $data = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $sales = $this->select('SUM(total_amount) as total')
                ->where('DATE(sale_date)', $date)
                ->where('status', 'completed')
                ->get()
                ->getRow();
            
            $data[] = [
                'date' => $date,
                'total' => $sales->total ?? 0
            ];
        }
        
        return $data;
    }

    /**
     * Get top customers by purchase amount
     */
    public function getTopCustomers($limit = 10)
    {
        return $this->select('
                customer_id,
                customers.name as customer_name,
                COUNT(*) as total_orders,
                SUM(total_amount) as total_spent
            ')
            ->join('customers', 'customers.id = sales.customer_id')
            ->where('customer_id !=', 0)
            ->where('customer_id IS NOT NULL')
            ->where('status', 'completed')
            ->groupBy('customer_id')
            ->orderBy('total_spent', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Search sales
     */
    public function searchSales($keyword)
    {
        return $this->select('sales.*, customers.name as customer_name')
            ->join('customers', 'customers.id = sales.customer_id', 'left')
            ->groupStart()
                ->like('sales.invoice_number', $keyword)
                ->orLike('customers.name', $keyword)
                ->orLike('customers.phone', $keyword)
                ->orLike('sales.payment_status', $keyword)
            ->groupEnd()
            ->orderBy('sale_date', 'DESC')
            ->findAll();
    }

    /**
     * Get pending payments
     */
    public function getPendingPayments()
    {
        return $this->select('sales.*, customers.name as customer_name, customers.phone as customer_phone')
            ->join('customers', 'customers.id = sales.customer_id', 'left')
            ->where('payment_status', 'partial')
            ->orWhere('payment_status', 'unpaid')
            ->where('status', 'completed')
            ->orderBy('sale_date', 'ASC')
            ->findAll();
    }

    /**
     * Export sales data
     */
    public function getExportData($startDate = null, $endDate = null)
    {
        $builder = $this->select('
                sales.id,
                sales.invoice_number,
                customers.name as customer_name,
                customers.phone as customer_phone,
                sales.sale_date,
                sales.total_amount,
                sales.discount,
                sales.amount_paid,
                sales.due_amount,
                sales.payment_status,
                sales.status,
                sales.notes,
                sales.created_at
            ')
            ->join('customers', 'customers.id = sales.customer_id', 'left');
        
        if ($startDate && $endDate) {
            $builder->where('sale_date >=', $startDate)
                    ->where('sale_date <=', $endDate);
        }
        
        return $builder->orderBy('sale_date', 'DESC')->findAll();
    }

    /**
     * Get sale items (for sale details)
     */
    public function getSaleItems($saleId)
    {
        $saleItemModel = new \App\Models\SaleItemsModel();
        return $saleItemModel->where('sale_id', $saleId)
            ->select('sale_items.*, products.name as product_name')
            ->join('products', 'products.id = sale_items.product_id')
            ->findAll();
    }

    /**
     * Calculate due amount for a sale
     */
    public function getDueAmount($saleId)
    {
        $sale = $this->find($saleId);
        if ($sale) {
            $netAmount = $sale->total_amount - ($sale->discount ?? 0);
            return max(0, $netAmount - $sale->amount_paid);
        }
        return 0;
    }

    /**
     * Record payment for a sale
     */
    public function recordPayment($saleId, $paymentAmount)
    {
        $sale = $this->find($saleId);
        if (!$sale) {
            return false;
        }
        
        $newAmountPaid = $sale->amount_paid + $paymentAmount;
        $netAmount = $sale->total_amount - ($sale->discount ?? 0);
        
        if ($newAmountPaid > $netAmount) {
            return false;
        }
        
        return $this->update($saleId, [
            'amount_paid' => $newAmountPaid,
            'due_amount' => $netAmount - $newAmountPaid,
            'payment_status' => $newAmountPaid >= $netAmount ? 'paid' : 'partial'
        ]);
    }
}