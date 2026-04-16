<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $allowedFields = [
        'name',
        'description'
        // Remove 'is_active' from allowed fields if not in database
    ];

    // Remove validation rules for is_active
    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[100]|is_unique[categories.name]',
        'description' => 'permit_empty|max_length[500]'
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;

    // Override findAll to add is_active virtual field
    public function findAll(?int $limit = null, int $offset = 0)
    {
        $categories = parent::findAll($limit, $offset);
        
        // Add virtual is_active field (default to 1/Active)
        foreach ($categories as &$category) {
            $category['is_active'] = 1; // All categories are active by default
        }
        
        return $categories;
    }

    // Override find to add is_active virtual field
    public function find($id = null)
    {
        $category = parent::find($id);
        
        if ($category) {
            $category['is_active'] = 1; // All categories are active by default
        }
        
        return $category;
    }

    // Get active categories for dropdown
    public function getActiveCategories()
    {
        return $this->orderBy('name', 'ASC')->findAll();
    }

    // Get category with product count
    public function getCategoryWithProductCount($id)
    {
        $productModel = new ProductsModel();
        $category = $this->find($id);
        
        if ($category) {
            $category['product_count'] = $productModel->where('category_id', $id)->countAllResults();
            $category['is_active'] = 1;
        }
        
        return $category;
    }

    // Get all categories with product counts
    public function getAllWithProductCounts()
    {
        $productModel = new ProductsModel();
        $categories = $this->findAll();
        
        foreach ($categories as &$category) {
            $category['product_count'] = $productModel->where('category_id', $category['id'])->countAllResults();
            $category['is_active'] = 1;
        }
        
        return $categories;
    }

    // Search categories
    public function searchCategories($keyword)
    {
        return $this->groupStart()
                    ->like('name', $keyword)
                    ->orLike('description', $keyword)
                    ->groupEnd()
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }
}