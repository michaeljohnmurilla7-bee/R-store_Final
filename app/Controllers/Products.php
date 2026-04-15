<?php

namespace App\Controllers;

use App\Models\ProductsModel;
use CodeIgniter\Controller;
use App\Models\LogModel;

class Products extends Controller
{
    public function index(){
        $model = new ProductsModel();
        $data['Products'] = $model->findAll();
        return view('Products/index', $data);
    }

    public function save(){
        $name = $this->request->getPost('name');
        $price = $this->request->getPost('price');
        $stock = $this->request->getPost('stock');
        $category = $this->request->getPost('category');
       
        if (!$price || !$stock) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'price and password are required']);
        }

        $ProductsModel = new \App\Models\ProductsModel();
        $logModel = new LogModel();

        // Check if price already exists
        $existingUser = $ProductsModel->where('price', $price)->first();
        if ($existingUser) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'price is already in use']);
        }

        $data = [
            'name'       => $name,
            'price'      => $price,
            'stock'   => password_hash($stock, PASSWORD_DEFAULT),
            'category'       => $category,
          
        ];

        if ($ProductsModel->insert($data)) {
            $logModel->addLog('New User has been added: ' . $name, 'ADD');
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to save user']);
        }
    }

    public function update(){
        $model = new ProductsModel();
        $logModel = new LogModel();
        $userId = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $price = $this->request->getPost('price');
        $stock = $this->request->getPost('stock');
        $category = $this->request->getPost('category');
      
    // Validate the input
        if (empty($price)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Email is required']);
        }

   
}

public function delete($id){
    $model = new ProductsModel();
    $logModel = new LogModel();
    $user = $model->find($id);
    if (!$user) {
        return $this->response->setJSON(['success' => false, 'message' => 'User not found.']);
    }

    $deleted = $model->delete($id);

    if ($deleted) {
        $logModel->addLog('Delete user', 'DELETED');
        return $this->response->setJSON(['success' => true, 'message' => 'User deleted successfully.']);
    } else {
        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete user.']);
    }
}

public function fetchRecords()
{
    $request = service('request');
    $model = new \App\Models\ProductsModel();

    $start = $request->getPost('start') ?? 0;
    $length = $request->getPost('length') ?? 10;
    $searchValue = $request->getPost('search')['value'] ?? '';

    $totalRecords = $model->countAll();
    $result = $model->getRecords($start, $length, $searchValue);

    $data = [];
    $counter = $start + 1;
    foreach ($result['data'] as $row) {
        $row['row_number'] = $counter++;
        $data[] = $row;
    }

    return $this->response->setJSON([
        'draw' => intval($request->getPost('draw')),
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $result['filtered'],
        'data' => $data,
    ]);
}

public function getCount()
{
    $db = \Config\Database::connect();
    $query = $db->query("SELECT COUNT(*) as total FROM product"); // Make sure table name is 'product'
    $result = $query->getRow();
    
    return $this->response->setJSON(['count' => $result->total]);
}
}