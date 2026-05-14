<?= $this->extend('theme/template') ?>

<?= $this->section('styles') ?>
<style>
    .chart-container {
        position: relative;
        height: 300px;
        margin: 20px 0;
    }
    .small-box {
        border-radius: 10px;
        transition: transform 0.3s;
    }
    .small-box:hover {
        transform: translateY(-5px);
    }
    .card-header h3 {
        font-size: 1.1rem;
    }
    #salesTrendChart {
    display: block !important;
    width: 100% !important;
    min-height: 300px !important;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><b>DASHBOARD</b></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
           <!-- Summary Cards Row - Grayish Theme with Black Fonts -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box" style="background: linear-gradient(135deg, #e8e8e8 0%, #d4d4d4 100%); box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <div class="inner">
                <h3 style="color: #000000; font-weight: 600;"><?= $total_products ?? 0 ?></h3>
                <p style="color: #333333; font-weight: 500;">PRODUCTS</p>
            </div>
            <div class="icon">
                <i class="fas fa-boxes" style="color: #05754ae6;"></i>
            </div>
            <a href="<?= base_url('products') ?>" class="small-box-footer" style="background: rgba(0,0,0,0.05); color: #01230f;">
                View Products <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
    <div class="small-box" style="background: linear-gradient(135deg, #e0e0e0 0%, #cccccc 100%); box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div class="inner">
            <h3 style="color: #000000; font-weight: 600;"><?= $total_categories ?? 0 ?></h3>
            <p style="color: #333333; font-weight: 500;">CATEGORIES</p>
        </div>
        <div class="icon">
            <i class="fas fa-layer-group" style="color: #05754ae6;"></i>
        </div>
        <a href="<?= base_url('categories') ?>" class="small-box-footer" style="background: rgba(0,0,0,0.05); color: #02390f;">
            View Categories <i class="fas fa-arrow-circle-right" style="color: #01230f;"></i>
        </a>
    </div>
</div>

    <div class="col-lg-3 col-6">
        <div class="small-box" style="background: linear-gradient(135deg, #d8d8d8 0%, #c4c4c4 100%); box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <div class="inner">
                <h3 style="color: #000000; font-weight: 600;"><?= $total_suppliers ?? 0 ?></h3>
                <p style="color: #333333; font-weight: 500;">SUPPLIERS</p>
            </div>
            <div class="icon">
                <i class="fas fa-truck" style="color: #05754ae6;"></i>
            </div>
            <a href="<?= base_url('suppliers') ?>" class="small-box-footer" style="background: rgba(0,0,0,0.05); color: #01230f;">
                View Suppliers <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box" style="background: linear-gradient(135deg, #d0d0d0 0%, #bbbbbb 100%); box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <div class="inner">
                <h3 style="color: #000000; font-weight: 600;"><?= $total_sales ?? 0 ?></h3>
                <p style="color: #333333; font-weight: 500;">SALES</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart" style="color: #05754ae6;"></i>
            </div>
            <a href="<?= base_url('sales') ?>" class="small-box-footer" style="background: rgba(0,0,0,0.05); color: #01230f;">
                View Sales <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

            <!-- Second Row of Cards -->
            <!-- Second Row of Cards - Grayish Theme with Black Fonts -->
<div class="row">
    <!-- Total Revenue Card -->
    <div class="col-lg-3 col-6">
        <div class="small-box" style="background: linear-gradient(135deg, #e5e5e5 0%, #d0d0d0 100%); box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <div class="inner">
                <h3 style="color: #000000; font-weight: 600;">₱<?= number_format($total_revenue ?? 0, 2) ?></h3>
                <p style="color: #333333; font-weight: 500;">TOTAL REVENUE</p>
            </div>
            <div class="icon">
                <i class="fas fa-money-bill-wave fa-3x" style="color: #05754ae6;"></i>
            </div>
            <a href="" class="small-box-footer" style="background: rgba(0,0,0,0.05); color: #01230f;">
                REVENUE <i class="" style="color: #01230f;"></i>
            </a>
        </div>
    </div>

    <!-- Customers Card -->
    <div class="col-lg-3 col-6">
        <div class="small-box" style="background: linear-gradient(135deg, #ddd 0%, #c8c8c8 100%); box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <div class="inner">
                <h3 style="color: #000000; font-weight: 600;"><?= $total_customers ?? 0 ?></h3>
                <p style="color: #333333; font-weight: 500;">CUSTOMERS</p>
            </div>
            <div class="icon">
                <i class="fas fa-users fa-3x" style="color: #05754ae6;"></i>
            </div>
            <a href="<?= base_url('customers') ?>" class="small-box-footer" style="background: rgba(0,0,0,0.05); color: #01230f;">
                View Customers <i class="fas fa-arrow-circle-right" style="color: #01230f;"></i>
            </a>
        </div>
    </div>

    <!-- Reports -->
<div class="col-lg-3 col-6">
    <div class="small-box" style="background: linear-gradient(135deg, #ddd 0%, #c8c8c8 100%); box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div class="inner">
            <h3 style="color: #000000; font-weight: 600;"><?= $total_reports ?? 0 ?></h3>
            <p style="color: #333333; font-weight: 500;">REPORTS</p>
        </div>
        <div class="icon">
            <i class="fas fa-chart-bar fa-3x" style="color: #05754ae6;"></i>
        </div>
        <a href="<?= base_url('reports') ?>" class="small-box-footer" style="background: rgba(0,0,0,0.05); color: #01230f;">
            View Reports <i class="fas fa-arrow-circle-right" style="color: #01230f;"></i>
        </a>
    </div>
</div>
</div>
            <!-- Charts Row -->
<div class="row">
    <!-- Sales Trend Chart -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line"></i> Sales Trend
                </h3>
                <div class="card-tools">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-default trend-filter active" data-days="7">7 Days</button>
                        <button type="button" class="btn btn-default trend-filter" data-days="15">15 Days</button>
                        <button type="button" class="btn btn-default trend-filter" data-days="30">30 Days</button>
                        <button type="button" class="btn btn-default trend-filter" data-days="90">90 Days</button>
                        <button type="button" class="btn btn-default" id="customRangeBtn">Custom</button>
                    </div>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Custom Date Range Picker -->
                <div id="customDateRange" style="display: none; margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                    <div class="row">
                        <div class="col-md-5">
                            <label>Start Date:</label>
                            <input type="date" id="startDate" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-5">
                            <label>End Date:</label>
                            <input type="date" id="endDate" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <button type="button" id="applyCustomRange" class="btn btn-success btn-sm form-control">Apply</button>
                        </div>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="salesTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products Chart -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-trophy"></i> Top Selling Products
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

            <!-- Second Row of Charts -->
            <div class="row">
                <!-- Payment Status Chart -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie"></i> Payment Status Distribution
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="paymentStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Sales Table -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-clock"></i> Recent Sales
                            </h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recent_sales)): ?>
                                        <?php foreach ($recent_sales as $sale): ?>
                                            <tr>
                                                <td><?= $sale['invoice_number'] ?>
                                                <td><?= date('M d, Y', strtotime($sale['sale_date'])) ?>
                                                <td>₱<?= number_format($sale['total_amount'], 2) ?>
                                                <td><span class="badge badge-success"><?= $sale['status'] ?></span>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No recent sales</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let salesChart = null;
