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
    
    protected $allowedFields = [
        'invoice_number',
        'user_id',
        'customer_id',
        'sale_date',
        'total_amount',
        'discount',
        'amount_paid',
        'due_amount',
        'payment_status',
        'status',
        'notes'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'user_id' => 'required|integer',
        'customer_id' => 'permit_empty|integer',
        'sale_date' => 'required|valid_date',
        'total_amount' => 'required|decimal|greater_than_equal_to[0]',
        'discount' => 'permit_empty|decimal|greater_than_equal_to[0]',
        'amount_paid' => 'required|decimal|greater_than_equal_to[0]',
        'payment_status' => 'in_list[paid,partial,unpaid]',
        'status' => 'in_list[pending,completed,cancelled]'
    ];

    protected $beforeInsert = ['generateInvoiceNumber', 'calculateDueAmount'];
    protected $beforeUpdate = ['calculateDueAmount'];

    /**
     * Generate invoice number before insert
     */
    protected function generateInvoiceNumber(array $data)
    {
        if (!isset($data['data']['invoice_number'])) {
            $prefix = 'INV';
            $year = date('Y');
            $month = date('m');
            
            // Get last invoice number for this month
            $lastSale = $this->select('invoice_number')
                ->like('invoice_number', $prefix . $year . $month, 'after')
                ->orderBy('id', 'DESC')
                ->first();
            
            if ($lastSale && !empty($lastSale->invoice_number)) {
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
     * Calculate due amount and payment status
     */
    protected function calculateDueAmount(array $data)
    {
        $totalAmount = $data['data']['total_amount'] ?? 0;
        $amountPaid = $data['data']['amount_paid'] ?? 0;
        $discount = $data['data']['discount'] ?? 0;
        
        $netAmount = $totalAmount - $discount;
        $dueAmount = $netAmount - $amountPaid;
        
        // Set payment status
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
        return $this->where('DATE(sale_date)', date('Y-m-d'))->countAllResults();
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
     * Get sales summary for a date
     */
    public function getSalesSummary($date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }
        
        $result = $this->select('
                COUNT(*) as total_sales,
                COALESCE(SUM(total_amount), 0) as total_revenue,
                COALESCE(SUM(amount_paid), 0) as total_paid
            ')
            ->where('DATE(sale_date)', $date)
            ->where('status', 'completed')
            ->get()
            ->getRow();
        
        return [
            'total_sales' => $result->total_sales ?? 0,
            'total_revenue' => $result->total_revenue ?? 0,
            'total_paid' => $result->total_paid ?? 0
        ];
    }
}