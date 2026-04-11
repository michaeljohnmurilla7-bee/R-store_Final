<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductsModel extends Model
{
    protected $table      = 'products';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'category_id', 'supplier_id', 'name', 'sku',
        'description', 'cost_price', 'price', 'stock_qty',
        'reorder_level', 'is_active'
    ];
    protected $validationRules = [
        'name'        => 'required|min_length[3]',
        'category_id' => 'required|is_not_unique[categories.id]',
        'supplier_id' => 'required|is_not_unique[suppliers.id]',
        'price'       => 'required|numeric|greater_than[0]',
        'cost_price'  => 'permit_empty|numeric',
        'stock_qty'   => 'permit_empty|integer',
    ];

    // Relationships
    public function getWithDetails($id = null)
    {
        $builder = $this->db->table('products');
        $builder->select('products.*, categories.name as category_name, suppliers.name as supplier_name');
        $builder->join('categories', 'categories.id = products.category_id', 'left');
        $builder->join('suppliers', 'suppliers.id = products.supplier_id', 'left');
        
        if ($id !== null) {
            return $builder->where('products.id', $id)->get()->getRowArray();
        }
        
        return $builder->get()->getResultArray();
    }

    // For DataTable server-side processing (optional, we'll use client-side for simplicity)
    public function getProductsWithDetails()
    {
        return $this->getWithDetails();
    }

    // Get low stock products
    public function getLowStock()
    {
        return $this->where('stock_qty <= reorder_level')->findAll();
    }
}