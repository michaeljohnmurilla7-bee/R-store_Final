<?php
// app/Controllers/Suppliers.php

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
        if ($this->request->isAJAX()) {
            $draw = $this->request->getPost('draw');
            $start = $this->request->getPost('start');
            $length = $this->request->getPost('length');
            $search = $this->request->getPost('search')['value'];
            
            $totalRecords = $this->supplierModel->countAll();
            
            if (!empty($search)) {
                $this->supplierModel->groupStart()
                    ->like('name', $search)
                    ->orLike('email', $search)
                    ->orLike('phone', $search)
                    ->groupEnd();
            }
            
            $totalFiltered = $this->supplierModel->countAllResults(false);
            
            if (!empty($search)) {
                $this->supplierModel->groupStart()
                    ->like('name', $search)
                    ->orLike('email', $search)
                    ->orLike('phone', $search)
                    ->groupEnd();
            }
            
            $suppliers = $this->supplierModel->orderBy('id', 'ASC')
                ->findAll($length, $start);
            
            $data = [];
            foreach ($suppliers as $supplier) {
                $data[] = [
                    'id' => $supplier['id'],
                    'name' => $supplier['name'],
                    'contact_person' => $supplier['contact_person'],
                    'email' => $supplier['email'],
                    'phone' => $supplier['phone'],
                    'address' => $supplier['address'],
                    'is_active' => $supplier['is_active']
                ];
            }
            
            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalFiltered,
                'data' => $data
            ]);
        }
    }
    
    public function addSupplier()
{
    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    // Log the request
    log_message('debug', 'Add supplier request received');
    log_message('debug', 'POST data: ' . print_r($this->request->getPost(), true));
    
    // Check if it's an AJAX request
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Invalid request type. Expected AJAX request.'
        ]);
    }
    
    // Validate
    $rules = [
        'name' => 'required|min_length[3]',
        'email' => 'required|valid_email',
        'phone' => 'required|min_length[10]'
    ];
    
    if (!$this->validate($rules)) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => $this->validator->getErrors()
        ]);
    }
    
    // Prepare data
    $data = [
        'name' => $this->request->getPost('name'),
        'contact_person' => $this->request->getPost('contact_person'),
        'email' => $this->request->getPost('email'),
        'phone' => $this->request->getPost('phone'),
        'address' => $this->request->getPost('address'),
        'city' => $this->request->getPost('city'),
        'state' => $this->request->getPost('state'),
        'postal_code' => $this->request->getPost('postal_code'),
        'country' => $this->request->getPost('country'),
        'tax_number' => $this->request->getPost('tax_number'),
        'is_active' => $this->request->getPost('is_active') ?? 1,
        'notes' => $this->request->getPost('notes')
    ];
    
    // Insert
    if ($this->supplierModel->insert($data)) {
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Supplier added successfully'
        ]);
    } else {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to add supplier. Please check the database.'
        ]);
    }
}
    
    public function getSupplier($id)
    {
        if ($this->request->isAJAX()) {
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
    }
    
    public function updateSupplier()
    {
        if ($this->request->isAJAX()) {
            $id = $this->request->getPost('id');
            $supplier = $this->supplierModel->find($id);
            
            if (!$supplier) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Supplier not found'
                ]);
            }
            
            $rules = [
                'name' => 'required|min_length[3]',
                'email' => 'required|valid_email',
                'phone' => 'required|min_length[10]'
            ];
            
            if ($this->request->getPost('email') != $supplier['email']) {
                $rules['email'] .= '|is_unique[suppliers.email]';
            }
            
            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $this->validator->getErrors()
                ]);
            }
            
            $data = [
                'name' => $this->request->getPost('name'),
                'contact_person' => $this->request->getPost('contact_person'),
                'email' => $this->request->getPost('email'),
                'phone' => $this->request->getPost('phone'),
                'address' => $this->request->getPost('address'),
                'city' => $this->request->getPost('city'),
                'state' => $this->request->getPost('state'),
                'postal_code' => $this->request->getPost('postal_code'),
                'country' => $this->request->getPost('country'),
                'tax_number' => $this->request->getPost('tax_number'),
                'is_active' => $this->request->getPost('is_active'),
                'notes' => $this->request->getPost('notes')
            ];
            
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
    }
    
    public function deleteSupplier()
    {
        if ($this->request->isAJAX()) {
            $id = $this->request->getPost('id');
            $supplier = $this->supplierModel->find($id);
            
            if (!$supplier) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Supplier not found'
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
    }
    
    public function refreshCSRF()
    {
        return $this->response->setJSON([
            'csrf_token' => csrf_token(),
            'csrf_hash' => csrf_hash()
        ]);
    }
}