let currentDays = 7;

// TEST VERSION - Simplified chart
async function loadSalesTrend(days = 7, startDate = null, endDate = null) {
    try {
        let url = '<?= base_url("dashboard/getSalesChartDataFiltered") ?>?';
        if (startDate && endDate) {
            url += `start_date=${startDate}&end_date=${endDate}`;
        } else {
            url += `days=${days}`;
        }
        
        console.log('URL:', url);
        const response = await fetch(url);
        const result = await response.json();
        
        console.log('FULL RESULT:', result);
        
        if (result.status === 'success') {
            console.log('Labels:', result.labels);
            console.log('Counts:', result.counts);
            console.log('Revenues:', result.revenues);
            
            const ctx = document.getElementById('salesTrendChart').getContext('2d');
            
            if (salesChart) salesChart.destroy();
            
            // Force a simple chart
            salesChart = new Chart(ctx, {
                type: 'bar',  // Try bar chart instead of line
                data: {
                    labels: result.labels,
                    datasets: [{
                        label: 'Number of Sales',
                        data: result.counts,
                        backgroundColor: '#007bff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('salesTrendChart').parentElement.innerHTML = '<div class="alert alert-danger">Chart Error: ' + error.message + '</div>';
    }
}

// Top Products Chart
async function loadTopProducts() {
    try {
        const response = await fetch('<?= base_url("dashboard/getTopProductsChartData") ?>');
        const result = await response.json();
        
        if (result.status === 'success' && result.labels.length > 0) {
            const ctx = document.getElementById('topProductsChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: result.labels,
                    datasets: [{
                        label: 'Units Sold',
                        data: result.values,
                        backgroundColor: '#28a745',
                        borderRadius: 8,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { color: '#333333' } }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Quantity Sold', color: '#666666' },
                            grid: { color: 'rgba(0, 0, 0, 0.05)' },
                            ticks: { color: '#666666' }
                        },
                        x: { ticks: { color: '#666666' }, grid: { display: false } }
                    }
                }
            });
        } else {
            document.getElementById('topProductsChart').parentElement.innerHTML = '<div class="text-center text-muted p-4">No sales data available</div>';
        }
    } catch (error) {
        console.error('Error loading top products:', error);
    }
}

// Payment Status Chart
async function loadPaymentStatus() {
    try {
        const response = await fetch('<?= base_url("dashboard/getPaymentStatusData") ?>');
        const result = await response.json();
        
        if (result.status === 'success' && result.labels.length > 0) {
            const ctx = document.getElementById('paymentStatusChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: result.labels,
                    datasets: [{
                        data: result.values,
                        backgroundColor: result.colors || ['#28a745', '#ffc107', '#dc3545'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { color: '#333333', usePointStyle: true, boxWidth: 10 } }
                    }
                }
            });
        } else {
            document.getElementById('paymentStatusChart').parentElement.innerHTML = '<div class="text-center text-muted p-4">No payment data available</div>';
        }
    } catch (error) {
        console.error('Error loading payment status:', error);
    }
}

// Auto-refresh when page becomes visible (coming back from Sales)
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        console.log('Page visible - refreshing chart data');
        loadSalesTrend(currentDays);
        loadTopProducts();
        loadPaymentStatus();
    }
});

// Refresh when coming from back/forward navigation
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        console.log('Page from cache - refreshing');
        loadSalesTrend(currentDays);
        loadTopProducts();
        loadPaymentStatus();
    }
});

// Filter button event listeners
document.querySelectorAll('.trend-filter').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.trend-filter').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        currentDays = parseInt(this.getAttribute('data-days'));
        loadSalesTrend(currentDays);
        document.getElementById('customDateRange').style.display = 'none';
    });
});

// Custom range button
document.getElementById('customRangeBtn').addEventListener('click', function() {
    const rangeDiv = document.getElementById('customDateRange');
    rangeDiv.style.display = rangeDiv.style.display === 'none' ? 'block' : 'none';
});

// Apply custom range
document.getElementById('applyCustomRange').addEventListener('click', function() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    if (startDate && endDate) {
        loadSalesTrend(null, startDate, endDate);
        document.getElementById('customDateRange').style.display = 'none';
    } else {
        alert('Please select both start and end dates');
    }
});

// Load all charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadSalesTrend(7);
    loadTopProducts();
    loadPaymentStatus();
});
</script>
<?= $this->endSection() ?>