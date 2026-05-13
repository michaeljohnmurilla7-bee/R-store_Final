<?php

namespace App\Controllers;

use App\Models\SalesModel;
use App\Models\SaleItemsModel;
use App\Models\ProductsModel;
use App\Models\CategoryModel;
use App\Models\SupplierModel;

class Reports extends BaseController
{
    protected $salesModel;
    protected $saleItemsModel;
    protected $productsModel;
    protected $categoryModel;
    protected $supplierModel;
    protected $db;

    public function __construct()
    {
        $this->salesModel = new SalesModel();
        $this->saleItemsModel = new SaleItemsModel();
        $this->productsModel = new ProductsModel();
        $this->categoryModel = new CategoryModel();
        $this->supplierModel = new SupplierModel();
        $this->db = \Config\Database::connect();
        helper(['form', 'url', 'number']);
    }

    public function index()
    {
        $data['title'] = 'Reports Dashboard';
        return view('reports/index', $data);
    }

    // Sales Report by Date Range
    public function sales()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        
        // Get sales using existing SalesModel
        $sales = $this->salesModel
            ->where('sale_date >=', $startDate)
            ->where('sale_date <=', $endDate . ' 23:59:59')
            ->orderBy('sale_date', 'DESC')
            ->findAll();
        
        // Get item count for each sale
        foreach ($sales as &$sale) {
            $sale['item_count'] = $this->saleItemsModel
                ->where('sale_id', $sale['id'])
                ->countAllResults();
        }
        
        // Get summary using query builder directly
        $summary = $this->db->table('sales')
            ->select('
                COUNT(*) as total_sales,
                SUM(total_amount) as total_revenue,
                SUM(discount) as total_discount,
                SUM(amount_paid) as total_paid,
                AVG(total_amount) as average_sale
            ')
            ->where('sale_date >=', $startDate)
            ->where('sale_date <=', $endDate . ' 23:59:59')
            ->get()
            ->getRowArray();
        
        $data = [
            'title' => 'Sales Report',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'sales' => $sales,
            'summary' => $summary
        ];
        
        return view('reports/sales_report', $data);
    }

    // Inventory Report
    public function inventory()
    {
        // Use existing ProductsModel method
        $products = $this->productsModel->getProductsWithDetails();
        
        // Get low stock using existing method
        $lowStock = $this->productsModel->getLowStock();
        
        // Get out of stock
        $outOfStock = $this->productsModel->getOutOfStock();
        
        // Calculate totals
        $totalStockValue = $this->productsModel->getTotalStockValue();
        $totalCostValue = $this->productsModel->getTotalCostValue();
        $totalProducts = $this->productsModel->where('is_active', 1)->countAllResults();
        
        $data = [
            'title' => 'Inventory Report',
            'products' => $products,
            'lowStock' => $lowStock,
            'outOfStock' => $outOfStock,
            'totalStockValue' => $totalStockValue,
            'totalCostValue' => $totalCostValue,
            'totalProducts' => $totalProducts
        ];
        
        return view('reports/inventory_report', $data);
    }

    // Daily Sales Report
    public function daily($date = null)
    {
        $date = $date ?? date('Y-m-d');
        
        // Get sales for the day
        $sales = $this->salesModel
            ->where('sale_date >=', $date . ' 00:00:00')
            ->where('sale_date <=', $date . ' 23:59:59')
            ->orderBy('sale_date', 'DESC')
            ->findAll();
        
        foreach ($sales as &$sale) {
            $sale['item_count'] = $this->saleItemsModel
                ->where('sale_id', $sale['id'])
                ->countAllResults();
        }
        
        // Get daily summary
        $summary = $this->db->table('sales')
            ->select('
                COUNT(*) as total_sales,
                SUM(total_amount) as total_revenue,
                SUM(discount) as total_discount,
                SUM(amount_paid) as total_paid
            ')
            ->where('sale_date >=', $date . ' 00:00:00')
            ->where('sale_date <=', $date . ' 23:59:59')
            ->get()
            ->getRowArray();
        
        $data = [
            'title' => 'Daily Sales Report',
            'date' => $date,
            'sales' => $sales,
            'summary' => $summary
        ];
        
        return view('reports/daily_report', $data);
    }

    // Monthly Sales Report
    public function monthly($year = null, $month = null)
    {
        $year = $year ?? date('Y');
        $month = $month ?? date('m');
        
        $startDate = $year . '-' . $month . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));
        
        // Get sales for the month
        $sales = $this->salesModel
            ->where('sale_date >=', $startDate)
            ->where('sale_date <=', $endDate . ' 23:59:59')
            ->orderBy('sale_date', 'DESC')
            ->findAll();
        
        foreach ($sales as &$sale) {
            $sale['item_count'] = $this->saleItemsModel
                ->where('sale_id', $sale['id'])
                ->countAllResults();
        }
        
