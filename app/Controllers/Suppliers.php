<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SupplierModel;

class Suppliers extends BaseController
{
    protected $supplierModel;
    
    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
    }
    
    public function index()
    {
        return view('suppliers/index');
    }
    
    public function getSuppliers()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }
        
        $draw = $this->request->getPost('draw');
        $start = $this->request->getPost('start');
        $length = $this->request->getPost('length');
        $search = $this->request->getPost('search')['value'] ?? '';
        
        $builder = $this->supplierModel->builder();
        $totalRecords = $this->supplierModel->countAll();
        
        if (!empty($search)) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('email', $search)
                ->orLike('phone', $search)
                ->orLike('contact_person', $search)
                ->groupEnd();
        }
        
        $totalFiltered = $builder->countAllResults(false);
        $suppliers = $builder->orderBy('name', 'ASC')
            ->limit($length, $start)
            ->get()
            ->getResultArray();
        
        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $suppliers
        ]);
    }
    
    // ============ SELECT LIST FOR DROPDOWNS ============
    public function getSelectList()
    {
        // Set JSON header
        $this->response->setHeader('Content-Type', 'application/json');
        
        try {
            // Get all active suppliers, ordered by name
            $suppliers = $this->supplierModel->where('is_active', 1)
                                              ->orderBy('name', 'ASC')
                                              ->findAll();
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $suppliers
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    // ===================================================
    
    public function addSupplier()
    {
        $this->response->setHeader('Content-Type', 'application/json');
        
        $data = [
            'name' => trim($this->request->getPost('name')),
            'contact_person' => trim($this->request->getPost('contact_person')) ?: null,
            'email' => trim($this->request->getPost('email')) ?: null,
            'phone' => trim($this->request->getPost('phone')) ?: null,
            'address' => trim($this->request->getPost('address')) ?: null,
            'is_active' => $this->request->getPost('is_active') ?? 1
        ];
        
        if (empty($data['name'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Supplier name is required'
            ]);
        }
        
        if ($this->supplierModel->insert($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Supplier added successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to add supplier'
            ]);
        }
    }
    
    public function getSupplier($id)
    {
        $this->response->setHeader('Content-Type', 'application/json');
        
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
    
    public function updateSupplier()
    {
        $this->response->setHeader('Content-Type', 'application/json');
        
        $id = $this->request->getPost('id');
        
        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Supplier ID is required'
            ]);
        }
        
        $data = [
            'name' => trim($this->request->getPost('name')),
            'contact_person' => trim($this->request->getPost('contact_person')) ?: null,
            'email' => trim($this->request->getPost('email')) ?: null,
            'phone' => trim($this->request->getPost('phone')) ?: null,
            'address' => trim($this->request->getPost('address')) ?: null,
            'is_active' => $this->request->getPost('is_active')
        ];
        
        if (empty($data['name'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Supplier name is required'
            ]);
        }
        
        if ($this->supplierModel->update($id, $data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Supplier updated successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update supplier'
            ]);
        }
    }
    
    public function deleteSupplier()
    {
        $this->response->setHeader('Content-Type', 'application/json');
        
        $id = $this->request->getPost('id');
        
        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Supplier ID is required'
            ]);
        }
        
        if ($this->supplierModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Supplier deleted successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to delete supplier'
            ]);
        }
    }
    
    public function refreshCSRF()
    {
        $this->response->setHeader('Content-Type', 'application/json');
        
        return $this->response->setJSON([
            'csrf_token' => csrf_token(),
            'csrf_hash' => csrf_hash()
        ]);
    }
    
    public function export()
    {
        $suppliers = $this->supplierModel->orderBy('name', 'ASC')->findAll();
        
        $filename = 'suppliers_export_' . date('Y-m-d_His') . '.csv';
        
        $this->response->setHeader('Content-Type', 'text/csv');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fwrite($output, "\xEF\xBB\xBF");
        
        fputcsv($output, ['ID', 'Name', 'Contact Person', 'Email', 'Phone', 'Address', 'Status', 'Created At']);
        
        foreach ($suppliers as $supplier) {
            fputcsv($output, [
                $supplier['id'],
                $supplier['name'],
                $supplier['contact_person'] ?? '',
                $supplier['email'] ?? '',
                $supplier['phone'] ?? '',
                $supplier['address'] ?? '',
                $supplier['is_active'] == 1 ? 'Active' : 'Inactive',
                $supplier['created_at'] ?? ''
            ]);
        }
        
        fclose($output);
        exit;
    }
}