<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomersModel;

class Customers extends BaseController
{
    protected $customerModel;
    protected $session;
    protected $validation;
    
    public function __construct()
    {
        $this->customerModel = new CustomersModel();
        $this->session = \Config\Services::session();
        $this->validation = \Config\Services::validation();
    }
    
    /**
     * Display customers list page
     */
    public function index()
    {
        $data = [
            'title' => 'Customers Management',
            'page_title' => 'Customers',
            'subtitle' => 'Manage your customer database'
        ];
        
        return view('customers/index', $data);
    }
    
    /**
     * Get total customer count (for dashboard/stats)
     */
    public function getCount()
    {
        $total = $this->customerModel->getTotalCount();
        $recent = $this->customerModel->getRecentCustomers(5);
        
        return $this->response->setJSON([
            'success' => true,
            'total' => $total,
            'recent_count' => count($recent)
        ]);
    }
    
    /**
     * Get customers data for DataTable
     */
    public function getCustomersData()
    {
        // Get request variables from DataTable
        $search = $this->request->getVar('search')['value'] ?? null;
        $orderBy = $this->request->getVar('order')[0]['column'] ?? 0;
        $orderDir = $this->request->getVar('order')[0]['dir'] ?? 'DESC';
        $start = $this->request->getVar('start') ?? 0;
        $length = $this->request->getVar('length') ?? 10;
        
        // Map column indexes to database fields
        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'phone',
            3 => 'email',
            4 => 'address',
            5 => 'created_at',
            6 => 'updated_at'
        ];
        
        $orderBy = $columns[$orderBy] ?? 'id';
        
        // Get data from model
        $result = $this->customerModel->getDatatableData($search, $orderBy, $orderDir, $start, $length);
        
        // Prepare data for DataTable
        $data = [];
        foreach ($result['data'] as $customer) {
            $data[] = [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone ?? '-',
                'email' => $customer->email,
                'address' => $customer->address ? substr($customer->address, 0, 50) . '...' : '-',
                'created_at' => date('d/m/Y H:i', strtotime($customer->created_at)),
                'updated_at' => date('d/m/Y H:i', strtotime($customer->updated_at)),
                'action' => $this->generateActionButtons($customer->id)
            ];
        }
        
