<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Inventory Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('reports') ?>">Reports</a></li>
                        <li class="breadcrumb-item active">Inventory</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Summary Cards -->
            <div class="row">
                <div class="col-md-4">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $totalProducts ?? 0 ?></h3>
                            <p>Total Products</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>₱<?= number_format($totalStockValue ?? 0, 2) ?></h3>
                            <p>Total Stock Value (Selling)</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>₱<?= number_format($totalCostValue ?? 0, 2) ?></h3>
                            <p>Total Cost Value</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alert -->
            <?php if (!empty($lowStock)): ?>
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle"></i> Low Stock Alert
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        The following products are running low on stock (stock ≤ reorder level):
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Current Stock</th>
                                <th>Reorder Level</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lowStock as $product): ?>
                                <tr>
                                    <td><?= $product['name'] ?>?</td>
                                    <td class="text-danger"><?= $product['stock_qty'] ?></td>
                                    <td><?= $product['reorder_level'] ?></td>
                                    <td><span class="badge badge-warning">Low Stock</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Out of Stock -->
            <?php if (!empty($outOfStock)): ?>
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-times-circle"></i> Out of Stock Products
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($outOfStock as $product): ?>
                                <tr>
                                    <td><?= $product['name'] ?>?</td>
                                    <td><?= $product['sku'] ?></td>
                                    <td><span class="badge badge-danger">Out of Stock</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- All Products -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Products Inventory</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('reports/export/inventory') ?>" class="btn btn-sm btn-success">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th>Supplier</th>
                                <th>Stock</th>
                                <th>Selling Price</th>
                                <th>Cost Price</th>
                                <th>Reorder Level</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($products)): ?>
                                <?php foreach ($products as $product): ?>
                                    <?php
                                    $stockClass = $product['stock_qty'] <= 0 ? 'danger' : ($product['stock_qty'] <= $product['reorder_level'] ? 'warning' : 'success');
                                    ?>
                                    <tr>
                                        <td><?= $product['id'] ?></td>
                                        <td><?= $product['name'] ?></td>
                                        <td><?= $product['sku'] ?></td>
                                        <td><?= $product['category_name'] ?? '-' ?></td>
                                        <td><?= $product['supplier_name'] ?? '-' ?></td>
                                        <td class="text-<?= $stockClass ?>"><?= $product['stock_qty'] ?></td>
                                        <td>₱<?= number_format($product['price'], 2) ?></td>
                                        <td>₱<?= number_format($product['cost_price'], 2) ?></td>
                                        <td><?= $product['reorder_level'] ?></td>
                                        <td><?= $product['is_active'] == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center">No products found</td>
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