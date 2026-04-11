<?php

namespace App\Models;

use CodeIgniter\Model;

class StockAdjustmentModel extends Model
{
    protected $table      = 'stock_adjustments';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // no updated_at in table
    protected $allowedFields = ['product_id', 'user_id', 'type', 'quantity', 'reason'];
}