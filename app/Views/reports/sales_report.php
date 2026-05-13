<?= $this->extend('theme/template') ?>

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

            <!-- Summary Cards -->
            <div class="row">
                <div class="col-md-3">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= number_format($summary['total_sales'] ?? 0) ?></h3>
                            <p>Total Transactions</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>₱<?= number_format($summary['total_revenue'] ?? 0, 2) ?></h3>
                            <p>Total Revenue</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>₱<?= number_format($summary['total_discount'] ?? 0, 2) ?></h3>
                            <p>Total Discounts</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-tags"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>₱<?= number_format($summary['average_sale'] ?? 0, 2) ?></h3>
                            <p>Average Sale</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line"></i>
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
                                        <td><?= $sale['invoice_number'] ?></td>
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