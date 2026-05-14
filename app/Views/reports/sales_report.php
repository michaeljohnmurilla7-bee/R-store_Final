<?= $this->extend('theme/template') ?>

<?= $this->section('styles') ?>
<style>
    /* Grey Card Colors - Matching Dashboard */
    .bg-grey-light {
        background: linear-gradient(135deg, #e8e8e8 0%, #d4d4d4 100%) !important;
    }
    .bg-grey-medium {
        background: linear-gradient(135deg, #e0e0e0 0%, #cccccc 100%) !important;
    }
    .bg-grey-dark {
        background: linear-gradient(135deg, #d8d8d8 0%, #c4c4c4 100%) !important;
    }
    .bg-grey-charcoal {
        background: linear-gradient(135deg, #d0d0d0 0%, #bbbbbb 100%) !important;
    }
    
    /* Text colors for grey cards */
    .bg-grey-light h3, .bg-grey-light p,
    .bg-grey-medium h3, .bg-grey-medium p,
    .bg-grey-dark h3, .bg-grey-dark p,
    .bg-grey-charcoal h3, .bg-grey-charcoal p {
        color: #1a1a1a !important;
    }
    
    /* Icon styling */
    .small-box .icon {
        font-size: 50px;
        position: absolute;
        right: 15px;
        top: 15px;
        opacity: 0.7;
    }
    
    /* Icon Grey Color */
    .icon-grey {
        color: #888888 !important;
    }
    
    /* Button Styles */
    .btn-primary {
        background: #6c757d !important;
        border-color: #6c757d !important;
    }
    .btn-primary:hover {
        background: #5a6268 !important;
        border-color: #5a6268 !important;
    }
    .btn-success {
        background: #28a745 !important;
        border-color: #28a745 !important;
    }
    .btn-success:hover {
        background: #1a5a2a !important;
        border-color: #1a5a2a !important;
    }
    
    /* Table Styles */
    .table {
        color: #333333;
    }
    .table thead th {
        background: #f5f5f5;
        color: #1a1a1a;
        font-weight: 600;
        border-bottom: 2px solid #28a745;
    }
    .table tbody tr:hover {
        background: #f8f9fa;
    }
    
    /* Badge Styles */
    .badge-success {
        background: #28a745;
        padding: 5px 10px;
        border-radius: 20px;
    }
    
    /* Card Header */
    .card-header {
        background: #f8f9fa;
        border-bottom: 2px solid #28a745;
    }
    .card-header h3 {
        color: #1a1a1a;
        font-weight: 600;
    }
    
    /* Form Controls */
    .form-control {
        border-color: #dddddd;
    }
    .form-control:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Sales Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('reports') ?>">Reports</a></li>
                        <li class="breadcrumb-item active">Sales Report</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Date Filter -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter by Date Range</h3>
                </div>
                <div class="card-body">
                    <form method="get" class="form-inline">
                        <div class="form-group mr-2">
                            <label class="mr-2">Start Date:</label>
                            <input type="date" name="start_date" class="form-control" value="<?= $start_date ?? date('Y-m-01') ?>">
                        </div>
                        <div class="form-group mr-2">
                            <label class="mr-2">End Date:</label>
                            <input type="date" name="end_date" class="form-control" value="<?= $end_date ?? date('Y-m-d') ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Generate Report
                        </button>
                        <a href="<?= base_url('reports/export/sales?start_date=' . ($start_date ?? date('Y-m-01')) . '&end_date=' . ($end_date ?? date('Y-m-d'))) ?>" class="btn btn-success ml-2">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                    </form>
                </div>
            </div>

            <!-- Summary Cards - All Grey Colors -->
            <div class="row">
                <!-- Total Transactions - Light Grey -->
                <div class="col-md-3 col-6">
                    <div class="small-box bg-grey-light">
                        <div class="inner">
                            <h3><?= number_format($summary['total_sales'] ?? 0) ?></h3>
                            <p>Total Transactions</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shopping-cart fa-3x icon-grey"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Revenue - Medium Grey -->
                <div class="col-md-3 col-6">
                    <div class="small-box bg-grey-medium">
                        <div class="inner">
                            <h3>₱<?= number_format($summary['total_revenue'] ?? 0, 2) ?></h3>
                            <p>Total Revenue</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill-wave fa-3x icon-grey"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Discounts - Dark Grey -->
                <div class="col-md-3 col-6">
                    <div class="small-box bg-grey-dark">
                        <div class="inner">
                            <h3>₱<?= number_format($summary['total_discount'] ?? 0, 2) ?></h3>
                            <p>Total Discounts</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-tags fa-3x icon-grey"></i>
                        </div>
                    </div>
                </div>

                <!-- Average Sale - Charcoal Grey -->
                <div class="col-md-3 col-6">
                    <div class="small-box bg-grey-charcoal">
                        <div class="inner">
                            <h3>₱<?= number_format($summary['average_sale'] ?? 0, 2) ?></h3>
                            <p>Average Sale</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line fa-3x icon-grey"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sales Transactions</h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total Amount</th>
                                <th>Discount</th>
                                <th>Amount Paid</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($sales)): ?>
                                <?php foreach ($sales as $sale): ?>
                                    <tr>
                                        <td><?= $sale['invoice_number'] ?>
                                        <td><?= date('Y-m-d H:i:s', strtotime($sale['sale_date'])) ?>
                                        <td><?= $sale['item_count'] ?? 0 ?>
                                        <td>₱<?= number_format($sale['total_amount'], 2) ?>
                                        <td>₱<?= number_format($sale['discount'] ?? 0, 2) ?>
                                        <td>₱<?= number_format($sale['amount_paid'], 2) ?>
                                        <td><span class="badge badge-success"><?= $sale['status'] ?></span>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No sales found for this period</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>