        // Get daily breakdown for the month
        $dailyBreakdown = $this->db->table('sales')
            ->select('DATE(sale_date) as sale_day, COUNT(*) as count, SUM(total_amount) as total')
            ->where('sale_date >=', $startDate)
            ->where('sale_date <=', $endDate . ' 23:59:59')
            ->groupBy('DATE(sale_date)')
            ->orderBy('sale_day', 'ASC')
            ->get()
            ->getResultArray();
        
        // Get monthly summary
        $summary = $this->db->table('sales')
            ->select('
                COUNT(*) as total_sales,
                SUM(total_amount) as total_revenue,
                SUM(discount) as total_discount,
                SUM(amount_paid) as total_paid,
                AVG(total_amount) as average_sale
            ')
            ->where('sale_date >=', $startDate)
            ->where('sale_date <=', $endDate . ' 23:59:59')
            ->get()
            ->getRowArray();
        
        $data = [
            'title' => 'Monthly Sales Report',
            'year' => $year,
            'month' => $month,
            'month_name' => date('F', strtotime($startDate)),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'sales' => $sales,
            'dailyBreakdown' => $dailyBreakdown,
            'summary' => $summary
        ];
        
        return view('reports/monthly_report', $data);
    }

    // Top Selling Products
    public function topProducts()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        $limit = $this->request->getGet('limit') ?? 10;
        
        // Get top selling products using query builder
        $topProducts = $this->db->table('sale_items')
            ->select('
                products.id,
                products.name,
                products.sku,
                products.price,
                SUM(sale_items.quantity) as total_quantity,
                SUM(sale_items.subtotal) as total_revenue
            ')
            ->join('products', 'products.id = sale_items.product_id')
            ->join('sales', 'sales.id = sale_items.sale_id')
            ->where('sales.sale_date >=', $startDate)
            ->where('sales.sale_date <=', $endDate . ' 23:59:59')
            ->groupBy('products.id')
            ->orderBy('total_quantity', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
        
        $data = [
            'title' => 'Top Selling Products',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'products' => $topProducts,
            'limit' => $limit
        ];
        
        return view('reports/top_products', $data);
    }

    // Category Sales Report
    public function byCategory()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        
        // Get sales by category
        $categorySales = $this->db->table('sale_items')
            ->select('
                categories.id,
                categories.name as category_name,
                COUNT(DISTINCT sales.id) as num_transactions,
                SUM(sale_items.quantity) as total_quantity,
                SUM(sale_items.subtotal) as total_revenue
            ')
            ->join('products', 'products.id = sale_items.product_id')
            ->join('categories', 'categories.id = products.category_id')
            ->join('sales', 'sales.id = sale_items.sale_id')
            ->where('sales.sale_date >=', $startDate)
            ->where('sales.sale_date <=', $endDate . ' 23:59:59')
            ->groupBy('categories.id')
            ->orderBy('total_revenue', 'DESC')
            ->get()
            ->getResultArray();
        
        $data = [
            'title' => 'Sales by Category',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'categorySales' => $categorySales
        ];
        
        return view('reports/category_report', $data);
    }

    // Export to CSV
    public function export($type = 'sales')
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        
        $filename = $type . '_report_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        if ($type == 'sales') {
            // Sales CSV headers
            fputcsv($output, ['Invoice #', 'Date', 'Items', 'Total Amount', 'Discount', 'Amount Paid', 'Status']);
            
            $sales = $this->salesModel
                ->where('sale_date >=', $startDate)
                ->where('sale_date <=', $endDate . ' 23:59:59')
                ->orderBy('sale_date', 'DESC')
                ->findAll();
            
            foreach ($sales as $sale) {
                $itemCount = $this->saleItemsModel->where('sale_id', $sale['id'])->countAllResults();
                fputcsv($output, [
                    $sale['invoice_number'],
                    $sale['sale_date'],
                    $itemCount,
                    $sale['total_amount'],
                    $sale['discount'],
                    $sale['amount_paid'],
                    $sale['status']
                ]);
            }
        } elseif ($type == 'inventory') {
            // Inventory CSV headers
            fputcsv($output, ['ID', 'Name', 'SKU', 'Category', 'Supplier', 'Stock', 'Price', 'Cost', 'Reorder Level', 'Status']);
            
            $products = $this->productsModel->getProductsWithDetails();
            foreach ($products as $product) {
                fputcsv($output, [
                    $product['id'],
                    $product['name'],
                    $product['sku'],
                    $product['category_name'] ?? '',
                    $product['supplier_name'] ?? '',
                    $product['stock_qty'],
                    $product['price'],
                    $product['cost_price'],
                    $product['reorder_level'],
                    $product['is_active'] == 1 ? 'Active' : 'Inactive'
                ]);
            }
        }
        
        fclose($output);
        exit();
    }
}