<?php

namespace App\Controllers;

use App\Models\ProductsModel;
use App\Models\SalesModel;
use App\Models\CategoryModel;
use App\Models\SupplierModel;
use App\Models\CustomersModel;

class Dashboard extends BaseController
{
    protected $productsModel;
    protected $salesModel;
    protected $categoryModel;
    protected $supplierModel;
    protected $customersModel;
    protected $db;

    public function __construct()
    {
        $this->productsModel = new ProductsModel();
        $this->salesModel = new SalesModel();
        $this->categoryModel = new CategoryModel();
        $this->supplierModel = new SupplierModel();
        $this->customersModel = new CustomersModel();
        $this->db = \Config\Database::connect();
        helper(['number']);
    }

    public function index()
    {
        // Get statistics for cards
        $data['total_products'] = $this->productsModel->where('is_active', 1)->countAllResults();
        $data['total_categories'] = $this->categoryModel->countAllResults();
        $data['total_suppliers'] = $this->supplierModel->countAllResults();
        $data['total_customers'] = $this->customersModel->countAllResults();
        
        // Get sales statistics
        $data['total_sales'] = $this->salesModel->countAllResults();
        $data['total_revenue'] = $this->salesModel->selectSum('total_amount')->first()['total_amount'] ?? 0;
        
        // Get low stock products count
        $data['low_stock'] = $this->productsModel->where('stock_qty <=', 'reorder_level', false)
            ->where('is_active', 1)
            ->countAllResults();
        
        // Get recent sales
        $data['recent_sales'] = $this->salesModel
            ->orderBy('id', 'DESC')
            ->limit(5)
            ->findAll();
        
        return view('dashboard/index', $data);
    }

    // Get chart data for sales trend (last 7 days)
    public function getSalesChartData()
    {
        $chartData = [];
        
        // Get last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $chartData[$date] = [
                'date' => date('M d', strtotime($date)),
                'count' => 0,
                'revenue' => 0
            ];
        }
        
        // Get actual sales data for last 7 days
        $sales = $this->db->table('sales')
            ->select('DATE(sale_date) as sale_date, COUNT(*) as count, SUM(total_amount) as revenue')
            ->where('sale_date >=', date('Y-m-d', strtotime('-6 days')))
            ->groupBy('DATE(sale_date)')
            ->get()
            ->getResultArray();
        
        foreach ($sales as $sale) {
            $dateKey = $sale['sale_date'];
            if (isset($chartData[$dateKey])) {
                $chartData[$dateKey]['count'] = $sale['count'];
                $chartData[$dateKey]['revenue'] = $sale['revenue'];
            }
        }
        
        $labels = array_column($chartData, 'date');
        $counts = array_column($chartData, 'count');
        $revenues = array_column($chartData, 'revenue');
        
        return $this->response->setJSON([
            'status' => 'success',
            'labels' => $labels,
            'counts' => $counts,
            'revenues' => $revenues
        ]);
    }

    // Get chart data for top selling products
    public function getTopProductsChartData()
    {
        $topProducts = $this->db->table('sale_items')
            ->select('products.name, SUM(sale_items.quantity) as total_sold')
            ->join('products', 'products.id = sale_items.product_id')
            ->join('sales', 'sales.id = sale_items.sale_id')
            ->where('sales.sale_date >=', date('Y-m-d', strtotime('-30 days')))
            ->groupBy('sale_items.product_id')
            ->orderBy('total_sold', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();
        
        $labels = array_column($topProducts, 'name');
        $values = array_column($topProducts, 'total_sold');
        
        return $this->response->setJSON([
            'status' => 'success',
            'labels' => $labels,
            'values' => $values
        ]);
    }

     // Get chart data with custom date range
public function getSalesChartDataFiltered()
{
    $days = $this->request->getGet('days') ?? 7;
    $startDate = $this->request->getGet('start_date');
    $endDate = $this->request->getGet('end_date');
    
    // If custom range is provided
    if ($startDate && $endDate) {
        $start = $startDate;
        $end = $endDate;
        
        // Get all dates between start and end
        $period = new \DatePeriod(
            new \DateTime($start),
            new \DateInterval('P1D'),
            (new \DateTime($end))->modify('+1 day')
        );
        
        $chartData = [];
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $chartData[$dateStr] = [
                'date' => $date->format('M d'),
                'count' => 0,
                'revenue' => 0
            ];
        }
    } else {
        // Default: last X days
        $chartData = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $chartData[$date] = [
                'date' => date('M d', strtotime($date)),
                'count' => 0,
                'revenue' => 0
            ];
        }
        $start = date('Y-m-d', strtotime('-' . ($days - 1) . ' days'));
        $end = date('Y-m-d');
    }
    
    // Get actual sales data
    $sales = $this->db->table('sales')
        ->select('DATE(sale_date) as sale_date, COUNT(*) as count, SUM(total_amount) as revenue')
        ->where('sale_date >=', $start)
        ->where('sale_date <=', $end . ' 23:59:59')
        ->groupBy('DATE(sale_date)')
        ->get()
        ->getResultArray();
    
    foreach ($sales as $sale) {
        $dateKey = $sale['sale_date'];
        if (isset($chartData[$dateKey])) {
            $chartData[$dateKey]['count'] = $sale['count'];
            $chartData[$dateKey]['revenue'] = $sale['revenue'];
        }
    }
    
    $labels = array_column($chartData, 'date');
    $counts = array_column($chartData, 'count');
    $revenues = array_column($chartData, 'revenue');
    
    return $this->response->setJSON([
        'status' => 'success',
        'labels' => $labels,
        'counts' => $counts,
        'revenues' => $revenues
    ]);
} 
 
 // Test today's sales data
