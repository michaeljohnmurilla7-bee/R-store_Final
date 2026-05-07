<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Products</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
            <li class="breadcrumb-item active">Products</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">List of Products</h3>
              <div class="float-right">
                <button type="button" class="btn btn-md btn-primary" id="addNewProductBtn">
                  <i class="fa fa-plus-circle"></i> Add New
                </button>
              </div>
            </div>
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped table-sm">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th style="display:none;">id</th>
                    <th>Name</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Supplier</th>
                    <th>Cost Price</th>
                    <th>Selling Price</th>
                    <th>Stock</th>
                    <th>Reorder Level</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Add Product Modal - MOVED OUTSIDE content-wrapper -->
<div class="modal fade" id="AddNewModal" tabindex="-1" role="dialog" aria-labelledby="AddNewModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">
          <i class="fa fa-plus-circle"></i> Add New Product
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="addProductForm">
        <?= csrf_field() ?>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-8">
              <div class="form-group">
                <label>Product Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="Enter product name" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>SKU <span class="text-danger">*</span></label>
                <input type="text" name="sku" class="form-control" placeholder="Unique SKU" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Category <span class="text-danger">*</span></label>
                <select name="category_id" class="form-control" required>
                  <option value="">Select Category</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Supplier <span class="text-danger">*</span></label>
                <select name="supplier_id" class="form-control" required>
                  <option value="">Select Supplier</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Cost Price</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                  </div>
                  <input type="number" step="0.01" name="cost_price" class="form-control" value="0.00">
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Selling Price <span class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                  </div>
                  <input type="number" step="0.01" name="price" class="form-control" required>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Initial Stock</label>
                <input type="number" name="stock_qty" class="form-control" value="0">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Reorder Level</label>
                <input type="number" name="reorder_level" class="form-control" value="0">
                <small class="text-muted">Alert when stock falls below this level</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Status</label>
                <select name="is_active" class="form-control">
                  <option value="1">Active</option>
                  <option value="0">Inactive</option>
                </select>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Product description (optional)"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fa fa-times"></i> Cancel
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> Save Product
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title">
          <i class="fa fa-edit"></i> Edit Product
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="editProductForm">
        <?= csrf_field() ?>
        <div class="modal-body">
          <input type="hidden" id="productId" name="id">
          
          <div class="row">
            <div class="col-md-8">
              <div class="form-group">
                <label>Product Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>SKU <span class="text-danger">*</span></label>
                <input type="text" name="sku" id="sku" class="form-control" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Category <span class="text-danger">*</span></label>
                <select name="category_id" id="category_id" class="form-control" required>
                  <option value="">Select Category</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Supplier <span class="text-danger">*</span></label>
                <select name="supplier_id" id="supplier_id" class="form-control" required>
                  <option value="">Select Supplier</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Cost Price</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                  </div>
                  <input type="number" step="0.01" name="cost_price" id="cost_price" class="form-control">
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Selling Price <span class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                  </div>
                  <input type="number" step="0.01" name="price" id="price" class="form-control" required>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Current Stock</label>
                <input type="number" name="stock_qty" id="stock_qty" class="form-control" readonly>
                <small class="text-muted">Read-only</small>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Reorder Level</label>
                <input type="number" name="reorder_level" id="reorder_level" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Status</label>
                <select name="is_active" id="is_active" class="form-control">
                  <option value="1">Active</option>
                  <option value="0">Inactive</option>
                </select>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Description</label>
            <textarea name="description" id="description" class="form-control" rows="3" placeholder="Product description"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fa fa-times"></i> Cancel
          </button>
          <button type="submit" class="btn btn-warning">
            <i class="fa fa-save"></i> Update Product
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- View Product Modal -->
<div class="modal fade" id="viewProductModal" tabindex="-1" role="dialog" aria-labelledby="viewProductModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">
          <i class="fa fa-info-circle"></i> Product Details
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <table class="table table-borderless">
              <tr><th width="40%">Product Name:</th><td id="view_name">-</td></tr>
              <tr><th>SKU:</th><td id="view_sku">-</td></tr>
              <tr><th>Category:</th><td id="view_category">-</td></tr>
              <tr><th>Supplier:</th><td id="view_supplier">-</td></tr>
              <tr><th>Cost Price:</th><td id="view_cost_price">-</td></tr>
              <tr><th>Selling Price:</th><td id="view_price">-</td></tr>
            </table>
          </div>
          <div class="col-md-6">
            <table class="table table-borderless">
              <tr><th width="40%">Current Stock:</th><td id="view_stock_qty">-</td></tr>
              <tr><th>Reorder Level:</th><td id="view_reorder_level">-</td></tr>
              <tr><th>Status:</th><td id="view_status">-</td></tr>
              <tr><th>Created At:</th><td id="view_created_at">-</td></tr>
              <tr><th>Last Updated:</th><td id="view_updated_at">-</td></tr>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <hr>
            <strong>Description:</strong>
            <p id="view_description" class="text-muted mt-2">-</p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fa fa-times"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Stock Adjustment Modal -->
<div class="modal fade" id="stockAdjustmentModal" tabindex="-1" role="dialog" aria-labelledby="stockAdjustmentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title">
          <i class="fa fa-exchange-alt"></i> Adjust Stock
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="stockAdjustmentForm">
        <?= csrf_field() ?>
        <div class="modal-body">
          <input type="hidden" name="product_id" id="adjust_product_id">
          
          <div class="form-group">
            <label>Product</label>
            <input type="text" id="adjust_product_name" class="form-control" readonly>
          </div>

          <div class="form-group">
            <label>Adjustment Type</label>
            <select name="type" class="form-control" required>
              <option value="in">📥 Add Stock (Stock In)</option>
              <option value="out">📤 Remove Stock (Stock Out)</option>
            </select>
          </div>

          <div class="form-group">
            <label>Quantity</label>
            <input type="number" name="quantity" class="form-control" required min="1" placeholder="Enter quantity">
          </div>

          <div class="form-group">
            <label>Reason</label>
            <textarea name="reason" class="form-control" rows="3" placeholder="Reason for stock adjustment..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fa fa-times"></i> Cancel
          </button>
          <button type="submit" class="btn btn-warning">
            <i class="fa fa-check"></i> Apply Adjustment
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="toasts-top-right fixed" style="position: fixed; top: 1rem; right: 1rem; z-index: 9999;"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const baseUrl = "<?= base_url() ?>";

// Use vanilla JavaScript to open modal instead of data-toggle
document.getElementById('addNewProductBtn').addEventListener('click', function() {
    // Reset form
    document.getElementById('addProductForm').reset();
    // Show modal using Bootstrap 4
    $('#AddNewModal').modal({
        backdrop: 'static',
        keyboard: false,
        show: true
    });
});

// Manual modal cleanup
$(document).ready(function() {
    // Force remove any stuck backdrops on page load
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    
    // Ensure modals are properly destroyed when closed
    $('.modal').on('hidden.bs.modal', function() {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        $(this).removeData('bs.modal');
    });
    
    // Test if Bootstrap is working
    console.log('jQuery version:', $.fn.jquery);
    console.log('Bootstrap modal available:', typeof $.fn.modal !== 'undefined');
});
</script>
<script src="<?= base_url('js/products/products.js') ?>"></script>
<?= $this->endSection() ?>