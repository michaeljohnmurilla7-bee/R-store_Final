<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Reports Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Reports</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Sales Reports -->
                <div class="col-md-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-line"></i> Sales Reports
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <a href="<?= base_url('reports/sales') ?>" class="list-group-item list-group-item-action">
                                    <i class="fas fa-calendar mr-2"></i> Sales by Date Range
                                </a>
                                <a href="<?= base_url('reports/daily') ?>" class="list-group-item list-group-item-action">
                                    <i class="fas fa-calendar-day mr-2"></i> Daily Sales Report
                                </a>
                                <a href="<?= base_url('reports/top-products') ?>" class="list-group-item list-group-item-action">
                                    <i class="fas fa-trophy mr-2"></i> Top Selling Products
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inventory Reports -->
                <div class="col-md-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-boxes"></i> Inventory Reports
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <a href="<?= base_url('reports/inventory') ?>" class="list-group-item list-group-item-action">
                                    <i class="fas fa-list mr-2"></i> Current Inventory Status
                                </a>
                                <a href="<?= base_url('reports/inventory') ?>#low-stock" class="list-group-item list-group-item-action">
                                    <i class="fas fa-exclamation-triangle mr-2 text-warning"></i> Low Stock Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Section -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-download"></i> Export Reports
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="<?= base_url('reports/export/sales') ?>" class="btn btn-info btn-block">
                                        <i class="fas fa-file-csv"></i> Export Sales to CSV
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="<?= base_url('reports/export/inventory') ?>" class="btn btn-info btn-block">
                                        <i class="fas fa-file-csv"></i> Export Inventory to CSV
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>