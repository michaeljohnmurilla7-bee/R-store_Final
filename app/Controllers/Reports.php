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
    
    // FIX: Get item count for each sale - SUM quantities
    foreach ($sales as &$sale) {
        $totalQuantity = $this->saleItemsModel
            ->select('SUM(quantity) as total')
            ->where('sale_id', $sale['id'])
            ->get()
            ->getRowArray();
        
        $sale['item_count'] = $totalQuantity['total'] ?? 0;  // ← FIXED
    }
    
    // Rest of your code...
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
 // Get the date
    if ($this->request->getGet('date')) {
        $date = $this->request->getGet('date');
    } elseif ($date === null) {
        $date = date('Y-m-d');
    }
    
    // Get sales for the day
    $sales = $this->salesModel
        ->where('DATE(sale_date)', $date)
        ->orderBy('id', 'DESC')
        ->findAll();
    
    foreach ($sales as &$sale) {
        // Get total quantity
        $totalQuantity = $this->saleItemsModel
            ->select('SUM(quantity) as total')
            ->where('sale_id', $sale['id'])
            ->get()
            ->getRowArray();
        
        $sale['item_count'] = $totalQuantity['total'] ?? 0;
        
        // Get product names for this sale
        $products = $this->saleItemsModel
            ->select('products.name')
            ->join('products', 'products.id = sale_items.product_id')
            ->where('sale_id', $sale['id'])
            ->findAll();
        
        $productNames = array_column($products, 'name');
        if (count($productNames) > 1) {
            $sale['product_name'] = $productNames[0] . ' +' . (count($productNames) - 1);
        } else {
            $sale['product_name'] = $productNames[0] ?? 'N/A';
        }
    }
    
    // Get daily summary
    $summary = $this->db->table('sales')
        ->select('
            COUNT(*) as total_sales,
            SUM(total_amount) as total_revenue,
            SUM(discount) as total_discount,
            SUM(amount_paid) as total_paid
        ')
        ->where('DATE(sale_date)', $date)
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
    
    // FIX: Get total quantity for each sale
    foreach ($sales as &$sale) {
        $totalQuantity = $this->saleItemsModel
            ->select('SUM(quantity) as total')
            ->where('sale_id', $sale['id'])
            ->get()
            ->getRowArray();
        
        $sale['item_count'] = $totalQuantity['total'] ?? 0;  // ← FIXED
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
    
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    
    $output = fopen('php://output', 'w');
    
    // Add UTF-8 BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    if ($type == 'sales') {
        // Sales CSV headers
        fputcsv($output, ['Invoice #', 'Date', 'Items', 'Total Amount', 'Discount', 'Amount Paid', 'Status']);
        
        $sales = $this->salesModel
            ->where('sale_date >=', $startDate)
            ->where('sale_date <=', $endDate . ' 23:59:59')
            ->orderBy('sale_date', 'DESC')
            ->findAll();
        
        foreach ($sales as $sale) {
            // FIX: SUM quantities instead of COUNT rows
            $totalQuantity = $this->saleItemsModel
                ->select('SUM(quantity) as total')
                ->where('sale_id', $sale['id'])
                ->get()
                ->getRowArray();
            
            $itemCount = $totalQuantity['total'] ?? 0;
            
            fputcsv($output, [
                $sale['invoice_number'],
                date('Y-m-d H:i:s', strtotime($sale['sale_date'])),
                $itemCount,
                number_format($sale['total_amount'], 2),
                number_format($sale['discount'], 2),
                number_format($sale['amount_paid'], 2),
                ucfirst($sale['status'])
            ]);
        }
    } elseif ($type == 'inventory') {
        // Inventory CSV headers
        fputcsv($output, ['ID', 'Name', 'SKU', 'Category', 'Supplier', 'Stock', 'Selling Price', 'Cost Price', 'Reorder Level', 'Status']);
        
        $products = $this->productsModel->getProductsWithDetails();
        foreach ($products as $product) {
            fputcsv($output, [
                $product['id'],
                $product['name'],
                $product['sku'],
                $product['category_name'] ?? 'Uncategorized',
                $product['supplier_name'] ?? 'No Supplier',
                $product['stock_qty'] ?? 0,
                number_format($product['price'], 2),
                number_format($product['cost_price'] ?? 0, 2),
                $product['reorder_level'] ?? 0,
                $product['is_active'] == 1 ? 'Active' : 'Inactive'
            ]);
        }
    }
    
    fclose($output);
    exit();
}
}