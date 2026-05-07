<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleItemsModel extends Model
{
    protected $table = 'sale_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = null;
    protected $updatedField = null;
    protected $deletedField = null;

    // Validation
    protected $validationRules = [
        'sale_id' => 'required|integer',
        'product_id' => 'required|integer',
        'quantity' => 'required|integer|greater_than[0]',
        'unit_price' => 'required|decimal|greater_than_equal_to[0]',
        'subtotal' => 'required|decimal|greater_than_equal_to[0]'
    ];

    protected $validationMessages = [
        'sale_id' => [
            'required' => 'Sale ID is required',
            'integer' => 'Sale ID must be a valid number'
        ],
        'product_id' => [
            'required' => 'Product ID is required',
            'integer' => 'Product ID must be a valid number'
        ],
        'quantity' => [
            'required' => 'Quantity is required',
            'integer' => 'Quantity must be a whole number',
            'greater_than' => 'Quantity must be greater than 0'
        ],
        'unit_price' => [
            'required' => 'Unit price is required',
            'decimal' => 'Unit price must be a valid number',
            'greater_than_equal_to' => 'Unit price cannot be negative'
        ],
        'subtotal' => [
            'required' => 'Subtotal is required',
            'decimal' => 'Subtotal must be a valid number',
            'greater_than_equal_to' => 'Subtotal cannot be negative'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['calculateSubtotal', 'updateProductStock'];
    protected $beforeUpdate = ['calculateSubtotal'];
    protected $afterInsert = ['updateSaleTotal'];
    protected $afterUpdate = ['updateSaleTotal'];
    protected $afterDelete = ['updateSaleTotal'];

    /**
     * Calculate subtotal before insert/update
     * Formula: quantity * unit_price = subtotal
     */
    protected function calculateSubtotal(array $data)
    {
        $quantity = $data['data']['quantity'] ?? 0;
        $unitPrice = $data['data']['unit_price'] ?? 0;
        
        // Calculate subtotal: quantity * unit price
        $subtotal = $quantity * $unitPrice;
        $data['data']['subtotal'] = max(0, $subtotal);
        
        return $data;
    }

    /**
     * Update product stock when sale is created
     */
    protected function updateProductStock(array $data)
    {
        $productId = $data['data']['product_id'] ?? null;
        $quantity = $data['data']['quantity'] ?? 0;
        
        if ($productId && $quantity > 0) {
            $productModel = new \App\Models\ProductsModel();
            $product = $productModel->find($productId);
            
            if ($product) {
                // Reduce product stock
                $newStock = $product->stock - $quantity;
                $productModel->update($productId, ['stock' => max(0, $newStock)]);
            }
        }
        
        return $data;
    }

    /**
     * Update sale total amount when items are added/updated/deleted
     */
    protected function updateSaleTotal(array $data)
    {
        $saleId = $data['data']['sale_id'] ?? null;
        
        if ($saleId) {
            $this->recalculateSaleTotal($saleId);
        }
        
        return $data;
    }

    /**
     * Recalculate sale total amount
     */
    public function recalculateSaleTotal($saleId)
    {
        // Calculate total from all sale items
        $result = $this->select('SUM(subtotal) as total')
            ->where('sale_id', $saleId)
            ->get()
            ->getRow();
        
        $totalAmount = $result->total ?? 0;
        
        // Update sale total
        $saleModel = new \App\Models\SalesModel();
        $saleModel->update($saleId, ['total_amount' => $totalAmount]);
        
        return $totalAmount;
    }

    /**
     * Get sale items with product details
     */
    public function getSaleItemsWithProducts($saleId)
    {
        return $this->select('
                sale_items.*,
                products.name as product_name,
                products.sku as product_sku,
                products.stock as current_stock
            ')
            ->join('products', 'products.id = sale_items.product_id')
            ->where('sale_items.sale_id', $saleId)
            ->orderBy('sale_items.id', 'ASC')
            ->findAll();
    }

    /**
     * Get sale items without product details (basic)
     */
    public function getSaleItems($saleId)
    {
        return $this->where('sale_id', $saleId)
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    /**
     * Add item to sale
     */
    public function addItem($saleId, $productId, $quantity, $unitPrice)
    {
        // Check if product already exists in sale
        $existingItem = $this->where('sale_id', $saleId)
            ->where('product_id', $productId)
            ->first();
        
        if ($existingItem) {
            // Update existing item
            $newQuantity = $existingItem->quantity + $quantity;
            return $this->update($existingItem->id, [
                'quantity' => $newQuantity,
                'unit_price' => $unitPrice
            ]);
        } else {
            // Add new item
            return $this->insert([
                'sale_id' => $saleId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice
            ]);
        }
    }

    /**
     * Update item quantity
     */
    public function updateQuantity($itemId, $newQuantity)
    {
        $item = $this->find($itemId);
        if (!$item) {
            return false;
        }
        
        // Calculate stock difference
        $quantityDiff = $newQuantity - $item->quantity;
        
        // Update product stock
        $productModel = new \App\Models\ProductsModel();
        $product = $productModel->find($item->product_id);
        
        if ($product) {
            $newStock = $product->stock - $quantityDiff;
            if ($newStock < 0) {
                return false; // Not enough stock
            }
            $productModel->update($item->product_id, ['stock' => $newStock]);
        }
        
        // Update item quantity
        return $this->update($itemId, ['quantity' => $newQuantity]);
    }

    /**
     * Update unit price
     */
    public function updateUnitPrice($itemId, $newUnitPrice)
    {
        $item = $this->find($itemId);
        if (!$item) {
            return false;
        }
        
        return $this->update($itemId, ['unit_price' => $newUnitPrice]);
    }

    /**
     * Remove item from sale
     */
    public function removeItem($itemId)
    {
        $item = $this->find($itemId);
        if (!$item) {
            return false;
        }
        
        // Return stock to product
        $productModel = new \App\Models\ProductsModel();
        $product = $productModel->find($item->product_id);
        
        if ($product) {
            $newStock = $product->stock + $item->quantity;
            $productModel->update($item->product_id, ['stock' => $newStock]);
        }
        
        // Delete item
        return $this->delete($itemId);
    }

    /**
     * Remove multiple items from sale
     */
    public function removeItems($itemIds)
    {
        if (empty($itemIds)) {
            return false;
        }
        
        foreach ($itemIds as $itemId) {
            $this->removeItem($itemId);
        }
        
        return true;
    }

    /**
     * Clear all items from a sale
     */
    public function clearSaleItems($saleId)
    {
        // Get all items to return stock
        $items = $this->where('sale_id', $saleId)->findAll();
        
        foreach ($items as $item) {
            // Return stock to product
            $productModel = new \App\Models\ProductsModel();
            $product = $productModel->find($item->product_id);
            
            if ($product) {
                $newStock = $product->stock + $item->quantity;
                $productModel->update($item->product_id, ['stock' => $newStock]);
            }
        }
        
        // Delete all items
        return $this->where('sale_id', $saleId)->delete();
    }

    /**
     * Get total items count for a sale
     */
    public function getTotalItems($saleId)
    {
        return $this->where('sale_id', $saleId)
            ->selectSum('quantity')
            ->get()
            ->getRow()
            ->quantity ?? 0;
    }

    /**
     * Get sale items summary
     */
    public function getSaleSummary($saleId)
    {
        $items = $this->getSaleItemsWithProducts($saleId);
        
        $summary = [
            'total_items' => 0,
            'total_quantity' => 0,
            'subtotal' => 0,
            'items' => []
        ];
        
        foreach ($items as $item) {
            $summary['total_items']++;
            $summary['total_quantity'] += $item->quantity;
            $summary['subtotal'] += $item->subtotal;
            
            $summary['items'][] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'product_sku' => $item->product_sku,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'subtotal' => $item->subtotal
            ];
        }
        
        return $summary;
    }

    /**
     * Get best selling products
     */
    public function getBestSellingProducts($limit = 10, $startDate = null, $endDate = null)
    {
        $builder = $this->select('
                products.id,
                products.name as product_name,
                products.sku,
                SUM(sale_items.quantity) as total_quantity_sold,
                SUM(sale_items.subtotal) as total_revenue
            ')
            ->join('products', 'products.id = sale_items.product_id')
            ->join('sales', 'sales.id = sale_items.sale_id')
            ->where('sales.status', 'completed')
            ->groupBy('product_id')
            ->orderBy('total_quantity_sold', 'DESC')
            ->limit($limit);
        
        if ($startDate && $endDate) {
            $builder->where('sales.sale_date >=', $startDate)
                    ->where('sales.sale_date <=', $endDate);
        }
        
        return $builder->findAll();
    }

    /**
     * Get product sales history
     */
    public function getProductSalesHistory($productId, $limit = 10)
    {
        return $this->select('
                sale_items.*,
                sales.invoice_number,
                sales.sale_date,
                sales.customer_name
            ')
            ->join('sales', 'sales.id = sale_items.sale_id')
            ->where('sale_items.product_id', $productId)
            ->where('sales.status', 'completed')
            ->orderBy('sales.sale_date', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get total sales by product
     */
    public function getTotalSalesByProduct($productId)
    {
        $result = $this->select('SUM(quantity) as total_quantity, SUM(subtotal) as total_revenue')
            ->where('product_id', $productId)
            ->get()
            ->getRow();
        
        return [
            'total_quantity' => $result->total_quantity ?? 0,
            'total_revenue' => $result->total_revenue ?? 0
        ];
    }

    /**
     * Get sale items by date range
     */
    public function getSaleItemsByDateRange($startDate, $endDate)
    {
        return $this->select('
                sale_items.*,
                sales.invoice_number,
                sales.sale_date,
                products.name as product_name
            ')
            ->join('sales', 'sales.id = sale_items.sale_id')
            ->join('products', 'products.id = sale_items.product_id')
            ->where('sales.sale_date >=', $startDate)
            ->where('sales.sale_date <=', $endDate)
            ->where('sales.status', 'completed')
            ->orderBy('sales.sale_date', 'DESC')
            ->findAll();
    }

    /**
     * Check if product exists in sale
     */
    public function productExistsInSale($saleId, $productId)
    {
        return $this->where('sale_id', $saleId)
            ->where('product_id', $productId)
            ->countAllResults() > 0;
    }

    /**
     * Get item by sale and product
     */
    public function getItemBySaleAndProduct($saleId, $productId)
    {
        return $this->where('sale_id', $saleId)
            ->where('product_id', $productId)
            ->first();
    }

    /**
     * Bulk insert sale items
     */
    public function bulkInsertItems($saleId, array $items)
    {
        $data = [];
        foreach ($items as $item) {
            $data[] = [
                'sale_id' => $saleId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price']
            ];
        }
        
        if (!empty($data)) {
            return $this->insertBatch($data);
        }
        
        return false;
    }

    /**
     * Duplicate sale items from another sale
     */
    public function duplicateSaleItems($fromSaleId, $toSaleId)
    {
        $items = $this->where('sale_id', $fromSaleId)->findAll();
        
        if (empty($items)) {
            return false;
        }
        
        $data = [];
        foreach ($items as $item) {
            $data[] = [
                'sale_id' => $toSaleId,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price
            ];
        }
        
        return $this->insertBatch($data);
    }
}