        return $this->response->setJSON([
            'draw' => $this->request->getVar('draw'),
            'recordsTotal' => $result['totalCount'],
            'recordsFiltered' => $result['totalCount'],
            'data' => $data
        ]);
    }
    
    /**
     * Generate action buttons for DataTable
     */
    private function generateActionButtons($id)
    {
        $buttons = '
            <div class="btn-group" role="group">
                <a href="' . site_url('customers/' . $id . '/edit') . '" class="btn btn-sm btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <button type="button" class="btn btn-sm btn-danger" onclick="deleteCustomer(' . $id . ')">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        ';
        
        return $buttons;
    }
    
    /**
     * Get single customer data
     */
    public function getCustomer($id)
    {
        $customer = $this->customerModel->find($id);
        
        if ($customer) {
            return $this->response->setJSON([
                'success' => true,
                'data' => $customer
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Customer not found'
        ])->setStatusCode(404);
    }
    
    /**
     * Get recent customers
     */
    public function getRecentCustomers()
    {
        $limit = $this->request->getVar('limit') ?? 10;
        $customers = $this->customerModel->getRecentCustomers($limit);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $customers
        ]);
    }
    
    /**
     * Display create customer form
     */
    public function create()
    {
        $data = [
            'title' => 'Add New Customer',
            'page_title' => 'Create Customer',
            'subtitle' => 'Add a new customer to database'
        ];
        
        return view('customers/create', $data);
    }
    
    /**
     * Store new customer
     */
    public function store()
    {
        // Validate request
        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email|is_unique[customers.email]',
            'phone' => 'permit_empty|min_length[10]|max_length[20]',
            'address' => 'permit_empty|max_length[255]'
        ];
        
        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $this->validator->getErrors()
                ])->setStatusCode(422);
            }
            
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Prepare data
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address')
        ];
        
        // Save customer
        if ($this->customerModel->insert($data)) {
            $customerId = $this->customerModel->getInsertID();
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Customer created successfully',
                    'id' => $customerId
                ]);
            }
            
            $this->session->setFlashdata('success', 'Customer created successfully');
            return redirect()->to('/customers');
        }
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create customer'
            ])->setStatusCode(500);
        }
        
        $this->session->setFlashdata('error', 'Failed to create customer');
        return redirect()->back()->withInput();
    }
    
    /**
     * Display edit customer form
     */
    public function edit($id)
    {
        $customer = $this->customerModel->find($id);
        
        if (!$customer) {
            $this->session->setFlashdata('error', 'Customer not found');
            return redirect()->to('/customers');
        }
        
        $data = [
            'title' => 'Edit Customer',
            'page_title' => 'Edit Customer',
            'subtitle' => 'Update customer information',
            'customer' => $customer
        ];
        
        return view('customers/edit', $data);
    }
    
    /**
     * Update customer
     */
    public function update($id)
    {
        // Check if customer exists
        $customer = $this->customerModel->find($id);
        if (!$customer) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Customer not found'
                ])->setStatusCode(404);
            }
            
            $this->session->setFlashdata('error', 'Customer not found');
            return redirect()->to('/customers');
        }
        
        // Validate request
        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
            'email' => "required|valid_email|is_unique[customers.email,id,{$id}]",
            'phone' => 'permit_empty|min_length[10]|max_length[20]',
            'address' => 'permit_empty|max_length[255]'
        ];
        
        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $this->validator->getErrors()
                ])->setStatusCode(422);
            }
            
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Prepare data
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address')
        ];
        
        // Update customer
        if ($this->customerModel->update($id, $data)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Customer updated successfully'
                ]);
            }
            
            $this->session->setFlashdata('success', 'Customer updated successfully');
            return redirect()->to('/customers');
        }
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update customer'
            ])->setStatusCode(500);
        }
        
        $this->session->setFlashdata('error', 'Failed to update customer');
        return redirect()->back()->withInput();
    }
    
    /**
     * Delete customer
     */
    public function delete($id)
    {
        // Check if customer exists
        $customer = $this->customerModel->find($id);
        if (!$customer) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Customer not found'
                ])->setStatusCode(404);
            }
            
            $this->session->setFlashdata('error', 'Customer not found');
            return redirect()->to('/customers');
        }
        
        // Delete customer
        if ($this->customerModel->delete($id)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Customer deleted successfully'
                ]);
            }
            
            $this->session->setFlashdata('success', 'Customer deleted successfully');
            return redirect()->to('/customers');
        }
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete customer'
            ])->setStatusCode(500);
        }
        
        $this->session->setFlashdata('error', 'Failed to delete customer');
        return redirect()->to('/customers');
    }
    
    /**
     * Bulk delete customers
     */
    public function bulkDelete()
    {
        $customerIds = $this->request->getPost('customer_ids');
        
        if (empty($customerIds) || !is_array($customerIds)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No customers selected'
                ])->setStatusCode(400);
            }
            
            $this->session->setFlashdata('error', 'No customers selected');
            return redirect()->to('/customers');
        }
        
        if ($this->customerModel->bulkDelete($customerIds)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => count($customerIds) . ' customers deleted successfully'
                ]);
            }
            
            $this->session->setFlashdata('success', count($customerIds) . ' customers deleted successfully');
            return redirect()->to('/customers');
        }
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete customers'
            ])->setStatusCode(500);
        }
        
        $this->session->setFlashdata('error', 'Failed to delete customers');
        return redirect()->to('/customers');
    }
    
    /**
     * Search customers
     */
    public function search($keyword = null)
    {
        if (!$keyword) {
            $keyword = $this->request->getVar('keyword');
        }
        
        if (!$keyword) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No search keyword provided'
            ])->setStatusCode(400);
        }
        
        $customers = $this->customerModel->searchCustomers($keyword);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $customers,
            'count' => count($customers)
        ]);
    }
    
    /**
     * Advanced search with filters
     */
    public function advancedSearch()
    {
        $searchData = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'date_from' => $this->request->getPost('date_from'),
            'date_to' => $this->request->getPost('date_to')
        ];
        
        // Build query
        $builder = $this->customerModel->builder();
        
        if (!empty($searchData['name'])) {
            $builder->like('name', $searchData['name']);
        }
        
        if (!empty($searchData['email'])) {
            $builder->like('email', $searchData['email']);
        }
        
        if (!empty($searchData['phone'])) {
            $builder->like('phone', $searchData['phone']);
        }
        
        if (!empty($searchData['date_from'])) {
            $builder->where('created_at >=', $searchData['date_from'] . ' 00:00:00');
        }
        
        if (!empty($searchData['date_to'])) {
            $builder->where('created_at <=', $searchData['date_to'] . ' 23:59:59');
        }
        
        $customers = $builder->orderBy('created_at', 'DESC')->get()->getResult();
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'data' => $customers,
                'count' => count($customers)
            ]);
        }
        
        $data = [
            'title' => 'Search Results',
            'page_title' => 'Customer Search',
            'customers' => $customers,
            'search_data' => $searchData
        ];
        
        return view('customers/search_results', $data);
    }
    
    /**
     * Export customers to CSV
     */
    public function export()
    {
        $customerIds = $this->request->getVar('customer_ids') ? explode(',', $this->request->getVar('customer_ids')) : null;
        
        $customers = $this->customerModel->getExportData($customerIds);
        
        if (empty($customers)) {
            $this->session->setFlashdata('error', 'No customers to export');
            return redirect()->to('/customers');
        }
        
        // Set CSV headers
        $filename = 'customers_export_' . date('Y-m-d_His') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Create output stream
        $output = fopen('php://output', 'w');
        
        // Add CSV headers
        fputcsv($output, ['ID', 'Name', 'Phone', 'Email', 'Address', 'Created At', 'Updated At']);
        
        // Add data rows
        foreach ($customers as $customer) {
            fputcsv($output, [
                $customer->id,
                $customer->name,
                $customer->phone,
                $customer->email,
                $customer->address,
                $customer->created_at,
                $customer->updated_at
            ]);
        }
        
        fclose($output);
        exit();
    }
    
    /**
     * Import customers from CSV
     */
    public function import()
    {
        $file = $this->request->getFile('csv_file');
        
        if (!$file || !$file->isValid()) {
            $this->session->setFlashdata('error', 'Please upload a valid CSV file');
            return redirect()->back();
        }
        
        if ($file->getExtension() !== 'csv') {
            $this->session->setFlashdata('error', 'Only CSV files are allowed');
            return redirect()->back();
        }
        
        // Read CSV file
        $handle = fopen($file->getTempName(), 'r');
        $header = fgetcsv($handle);
        
        $imported = 0;
        $failed = 0;
        $errors = [];
        
        while (($row = fgetcsv($handle)) !== false) {
            // Map CSV columns to database fields
            $data = [
                'name' => $row[1] ?? null,
                'phone' => $row[2] ?? null,
                'email' => $row[3] ?? null,
                'address' => $row[4] ?? null
            ];
            
            // Validate email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $failed++;
                $errors[] = "Invalid email: {$data['email']}";
                continue;
            }
            
            // Check if email already exists
            if ($this->customerModel->emailExists($data['email'])) {
                $failed++;
                $errors[] = "Email already exists: {$data['email']}";
                continue;
            }
            
            // Insert customer
            if ($this->customerModel->insert($data)) {
                $imported++;
            } else {
                $failed++;
                $errors[] = "Failed to import: {$data['email']}";
            }
        }
        
        fclose($handle);
        
        $message = "Import completed: {$imported} imported, {$failed} failed";
        
        if ($failed > 0) {
            $this->session->setFlashdata('warning', $message);
            $this->session->setFlashdata('import_errors', $errors);
        } else {
            $this->session->setFlashdata('success', $message);
        }
        
        return redirect()->to('/customers');
    }
    
    /**
     * Download CSV template for import
     */
    public function downloadTemplate()
    {
        $filename = 'customers_import_template.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Add headers
        fputcsv($output, ['ID', 'Name', 'Phone', 'Email', 'Address', 'Created At', 'Updated At']);
        
        // Add example row
        fputcsv($output, ['1', 'John Doe', '1234567890', 'john@example.com', '123 Main St', '', '']);
        fputcsv($output, ['', 'Jane Smith', '0987654321', 'jane@example.com', '456 Oak Ave', '', '']);
        
        fclose($output);
        exit();
    }
    
    /**
     * Legacy methods for backward compatibility
     */
    public function save()
    {
        return $this->store();
    }
    
    public function fetchRecords()
    {
        return $this->getCustomersData();
    }
    
    public function jsonList()
    {
        return $this->getCustomersData();
    }

    public function testData()
    {
    try {
        $customers = $this->customerModel->findAll();
        
        if (empty($customers)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'No customers found',
                'data' => []
            ]);
        }
        
        return $this->response->setJSON([
            'status' => 'success',
            'total_customers' => count($customers),
            'data' => $customers
        ]);
        
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => $e->getMessage()
        ])->setStatusCode(500);
    }
    }
}