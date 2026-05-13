<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Daily Sales Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('reports') ?>">Reports</a></li>
                        <li class="breadcrumb-item active">Daily Sales</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Date Picker -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Select Date</h3>
                </div>
                <div class="card-body">
                    <form method="get" action="<?= base_url('reports/daily') ?>" class="form-inline">
                        <div class="form-group mr-2">
                            <label class="mr-2">Date:</label>
                            <input type="date" name="date" class="form-control" value="<?= $date ?? date('Y-m-d') ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Generate Report
                        </button>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row">
                <div class="col-md-3">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $summary['total_sales'] ?? 0 ?></h3>
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
                            <h3>₱<?= number_format($summary['total_paid'] ?? 0, 2) ?></h3>
                            <p>Total Paid</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sales on <?= date('F d, Y', strtotime($date ?? date('Y-m-d'))) ?></h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Time</th>
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
                                        <td><?= date('h:i A', strtotime($sale['sale_date'])) ?></td>
                                        <td><?= $sale['item_count'] ?? 0 ?></td>
                                        <td>₱<?= number_format($sale['total_amount'], 2) ?></td>
                                        <td>₱<?= number_format($sale['discount'], 2) ?></td>
                                        <td>₱<?= number_format($sale['amount_paid'], 2) ?></td>
                                        <td><span class="badge badge-success"><?= $sale['status'] ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No sales found for this date</td>
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