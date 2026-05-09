<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table = 'suppliers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Make sure ALL fields are included here
    protected $allowedFields = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'is_active',
    ];
    
    // Disable validation temporarily to test
    protected $skipValidation = true;
    
    // Or keep validation but make it less strict
    // protected $validationRules = [];
}