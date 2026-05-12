<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleItemsModel extends Model
{
    protected $table = 'sale_items';
    protected $primaryKey = 'id';
    protected $allowedFields = ['sale_id', 'product_id', 'quantity', 'unit_price', 'subtotal'];
    
    protected $returnType = 'array';
}