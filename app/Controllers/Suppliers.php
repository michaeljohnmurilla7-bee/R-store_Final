<?php namespace App\Controllers;
use App\Models\SupplierModel;

class Suppliers extends BaseController {
    protected $model;
    public function __construct() { $this->model = new SupplierModel(); helper(['form','url']); }
    public function index() {
        $data['suppliers'] = $this->model->findAll();
        return view('suppliers/index', $data);
    }
    // create, store, edit, update, delete similar to categories
    // In store/update, handle checkbox 'is_active' (if unchecked, set to 0)
}