<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomersModel extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'name',
        'phone',
        'email',
        'address',
        'status'  // ADDED missing field
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[100]',
        'phone' => 'permit_empty|min_length[10]|max_length[20]',
        'email' => 'required|valid_email',
        'address' => 'permit_empty|max_length[255]',
        'status' => 'permit_empty|in_list[active,inactive]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Customer name is required',
            'min_length' => 'Name must be at least 2 characters long'
        ],
        'email' => [
            'required' => 'Email address is required',
            'valid_email' => 'Please provide a valid email address'
        ]
    ];

    protected $skipValidation = false;

    /**
     * Get customers with pagination for DataTable
     */
    public function getDatatableData($search = null, $orderBy = 'id', $orderDir = 'DESC', $start = 0, $length = 10)
    {
        $builder = $this->builder();
        
        if ($search) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('email', $search)
                ->orLike('phone', $search)
                ->orLike('address', $search)
                ->groupEnd();
        }
        
        $totalCount = $builder->countAllResults(false);
        
        $builder->orderBy($orderBy, $orderDir)
            ->limit($length, $start);
        
        $data = $builder->get()->getResult();
        
        return [
            'data' => $data,
            'totalCount' => $totalCount
        ];
    }

    public function getTotalCount()
    {
        return $this->countAllResults();
    }

    public function getRecentCustomers($limit = 10)
    {
        return $this->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function searchCustomers($keyword)
    {
        return $this->groupStart()
            ->like('name', $keyword)
            ->orLike('email', $keyword)
            ->orLike('phone', $keyword)
            ->groupEnd()
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    public function emailExists($email, $excludeId = null)
    {
        $builder = $this->where('email', $email);
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }
}