<?php

namespace App\Controllers;

use App\Models\ProductsModel;
use App\Models\CategoryModel;
use App\Models\SupplierModel;
use App\Models\StockAdjustmentModel;

class Products extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $supplierModel;
    protected $stockAdjustmentModel;

    public function __construct()
    {
        $this->productModel = new ProductsModel();
        $this->categoryModel = new CategoryModel();
        $this->supplierModel = new SupplierModel();
        $this->stockAdjustmentModel = new StockAdjustmentModel();
        helper(['form', 'url']);
    }

    // --------------------------------------------------------------------
    // List all products (main page)
    // --------------------------------------------------------------------
    public function index()
    {
        $data['title'] = 'Products';
        $data['products'] = $this->productModel->getProductsWithDetails();
        $data['categories'] = $this->categoryModel->findAll();
        $data['suppliers'] = $this->supplierModel->findAll();
        
        return view('products/index', $data);
    }

    // --------------------------------------------------------------------
    // AJAX endpoint for DataTable - Get all products with supplier and category names
    // --------------------------------------------------------------------
    public function getSuppliersData()
    {
        $products = $this->productModel->getProductsWithDetails();
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $products
        ]);
    }

    // --------------------------------------------------------------------
    // AJAX endpoint to get single product with details
    // --------------------------------------------------------------------
    public function getProduct($id)
    {
        $product = $this->productModel->getWithDetails($id);
        
        if ($product) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $product
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Product not found'
            ]);
        }
    }

    // --------------------------------------------------------------------
    // AJAX endpoint for product count (used in dashboard)
    // --------------------------------------------------------------------
    public function getCount()
    {
        $count = $this->productModel->countAll();
        return $this->response->setJSON(['count' => $count]);
    }

    // --------------------------------------------------------------------
    // Get low stock products (for dashboard alerts)
    // --------------------------------------------------------------------
    public function getLowStock()
    {
        $lowStockProducts = $this->productModel->getLowStock();
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $lowStockProducts
        ]);
    }

    // --------------------------------------------------------------------
    // Get product statistics
    // --------------------------------------------------------------------
    public function getStatistics()
    {
        $statistics = $this->productModel->getStatistics();
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $statistics
        ]);
    }

    // --------------------------------------------------------------------
    // Show create form (modal or page) – we'll return JSON for modal
    // --------------------------------------------------------------------
    public function create()
    {
        if ($this->request->isAJAX()) {
            $data = [
                'categories' => $this->categoryModel->findAll(),
                'suppliers'  => $this->supplierModel->findAll(),
            ];
            return $this->response->setJSON([
                'status' => 'success',
                'html'   => view('products/form_modal', $data)
            ]);
        }
        // Fallback for non-AJAX
        return redirect()->to('/products');
    }

    // --------------------------------------------------------------------
    // Store new product
    // --------------------------------------------------------------------
    public function store()
    {
        $rules = $this->productModel->validationRules;
        
        if (! $this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $this->validator->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        
        // Set default values if not provided
        if (!isset($data['cost_price'])) $data['cost_price'] = 0;
        if (!isset($data['stock_qty'])) $data['stock_qty'] = 0;
        if (!isset($data['reorder_level'])) $data['reorder_level'] = 0;
        if (!isset($data['is_active'])) $data['is_active'] = 1;
        
        $this->productModel->save($data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success', 
                'message' => 'Product added successfully'
            ]);
        }
        
        return redirect()->to('/products')->with('message', 'Product added successfully');
    }

    // --------------------------------------------------------------------
    // Edit product – return data for modal
    // --------------------------------------------------------------------
    public function edit($id)
    {
        $product = $this->productModel->getWithDetails($id);
        if (! $product) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Product not found']);
            }
            return redirect()->to('/products')->with('error', 'Product not found');
        }

        if ($this->request->isAJAX()) {
            $data = [
                'product'    => $product,
                'categories' => $this->categoryModel->findAll(),
                'suppliers'  => $this->supplierModel->findAll(),
            ];
            return $this->response->setJSON([
                'status' => 'success',
                'html'   => view('products/form_modal', $data)
            ]);
        }
        
        return redirect()->to('/products');
    }

    // --------------------------------------------------------------------
    // Update product
    // --------------------------------------------------------------------
    public function update($id)
{
    $rules = $this->productModel->validationRules;
    // Modify unique rule for update - CORRECT SYNTAX
    $rules['sku'] = "required|is_unique[products.sku,{$id}]";
    
    if (! $this->validate($rules)) {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }

    $data = $this->request->getPost();
    
    // Remove id from data if present
    unset($data['id']);
    
    $result = $this->productModel->update($id, $data);

    if ($this->request->isAJAX()) {
        if ($result) {
            return $this->response->setJSON([
                'status' => 'success', 
                'message' => 'Product updated successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error', 
                'message' => 'Failed to update product'
            ]);
        }
    }
    
    return redirect()->to('/products')->with('message', 'Product updated successfully');
}

    // --------------------------------------------------------------------
    // Delete product
    // --------------------------------------------------------------------
    public function delete($id)
    {
        // Check if product exists
        $product = $this->productModel->find($id);
        if (!$product) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error', 
                    'message' => 'Product not found'
                ]);
            }
            return redirect()->to('/products')->with('error', 'Product not found');
        }
        
        // Delete stock adjustments first (if any)
        $this->stockAdjustmentModel->where('product_id', $id)->delete();
        
        // Delete the product
        $this->productModel->delete($id);
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success', 
                'message' => 'Product deleted successfully'
            ]);
        }
        
        return redirect()->to('/products')->with('message', 'Product deleted successfully');
    }

    // --------------------------------------------------------------------
    // Restock product (increment/decrement stock and log adjustment)
    // --------------------------------------------------------------------
    public function restock($id)
    {
        if ($this->request->getMethod() === 'post') {
            $type = $this->request->getPost('type');
            $quantity = (int) $this->request->getPost('quantity');
            $reason = $this->request->getPost('reason') ?? ($type === 'in' ? 'Manual restock' : 'Manual removal');
            
            if ($quantity <= 0) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'status' => 'error', 
                        'message' => 'Quantity must be positive'
                    ]);
                }
                return redirect()->back()->with('error', 'Quantity must be positive');
            }

            // Check if product exists
            $product = $this->productModel->find($id);
            if (!$product) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'status' => 'error', 
                        'message' => 'Product not found'
                    ]);
                }
                return redirect()->to('/products')->with('error', 'Product not found');
            }

            // For stock out, check if enough stock is available
            if ($type === 'out' && $product['stock_qty'] < $quantity) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'status' => 'error', 
                        'message' => 'Insufficient stock. Current stock: ' . $product['stock_qty']
                    ]);
                }
                return redirect()->back()->with('error', 'Insufficient stock');
            }

            // Record stock adjustment (trigger will update product stock_qty)
            $this->stockAdjustmentModel->insert([
                'product_id' => $id,
                'user_id'    => session()->get('id'),
                'type'       => $type,
                'quantity'   => $quantity,
                'reason'     => $reason,
            ]);

            $message = $type === 'in' ? "Stock increased by {$quantity}" : "Stock decreased by {$quantity}";

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'success', 
                    'message' => $message
                ]);
            }
            
            return redirect()->to('/products')->with('message', $message);
        }

        // For AJAX modal content
        if ($this->request->isAJAX()) {
            $product = $this->productModel->find($id);
            if ($product) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'html'   => view('products/restock_modal', ['product' => $product])
                ]);
            }
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Product not found'
            ]);
        }
        
        return redirect()->to('/products');
    }

    // --------------------------------------------------------------------
    // Search products (AJAX endpoint)
    // --------------------------------------------------------------------
    public function search()
    {
        $keyword = $this->request->getGet('keyword');
        
        if (!$keyword) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Search keyword is required'
            ]);
        }
        
        $products = $this->productModel->searchProducts($keyword);
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $products
        ]);
    }

    // --------------------------------------------------------------------
    // Bulk update stock status
    // --------------------------------------------------------------------
    public function updateStockStatus()
    {
        $products = $this->productModel->findAll();
        
        foreach ($products as $product) {
            $status = ($product['stock_qty'] <= $product['reorder_level']) ? 'low' : 'normal';
            // You can add a 'stock_status' field to your products table if needed
            // $this->productModel->update($product['id'], ['stock_status' => $status]);
        }
        
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Stock status updated'
        ]);
    }

    // --------------------------------------------------------------------
    // Export products to CSV
    // --------------------------------------------------------------------
    public function export()
    {
        $products = $this->productModel->getProductsWithDetails();
        
        $filename = 'products_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Add headers
        fputcsv($output, [
            'ID', 'Name', 'Category', 'Supplier', 
            'Cost Price', 'Selling Price', 'Stock', 'Reorder Level', 
            'Status', 'Created At', 'Updated At'
        ]);
        
        // Add data rows
        foreach ($products as $product) {
            fputcsv($output, [
                $product['id'],
                $product['name'],
                $product['sku'],
                $product['category_name'] ?? '',
                $product['supplier_name'] ?? '',
                $product['cost_price'] ?? 0,
                $product['price'],
                $product['stock_qty'] ?? 0,
                $product['reorder_level'] ?? 0,
                $product['is_active'] == 1 ? 'Active' : 'Inactive',
                $product['created_at'],
                $product['updated_at']
            ]);
        }
        
        fclose($output);
        exit();
    }

    // --------------------------------------------------------------------
    // Legacy methods (for backward compatibility)
    // --------------------------------------------------------------------
    public function save()
    {
        return $this->store();
    }
    
    public function fetchRecords()
    {
        return $this->getSuppliersData();
    }
    
    public function jsonList()
    {
        return $this->getSuppliersData();
    }
}