<?php

namespace App\Controllers;

use App\Models\SupplierModel;

class Suppliers extends BaseController
{
    protected $supplierModel;

    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
        helper(['form', 'url']);
    }

    // --------------------------------------------------------------------
    // List all suppliers (main page)
    // --------------------------------------------------------------------
    public function index()
    {
        $data['title'] = 'Suppliers';
        $data['suppliers'] = $this->supplierModel->findAll();
        
        return view('suppliers/index', $data);
    }

    // --------------------------------------------------------------------
    // AJAX endpoint for supplier count (used in dashboard)
    // --------------------------------------------------------------------
    public function getCount()
    {
        $count = $this->supplierModel->countAll();
        return $this->response->setJSON(['count' => $count]);
    }

    // --------------------------------------------------------------------
    // AJAX endpoint for DataTable - Get all suppliers
    // --------------------------------------------------------------------
    public function getSuppliers()
    {
        $suppliers = $this->supplierModel->orderBy('name', 'ASC')->findAll();
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $suppliers
        ]);
    }

    // --------------------------------------------------------------------
    // AJAX endpoint to get single supplier
    // --------------------------------------------------------------------
    public function getSupplier($id)
    {
        $supplier = $this->supplierModel->find($id);
        
        if ($supplier) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $supplier
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Supplier not found'
            ]);
        }
    }

    // --------------------------------------------------------------------
    // Store new supplier
    // --------------------------------------------------------------------
    public function create()
    {
        $rules = $this->supplierModel->validationRules ?? [
            'name' => 'required|min_length[2]|max_length[150]',
            'email' => 'permit_empty|valid_email|max_length[150]',
            'phone' => 'permit_empty|max_length[30]',
            'contact_person' => 'permit_empty|max_length[100]',
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
            'contact_person' => $this->request->getPost('contact_person'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'address' => $this->request->getPost('address'),
            'is_active' => $this->request->getPost('is_active') ?? 1
        ];
        
        $this->supplierModel->save($data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success', 
                'message' => 'Supplier added successfully'
            ]);
        }
        
        return redirect()->to('/suppliers')->with('message', 'Supplier added successfully');
    }

    // --------------------------------------------------------------------
    // Update supplier
    // --------------------------------------------------------------------
    public function update($id)
    {
        $rules = $this->supplierModel->validationRules ?? [
            'name' => 'required|min_length[2]|max_length[150]',
            'email' => 'permit_empty|valid_email|max_length[150]',
            'phone' => 'permit_empty|max_length[30]',
            'contact_person' => 'permit_empty|max_length[100]',
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
            'contact_person' => $this->request->getPost('contact_person'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'address' => $this->request->getPost('address'),
            'is_active' => $this->request->getPost('is_active') ?? 1
        ];
        
        $this->supplierModel->update($id, $data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success', 
                'message' => 'Supplier updated successfully'
            ]);
        }
        
        return redirect()->to('/suppliers')->with('message', 'Supplier updated successfully');
    }

    // --------------------------------------------------------------------
    // Delete supplier
    // --------------------------------------------------------------------
    public function delete($id)
    {
        // Check if supplier has any products before deleting
        $productModel = new \App\Models\ProductsModel();
        $hasProducts = $productModel->where('supplier_id', $id)->countAllResults();
        
        if ($hasProducts > 0) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error', 
                    'message' => 'Cannot delete supplier with existing products. Please reassign or delete associated products first.'
                ]);
            }
            return redirect()->to('/suppliers')->with('error', 'Cannot delete supplier with existing products.');
        }
        
        $this->supplierModel->delete($id);
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success', 
                'message' => 'Supplier deleted successfully'
            ]);
        }
        
        return redirect()->to('/suppliers')->with('message', 'Supplier deleted successfully');
    }

    // --------------------------------------------------------------------
    // Toggle supplier status (activate/deactivate)
    // --------------------------------------------------------------------
    public function toggleStatus($id)
    {
        $supplier = $this->supplierModel->find($id);
        
        if (!$supplier) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Supplier not found'
                ]);
            }
            return redirect()->to('/suppliers')->with('error', 'Supplier not found');
        }
        
        $newStatus = $supplier['is_active'] == 1 ? 0 : 1;
        $this->supplierModel->update($id, ['is_active' => $newStatus]);
        
        $message = $newStatus == 1 ? 'Supplier activated successfully' : 'Supplier deactivated successfully';
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => $message,
                'is_active' => $newStatus
            ]);
        }
        
        return redirect()->to('/suppliers')->with('message', $message);
    }

    // --------------------------------------------------------------------
    // Export suppliers to CSV/Excel
    // --------------------------------------------------------------------
    public function export()
    {
        $suppliers = $this->supplierModel->orderBy('name', 'ASC')->findAll();
        
        $filename = 'suppliers_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Add headers
        fputcsv($output, ['ID', 'Name', 'Contact Person', 'Phone', 'Email', 'Address', 'Status', 'Created At', 'Updated At']);
        
        // Add data rows
        foreach ($suppliers as $supplier) {
            fputcsv($output, [
                $supplier['id'],
                $supplier['name'],
                $supplier['contact_person'] ?? '',
                $supplier['phone'] ?? '',
                $supplier['email'] ?? '',
                $supplier['address'] ?? '',
                $supplier['is_active'] == 1 ? 'Active' : 'Inactive',
                $supplier['created_at'],
                $supplier['updated_at']
            ]);
        }
        
        fclose($output);
        exit();
    }

    // --------------------------------------------------------------------
    // Get suppliers for dropdown/select inputs
    // --------------------------------------------------------------------
    public function getSelectList()
    {
        $suppliers = $this->supplierModel->where('is_active', 1)->orderBy('name', 'ASC')->findAll();
        
        $options = [];
        foreach ($suppliers as $supplier) {
            $options[] = [
                'id' => $supplier['id'],
                'text' => $supplier['name']
            ];
        }
        
        return $this->response->setJSON($options);
    }
}