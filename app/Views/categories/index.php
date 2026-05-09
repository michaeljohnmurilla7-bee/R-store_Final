<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Categories</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
            <li class="breadcrumb-item active">Categories</li>
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
              <h3 class="card-title">List of Categories</h3>
              <div class="float-right">
                <button type="button" class="btn btn-md btn-primary" id="addNewCategoryBtn">
                  <i class="fa fa-plus-circle"></i> Add New Category
                </button>
              </div>
            </div>
            <div class="card-body">
              <table id="categoriesTable" class="table table-bordered table-striped table-sm">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th style="display:none;">id</th>
                    <th>Category Name</th>
                    <th>Description</th>
                    <th>Products Count</th>
                    <th>Status</th>
                    <th>Created At</th>
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

<!-- Add Category Modal -->
<div class="modal fade" id="AddNewModal" tabindex="-1" role="dialog" aria-labelledby="AddNewModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">
          <i class="fa fa-plus-circle"></i> Add New Category
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="addCategoryForm">
        <?= csrf_field() ?>
        <div class="modal-body">
          <div class="form-group">
            <label>Category Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" placeholder="Enter category name" required>
          </div>

          <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Category description (optional)"></textarea>
          </div>

          <div class="form-group">
            <label>Status</label>
            <select name="is_active" class="form-control">
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fa fa-times"></i> Cancel
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> Save Category
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">
          <i class="fa fa-edit"></i> Edit Category
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="editCategoryForm">
        <?= csrf_field() ?>
        <div class="modal-body">
          <input type="hidden" id="categoryId" name="id">
          
          <div class="form-group">
            <label>Category Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Description</label>
            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
          </div>

          <div class="form-group">
            <label>Status</label>
            <select name="is_active" id="is_active" class="form-control">
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fa fa-times"></i> Cancel
          </button>
          <button type="submit" class="btn btn-warning">
            <i class="fa fa-save"></i> Update Category
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- View Category Modal -->
<div class="modal fade" id="viewCategoryModal" tabindex="-1" role="dialog" aria-labelledby="viewCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">
          <i class="fa fa-info-circle"></i> Category Details
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-borderless">
          <tr><th width="35%">Category Name:</th><td id="view_name">-</td></tr>
          <tr><th>Description:</th><td id="view_description">-</td></tr>
          <tr><th>Products Count:</th><td id="view_products_count">-</td></tr>
          <tr><th>Status:</th><td id="view_status">-</td></tr>
          <tr><th>Created At:</th><td id="view_created_at">-</td></tr>
          <tr><th>Last Updated:</th><td id="view_updated_at">-</td></tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fa fa-times"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Warning Modal for Delete with Products -->
<div class="modal fade" id="warningModal" tabindex="-1" role="dialog" aria-labelledby="warningModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">
          <i class="fa fa-exclamation-triangle"></i> Cannot Delete Category
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>This category has products associated with it. Please reassign or delete the products first before deleting this category.</p>
        <p class="mb-0"><strong>Products in this category:</strong></p>
        <ul id="productsList" class="mt-2"></ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fa fa-times"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>

<div class="toasts-top-right fixed" style="position: fixed; top: 1rem; right: 1rem; z-index: 9999;"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<meta name="csrf-token-name" content="<?= csrf_token() ?>">
<meta name="csrf-token-hash" content="<?= csrf_hash() ?>">
<script>
const baseUrl = "<?= base_url() ?>";

// Use vanilla JavaScript to open modal instead of data-toggle
document.getElementById('addNewCategoryBtn').addEventListener('click', function() {
    // Reset form
    document.getElementById('addCategoryForm').reset();
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
<script src="<?= base_url('js/categories/categories.js') ?>"></script>
<?= $this->endSection() ?>