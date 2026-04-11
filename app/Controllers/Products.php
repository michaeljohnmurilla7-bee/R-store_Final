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
    // AJAX endpoint for product count (used in dashboard)
    // --------------------------------------------------------------------
    public function getCount()
    {
        $count = $this->productModel->countAll();
        return $this->response->setJSON(['count' => $count]);
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
        $this->productModel->save($data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Product added successfully']);
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
    }

    // --------------------------------------------------------------------
    // Update product
    // --------------------------------------------------------------------
    public function update($id)
    {
        $rules = $this->productModel->validationRules;
        // Modify unique rule for update
        $rules['sku'] = "required|is_unique[products.sku,id,{$id}]";
          $rules['id']  = 'permit_empty|is_natural_no_zero'; 
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
        $this->productModel->update($id, $data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Product updated successfully']);
        }
        
        return redirect()->to('/products')->with('message', 'Product updated successfully');
    }

    // --------------------------------------------------------------------
    // Delete product
    // --------------------------------------------------------------------
    public function delete($id)
    {
        $this->productModel->delete($id);
        return redirect()->to('/products')->with('message', 'Product deleted');
    }

    // --------------------------------------------------------------------
    // Restock product (increment stock and log adjustment)
    // --------------------------------------------------------------------
    public function restock($id)
    {
        if ($this->request->getMethod() === 'post') {
            $quantity = (int) $this->request->getPost('quantity');
            $reason   = $this->request->getPost('reason') ?? 'Manual restock';
            
            if ($quantity <= 0) {
                return redirect()->back()->with('error', 'Quantity must be positive');
            }

            // Record stock adjustment (trigger will update product stock_qty)
            $this->stockAdjustmentModel->insert([
                'product_id' => $id,
                'user_id'    => session()->get('id'),
                'type'       => 'in',
                'quantity'   => $quantity,
                'reason'     => $reason,
            ]);

            return redirect()->to('/products')->with('message', "Stock increased by {$quantity}");
        }

        // For AJAX modal content
        if ($this->request->isAJAX()) {
            $product = $this->productModel->find($id);
            return $this->response->setJSON([
                'status' => 'success',
                'html'   => view('products/restock_modal', ['product' => $product])
            ]);
        }
        
        return redirect()->to('/products');
    }
}