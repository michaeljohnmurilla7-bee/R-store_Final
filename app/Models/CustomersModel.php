<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomersModel extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object'; // Can be 'array' or 'object'
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'name',
        'phone',
        'email',
        'address',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = null;

    // Validation
   protected $validationRules = [
    'name' => 'required|min_length[2]|max_length[100]',
    'phone' => 'permit_empty|min_length[10]|max_length[20]',
    'email' => 'required|valid_email',
    'address' => 'permit_empty|max_length[255]'
];

    protected $validationMessages = [
        'name' => [
            'required' => 'Customer name is required',
            'min_length' => 'Name must be at least 2 characters long'
        ],
        'email' => [
            'required' => 'Email address is required',
            'valid_email' => 'Please provide a valid email address',
            'is_unique' => 'This email is already registered'
        ]
    ];

    protected $skipValidation = true;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['setTimestamps'];
    protected $beforeUpdate = ['setTimestamps'];

    /**
     * Set timestamps before insert/update
     */
    protected function setTimestamps(array $data)
    {
        $currentTime = date('Y-m-d H:i:s');
        
        if (!isset($data['data']['created_at']) && $this->createdField) {
            $data['data'][$this->createdField] = $currentTime;
        }
        
        if ($this->updatedField) {
            $data['data'][$this->updatedField] = $currentTime;
        }
        
        return $data;
    }

    /**
     * Get customers with pagination for DataTable
     */
    public function getDatatableData($search = null, $orderBy = 'id', $orderDir = 'DESC', $start = 0, $length = 10)
    {
        $builder = $this->builder();
        
        // Apply search
        if ($search) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('email', $search)
                ->orLike('phone', $search)
                ->orLike('address', $search)
                ->groupEnd();
        }
        
        // Get total count
        $totalCount = $builder->countAllResults(false);
        
        // Apply ordering and limits
        $builder->orderBy($orderBy, $orderDir)
            ->limit($length, $start);
        
        $data = $builder->get()->getResult();
        
        return [
            'data' => $data,
            'totalCount' => $totalCount
        ];
    }

    /**
     * Get total customer count
     */
    public function getTotalCount()
    {
        return $this->countAllResults();
    }

    /**
     * Get recent customers
     */
    public function getRecentCustomers($limit = 10)
    {
        return $this->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Search customers
     */
    public function searchCustomers($keyword)
    {
        return $this->groupStart()
            ->like('name', $keyword)
            ->orLike('email', $keyword)
            ->orLike('phone', $keyword)
            ->orLike('address', $keyword)
            ->groupEnd()
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Bulk delete customers
     */
    public function bulkDelete($customerIds)
    {
        if (empty($customerIds)) {
            return false;
        }
        
        return $this->whereIn('id', $customerIds)->delete();
    }

    /**
     * Get customers by date range
     */
    public function getCustomersByDateRange($startDate, $endDate)
    {
        return $this->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Export customers to array
     */
    public function getExportData($customerIds = null)
    {
        $builder = $this->select('id, name, phone, email, address, created_at, updated_at');
        
        if ($customerIds && is_array($customerIds)) {
            $builder->whereIn('id', $customerIds);
        }
        
        return $builder->orderBy('id', 'ASC')->findAll();
    }

    /**
     * Check if email exists (for validation)
     */
    public function emailExists($email, $excludeId = null)
    {
        $builder = $this->where('email', $email);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Get customer by email
     */
    public function getCustomerByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Get customer by phone
     */
    public function getCustomerByPhone($phone)
    {
        return $this->where('phone', $phone)->first();
    }
}