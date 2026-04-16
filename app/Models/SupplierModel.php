<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table = 'suppliers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $allowedFields = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'is_active'
    ];

    // Validation rules
    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[150]',
        'email' => 'permit_empty|valid_email|max_length[150]',
        'phone' => 'permit_empty|max_length[30]',
        'contact_person' => 'permit_empty|max_length[100]',
        'is_active' => 'permit_empty|integer|in_list[0,1]'
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;

    // Get active suppliers for dropdown
    public function getActiveSuppliers()
    {
        return $this->where('is_active', 1)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    // Search suppliers
    public function searchSuppliers($keyword)
    {
        return $this->groupStart()
                    ->like('name', $keyword)
                    ->orLike('contact_person', $keyword)
                    ->orLike('email', $keyword)
                    ->orLike('phone', $keyword)
                    ->groupEnd()
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    // Get supplier with product count
    public function getSupplierWithProductCount($id)
    {
        $productModel = new ProductsModel();
        $supplier = $this->find($id);
        
        if ($supplier) {
            $supplier['product_count'] = $productModel->where('supplier_id', $id)->countAllResults();
        }
        
        return $supplier;
    }
}