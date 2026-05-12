<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductsModel extends Model
{
    protected $table      = 'products';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $allowedFields = [
        'category_id', 
        'supplier_id', 
        'name', 
        'sku',
        'description', 
        'cost_price', 
        'price', 
        'stock_qty',
        'reorder_level', 
        'is_active'
    ];
    
    protected $validationRules = [
        'name'        => 'required|min_length[2]|max_length[200]',
        'sku'         => 'required|is_unique[products.sku]|max_length[50]',
        'category_id' => 'required|is_not_unique[categories.id]',
        'supplier_id' => 'required|is_not_unique[suppliers.id]',
        'price'       => 'required|numeric|greater_than[0]',
        'cost_price'  => 'permit_empty|numeric',
        'stock_qty'   => 'permit_empty|integer',
        'reorder_level' => 'permit_empty|integer',
        'is_active'   => 'permit_empty|integer|in_list[0,1]'    
    ];
    
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    // --------------------------------------------------------------------
    // Get products with category and supplier names (for DataTable)
    // --------------------------------------------------------------------
    public function getProductsWithDetails()
{
    return $this->select('products.*, categories.name as category_name, suppliers.name as supplier_name')
        ->join('categories', 'categories.id = products.category_id', 'left')
        ->join('suppliers', 'suppliers.id = products.supplier_id', 'left')
        // ->where('products.is_active', 1)  // Only show active products
        ->orderBy('products.id', 'DESC')
        ->findAll();
}

    // --------------------------------------------------------------------
    // Get single product with category and supplier names
    // --------------------------------------------------------------------
    public function getWithDetails($id = null)
    {
        $builder = $this->select('products.*, categories.name as category_name, suppliers.name as supplier_name')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->join('suppliers', 'suppliers.id = products.supplier_id', 'left');
        
        if ($id !== null) {
            return $builder->where('products.id', $id)->first();
        }
        
        return $builder->findAll();
    }

    // --------------------------------------------------------------------
    // Get products by supplier
    // --------------------------------------------------------------------
    public function getProductsBySupplier($supplierId)
    {
        return $this->where('supplier_id', $supplierId)
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    // --------------------------------------------------------------------
    // Get products by category
    // --------------------------------------------------------------------
    public function getProductsByCategory($categoryId)
    {
        return $this->where('category_id', $categoryId)
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    // --------------------------------------------------------------------
    // Get low stock products (stock <= reorder level)
    // --------------------------------------------------------------------
    public function getLowStock()
    {
        return $this->select('products.*, categories.name as category_name, suppliers.name as supplier_name')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->join('suppliers', 'suppliers.id = products.supplier_id', 'left')
            ->where('products.stock_qty <=', 'products.reorder_level', false)
            ->where('products.is_active', 1)
            ->orderBy('products.stock_qty', 'ASC')
            ->findAll();
    }

    // Add these methods to your ProductsModel.php

/**
 * Get active products (for dropdowns)
 */
public function getActiveProducts()
{
    return $this->where('is_active', 1)
                ->where('stock_qty >', 0)
                ->orderBy('name', 'ASC')
                ->findAll();
}

/**
 * Search products with stock > 0 (for POS)
 */
public function searchAvailableProducts($keyword)
{
    $builder = $this->select('products.*, categories.name as category_name')
        ->join('categories', 'categories.id = products.category_id', 'left')
        ->groupStart()
            ->like('products.name', $keyword)
            ->orLike('products.sku', $keyword)
        ->groupEnd()
        ->where('products.is_active', 1)
        ->where('products.stock_qty >', 0);
    
    return $builder->limit(20)->findAll();
}

    // --------------------------------------------------------------------
    // Get out of stock products
    // --------------------------------------------------------------------
    public function getOutOfStock()
    {
        return $this->select('products.*, categories.name as category_name, suppliers.name as supplier_name')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->join('suppliers', 'suppliers.id = products.supplier_id', 'left')
            ->where('products.stock_qty', 0)
            ->where('products.is_active', 1)
            ->findAll();
    }

    // --------------------------------------------------------------------
    // Update stock quantity
    // --------------------------------------------------------------------
    public function updateStock($id, $quantity, $type = 'add')
    {
        $product = $this->find($id);
        if (!$product) {
            return false;
        }
        
        if ($type === 'add') {
            $newStock = $product['stock_qty'] + $quantity;
        } else {
            $newStock = $product['stock_qty'] - $quantity;
            if ($newStock < 0) {
                return false; // Cannot have negative stock
            }
        }
        
        return $this->update($id, ['stock_qty' => $newStock]);
    }

    // --------------------------------------------------------------------
    // Check if SKU exists (for validation)
    // --------------------------------------------------------------------
    public function skuExists($sku, $excludeId = null)
    {
        $builder = $this->where('sku', $sku);
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }

    // --------------------------------------------------------------------
    // Search products
    // --------------------------------------------------------------------
    public function searchProducts($keyword)
    {
        return $this->select('products.*, categories.name as category_name, suppliers.name as supplier_name')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->join('suppliers', 'suppliers.id = products.supplier_id', 'left')
            ->groupStart()
                ->like('products.name', $keyword)
                ->orLike('products.sku', $keyword)
                ->orLike('products.description', $keyword)
            ->groupEnd()
            ->where('products.is_active', 1)
            ->orderBy('products.name', 'ASC')
            ->findAll();
    }

    // --------------------------------------------------------------------
    // Get product count by category
    // --------------------------------------------------------------------
    public function getCountByCategory()
    {
        return $this->select('categories.name as category_name, COUNT(products.id) as total')
            ->join('categories', 'categories.id = products.category_id')
            ->groupBy('products.category_id')
            ->orderBy('total', 'DESC')
            ->findAll();
    }

    // --------------------------------------------------------------------
    // Get product count by supplier
    // --------------------------------------------------------------------
    public function getCountBySupplier()
    {
        return $this->select('suppliers.name as supplier_name, COUNT(products.id) as total')
            ->join('suppliers', 'suppliers.id = products.supplier_id')
            ->groupBy('products.supplier_id')
            ->orderBy('total', 'DESC')
            ->findAll();
    }

    // --------------------------------------------------------------------
    // Get total stock value
    // --------------------------------------------------------------------
    public function getTotalStockValue()
    {
        $result = $this->select('SUM(price * stock_qty) as total_value')
            ->where('is_active', 1)
            ->first();
        
        return $result['total_value'] ?? 0;
    }

    // --------------------------------------------------------------------
    // Get total cost value
    // --------------------------------------------------------------------
    public function getTotalCostValue()
    {
        $result = $this->select('SUM(cost_price * stock_qty) as total_cost')
            ->where('is_active', 1)
            ->first();
        
        return $result['total_cost'] ?? 0;
    }

    // --------------------------------------------------------------------
    // Get product statistics
    // --------------------------------------------------------------------
    public function getStatistics()
    {
        $totalProducts = $this->where('is_active', 1)->countAllResults();
        $lowStock = $this->where('stock_qty <= reorder_level', null, false)
                         ->where('is_active', 1)
                         ->countAllResults();
        $outOfStock = $this->where('stock_qty', 0)
                          ->where('is_active', 1)
                          ->countAllResults();
        
        return [
            'total_products' => $totalProducts,
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
            'total_stock_value' => $this->getTotalStockValue(),
            'total_cost_value' => $this->getTotalCostValue()
        ];
    }
}