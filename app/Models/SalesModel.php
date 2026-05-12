<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesModel extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
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
    
    protected $returnType = 'array';
}