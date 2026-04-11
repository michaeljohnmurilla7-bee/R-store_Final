<?php namespace App\Models;
use CodeIgniter\Model;
class SupplierModel extends Model {
    protected $table = 'suppliers';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['name','contact_person','phone','email','address','is_active'];
    protected $validationRules = ['name'=>'required|min_length[3]'];
}