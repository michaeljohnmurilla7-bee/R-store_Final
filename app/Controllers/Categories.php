<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductsModel;

class Categories extends BaseController
{
    protected $categoryModel;
    protected $productModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
        $this->productModel = new ProductsModel();
        helper(['form', 'url']);
    }

    // --------------------------------------------------------------------
    // List all categories (main page)
    // --------------------------------------------------------------------
    public function index()
    {
        $data['title'] = 'Categories';
        $data['categories'] = $this->categoryModel->findAll();
        
        return view('categories/index', $data);
    }

    // --------------------------------------------------------------------
    // AJAX endpoint for DataTable - Get all categories with product count
    // --------------------------------------------------------------------
    public function getCategoriesData()
    {
        $categories = $this->categoryModel->findAll();
        
        // Add product count for each category
        foreach ($categories as &$category) {
            $category['products_count'] = $this->productModel
                ->where('category_id', $category['id'])
                ->countAllResults();
        }
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    // --------------------------------------------------------------------
    // AJAX endpoint to get single category with product count
    // --------------------------------------------------------------------
    public function getCategory($id)
    {
        $category = $this->categoryModel->find($id);
        
        if ($category) {
            // Get product count for this category
            $category['products_count'] = $this->productModel
                ->where('category_id', $id)
                ->countAllResults();
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $category
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Category not found'
            ]);
        }
    }

    // --------------------------------------------------------------------
    // AJAX endpoint for category count (used in dashboard)
    // --------------------------------------------------------------------
    public function getCount()
    {
        $count = $this->categoryModel->countAll();
        return $this->response->setJSON(['count' => $count]);
    }

    // --------------------------------------------------------------------
    // Get categories for dropdown/select inputs
    // --------------------------------------------------------------------
    // Add this method to your Categories.php controller
