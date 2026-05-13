<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Top Selling Products</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('reports') ?>">Reports</a></li>
                        <li class="breadcrumb-item active">Top Products</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Date Range Filter -->
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
                        <div class="form-group mr-2">
                            <label class="mr-2">Limit:</label>
                            <select name="limit" class="form-control">
                                <option value="5">Top 5</option>
                                <option value="10" selected>Top 10</option>
                                <option value="20">Top 20</option>
                                <option value="50">Top 50</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Generate Report
                        </button>
                    </form>
                </div>
            </div>

            <!-- Products Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top Selling Products (<?= date('F d, Y', strtotime($start_date ?? date('Y-m-01'))) ?> - <?= date('F d, Y', strtotime($end_date ?? date('Y-m-d'))) ?>)</h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Product Name</th>
                                <th>SKU</th>
                                <th>Unit Price</th>
                                <th>Total Quantity Sold</th>
                                <th>Total Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($products)): ?>
                                <?php $rank = 1; ?>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><strong>#<?= $rank++ ?></strong></td>
                                        <td><?= $product['name'] ?></td>
                                        <td><?= $product['sku'] ?></td>
                                        <td>₱<?= number_format($product['price'], 2) ?></td>
                                        <td><?= $product['total_quantity'] ?> units</td>
                                        <td>₱<?= number_format($product['total_revenue'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No sales found for this period</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Chart Section (Optional) -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sales Chart</h3>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" style="height: 300px; width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
<?php if (!empty($products)): ?>
    const productNames = <?= json_encode(array_column($products, 'name')) ?>;
    const quantities = <?= json_encode(array_column($products, 'total_quantity')) ?>;
    const revenues = <?= json_encode(array_column($products, 'total_revenue')) ?>;
    
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: productNames,
            datasets: [
                {
                    label: 'Quantity Sold',
                    data: quantities,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    yAxisID: 'y'
                },
                {
                    label: 'Revenue (₱)',
                    data: revenues,
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Quantity Sold' }
                },
                y1: {
                    position: 'right',
                    beginAtZero: true,
                    title: { display: true, text: 'Revenue (₱)' }
                }
            }
        }
    });
<?php endif; ?>
</script>
<?= $this->endSection() ?>