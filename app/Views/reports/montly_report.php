<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Monthly Sales Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('reports') ?>">Reports</a></li>
                        <li class="breadcrumb-item active">Monthly Sales</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Month Picker -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Select Month</h3>
                </div>
                <div class="card-body">
                    <form method="get" action="<?= base_url('reports/monthly') ?>" class="form-inline">
                        <div class="form-group mr-2">
                            <label class="mr-2">Year:</label>
                            <select name="year" class="form-control">
                                <?php for ($y = date('Y') - 2; $y <= date('Y'); $y++): ?>
                                    <option value="<?= $y ?>" <?= ($year ?? date('Y')) == $y ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="form-group mr-2">
                            <label class="mr-2">Month:</label>
                            <select name="month" class="form-control">
                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                    <option value="<?= sprintf('%02d', $m) ?>" <?= ($month ?? date('m')) == sprintf('%02d', $m) ? 'selected' : '' ?>>
                                        <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Generate Report
                        </button>
                    </form>
                </div>
            </div>

            <!-- Summary -->
            <div class="row">
                <div class="col-md-4">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $summary['total_sales'] ?? 0 ?></h3>
                            <p>Total Transactions</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>₱<?= number_format($summary['total_revenue'] ?? 0, 2) ?></h3>
                            <p>Total Revenue</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>₱<?= number_format($summary['average_sale'] ?? 0, 2) ?></h3>
                            <p>Average Sale</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Breakdown Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daily Breakdown for <?= $month_name ?? '' ?> <?= $year ?? '' ?></h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Number of Sales</th>
                                <th>Total Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($dailyBreakdown)): ?>
                                <?php foreach ($dailyBreakdown as $day): ?>
                                    <tr>
                                        <td><?= date('F d, Y', strtotime($day['sale_day'])) ?></td>
                                        <td><?= $day['count'] ?></td>
                                        <td>₱<?= number_format($day['total'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">No sales found for this month</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Detailed Sales Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detailed Sales Transactions</h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($sales)): ?>
                                <?php foreach ($sales as $sale): ?>
                                    <tr>
                                        <td><?= $sale['invoice_number'] ?></td>
                                        <td><?= date('F d, Y', strtotime($sale['sale_date'])) ?></td>
                                        <td><?= $sale['item_count'] ?? 0 ?></td>
                                        <td>₱<?= number_format($sale['total_amount'], 2) ?></td>
                                        <td><span class="badge badge-success"><?= $sale['status'] ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No sales found for this month</td>
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