public function getSelectList()
{
    $categoryModel = new \App\Models\CategoryModel();
    $categories = $categoryModel->orderBy('name', 'ASC')->findAll();
    $selectList = [];
    
    foreach ($categories as $category) {
        $selectList[] = [
            'id' => $category['id'],
            'text' => $category['name']
        ];
    }
    
    return $this->response->setJSON($selectList);
}
    // --------------------------------------------------------------------
    // Check if category has products (before deletion)
    // --------------------------------------------------------------------
    public function checkProducts($id)
    {
        $products = $this->productModel->where('category_id', $id)->findAll();
        
        if (count($products) > 0) {
            return $this->response->setJSON([
                'has_products' => true,
                'products' => $products
            ]);
        } else {
            return $this->response->setJSON([
                'has_products' => false
            ]);
        }
    }

    // --------------------------------------------------------------------
    // Store new category
    // --------------------------------------------------------------------
    public function store()
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[100]|is_unique[categories.name]',
            'description' => 'permit_empty|max_length[500]',
            'is_active' => 'permit_empty|integer|in_list[0,1]'
        ];
        
        if (! $this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $this->validator->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'is_active' => $this->request->getPost('is_active') ?? 1
        ];
        
        $this->categoryModel->save($data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success', 
                'message' => 'Category added successfully'
            ]);
        }
        
        return redirect()->to('/categories')->with('message', 'Category added successfully');
    }

    // --------------------------------------------------------------------
    // Update category
    // --------------------------------------------------------------------
    public function update($id)
    {
        // Check if category exists
        $category = $this->categoryModel->find($id);
        if (!$category) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error', 
                    'message' => 'Category not found'
                ]);
            }
            return redirect()->to('/categories')->with('error', 'Category not found');
        }

        $rules = [
            'name' => "required|min_length[2]|max_length[100]|is_unique[categories.name,{$id}]",
            'description' => 'permit_empty|max_length[500]',
            'is_active' => 'permit_empty|integer|in_list[0,1]'
        ];
        
        if (! $this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $this->validator->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'is_active' => $this->request->getPost('is_active') ?? 1
        ];
        
        $this->categoryModel->update($id, $data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success', 
                'message' => 'Category updated successfully'
            ]);
        }
        
        return redirect()->to('/categories')->with('message', 'Category updated successfully');
    }

    // --------------------------------------------------------------------
    // Delete category (only if no products are associated)
    // --------------------------------------------------------------------
    public function delete($id)
    {
        // Check if category exists
        $category = $this->categoryModel->find($id);
        if (!$category) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error', 
                    'message' => 'Category not found'
                ]);
            }
            return redirect()->to('/categories')->with('error', 'Category not found');
        }
        
        // Check if category has products
        $productCount = $this->productModel->where('category_id', $id)->countAllResults();
        
        if ($productCount > 0) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error', 
                    'message' => 'Cannot delete category with associated products. Please reassign or delete the products first.'
                ]);
            }
            return redirect()->to('/categories')->with('error', 'Cannot delete category with associated products.');
        }
        
        // Delete the category
        $this->categoryModel->delete($id);
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success', 
                'message' => 'Category deleted successfully'
            ]);
        }
        
        return redirect()->to('/categories')->with('message', 'Category deleted successfully');
    }

    // --------------------------------------------------------------------
    // Toggle category status (activate/deactivate)
    // --------------------------------------------------------------------
    public function toggleStatus($id)
    {
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Category not found'
                ]);
            }
            return redirect()->to('/categories')->with('error', 'Category not found');
        }
        
        $newStatus = $category['is_active'] == 1 ? 0 : 1;
        $this->categoryModel->update($id, ['is_active' => $newStatus]);
        
        $message = $newStatus == 1 ? 'Category activated successfully' : 'Category deactivated successfully';
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => $message,
                'is_active' => $newStatus
            ]);
        }
        
        return redirect()->to('/categories')->with('message', $message);
    }

    // --------------------------------------------------------------------
    // Export categories to CSV
    // --------------------------------------------------------------------
    public function export()
    {
        $categories = $this->categoryModel->orderBy('name', 'ASC')->findAll();
        
        // Add product count for each category
        foreach ($categories as &$category) {
            $category['products_count'] = $this->productModel
                ->where('category_id', $category['id'])
                ->countAllResults();
        }
        
        $filename = 'categories_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Add headers
        fputcsv($output, ['ID', 'Category Name', 'Description', 'Products Count', 'Status', 'Created At', 'Updated At']);
        
        // Add data rows
        foreach ($categories as $category) {
            fputcsv($output, [
                $category['id'],
                $category['name'],
                $category['description'] ?? '',
                $category['products_count'],
                $category['is_active'] == 1 ? 'Active' : 'Inactive',
                $category['created_at'],
                $category['updated_at']
            ]);
        }
        
        fclose($output);
        exit();
    }

    // --------------------------------------------------------------------
    // Get category statistics
    // --------------------------------------------------------------------
    public function getStatistics()
    {
        $totalCategories = $this->categoryModel->countAll();
        $activeCategories = $this->categoryModel->where('is_active', 1)->countAllResults();
        $inactiveCategories = $this->categoryModel->where('is_active', 0)->countAllResults();
        
        // Get categories with most products
        $categories = $this->categoryModel->findAll();
        $categoryProductCounts = [];
        foreach ($categories as $category) {
            $count = $this->productModel->where('category_id', $category['id'])->countAllResults();
            $categoryProductCounts[] = [
                'name' => $category['name'],
                'product_count' => $count
            ];
        }
        
        // Sort by product count
        usort($categoryProductCounts, function($a, $b) {
            return $b['product_count'] - $a['product_count'];
        });
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'total_categories' => $totalCategories,
                'active_categories' => $activeCategories,
                'inactive_categories' => $inactiveCategories,
                'top_categories' => array_slice($categoryProductCounts, 0, 5)
            ]
        ]);
    }

    // --------------------------------------------------------------------
    // Bulk update category status
    // --------------------------------------------------------------------
    public function bulkUpdateStatus()
    {
        $categoryIds = $this->request->getPost('category_ids');
        $status = $this->request->getPost('status');
        
        if (empty($categoryIds) || !is_array($categoryIds)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No categories selected'
            ]);
        }
        
        foreach ($categoryIds as $id) {
            $this->categoryModel->update($id, ['is_active' => $status]);
        }
        
        return $this->response->setJSON([
            'status' => 'success',
            'message' => count($categoryIds) . ' categories updated successfully'
        ]);
    }

    // --------------------------------------------------------------------
    // Search categories
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
        
        $categories = $this->categoryModel->like('name', $keyword)
                                         ->orLike('description', $keyword)
                                         ->findAll();
        
        // Add product count for each category
        foreach ($categories as &$category) {
            $category['products_count'] = $this->productModel
                ->where('category_id', $category['id'])
                ->countAllResults();
        }
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $categories
        ]);
    }
}