public function testTodaySales()
{
    $today = date('Y-m-d');
    
    $sales = $this->db->table('sales')
        ->select('COUNT(*) as count, SUM(total_amount) as revenue')
        ->where('DATE(sale_date)', $today)
        ->get()
        ->getRowArray();
    
    return $this->response->setJSON([
        'status' => 'success',
        'date' => $today,
        'count' => $sales['count'] ?? 0,
        'revenue' => $sales['revenue'] ?? 0
    ]);
}

// Get chart data for sales by payment status
// Get chart data for sales by payment status
public function getPaymentStatusData()
{
    $stats = $this->db->table('sales')
        ->select('payment_status, COUNT(*) as count')
        ->groupBy('payment_status')
        ->get()
        ->getResultArray();
    
    $labels = [];
    $values = [];
    $colors = [];
    
    // Match your dashboard green theme colors
    $statusMap = [
        'paid' => ['label' => 'Paid', 'color' => '#28a745'],     // Bootstrap success green
        'partial' => ['label' => 'Partial', 'color' => '#0e3216'], // Your dark green
        'unpaid' => ['label' => 'Unpaid', 'color' => '#083d26']     // Your darker green
    ];
    
    foreach ($stats as $stat) {
        $status = $stat['payment_status'];
        $labels[] = $statusMap[$status]['label'] ?? $status;
        $values[] = $stat['count'];
        $colors[] = $statusMap[$status]['color'] ?? '#0e3216';
    }
    
    return $this->response->setJSON([
        'status' => 'success',
        'labels' => $labels,
        'values' => $values,
        'colors' => $colors
    ]);
}

// Debug method to check sales data
public function debugSalesData()
{
    // Get all sales
    $allSales = $this->db->table('sales')
        ->select('id, invoice_number, sale_date, total_amount')
        ->orderBy('sale_date', 'DESC')
        ->limit(10)
        ->get()
        ->getResultArray();
    
    // Get sales count by date
    $salesByDate = $this->db->table('sales')
        ->select('DATE(sale_date) as sale_date, COUNT(*) as count, SUM(total_amount) as revenue')
        ->where('sale_date >=', date('Y-m-d', strtotime('-7 days')))
        ->groupBy('DATE(sale_date)')
        ->get()
        ->getResultArray();
    
    return $this->response->setJSON([
        'all_sales' => $allSales,
        'sales_by_date_last_7_days' => $salesByDate,
        'today' => date('Y-m-d'),
        'seven_days_ago' => date('Y-m-d', strtotime('-7 days'))
    ]);
}
}