<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Suppliers</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Suppliers</li>
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
              <h3 class="card-title">List of Suppliers</h3>
              <div class="float-right">
                <button type="button" class="btn btn-md btn-primary" id="addNewSupplierBtn">
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
                    <th>Contact Person</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
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

<!-- Add Supplier Modal -->
<div class="modal fade" id="AddNewModal" tabindex="-1" role="dialog" aria-labelledby="AddNewModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">
          <i class="fa fa-plus-circle"></i> Add New Supplier
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="addSupplierForm">
        <?= csrf_field() ?>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Supplier Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="Enter supplier name" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Contact Person</label>
                <input type="text" name="contact_person" class="form-control" placeholder="Contact person name">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" placeholder="supplier@example.com" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Phone <span class="text-danger">*</span></label>
                <input type="text" name="phone" class="form-control" placeholder="Phone number" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="2" placeholder="Supplier address"></textarea>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="city" class="form-control" placeholder="City">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>State/Province</label>
                <input type="text" name="state" class="form-control" placeholder="State/Province">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Postal Code</label>
                <input type="text" name="postal_code" class="form-control" placeholder="Postal code">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Country</label>
                <input type="text" name="country" class="form-control" placeholder="Country">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Tax Number/VAT</label>
                <input type="text" name="tax_number" class="form-control" placeholder="Tax/VAT number">
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
            <label>Notes</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Additional notes (optional)"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fa fa-times"></i> Cancel
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> Save Supplier
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Supplier Modal -->
<div class="modal fade" id="editSupplierModal" tabindex="-1" role="dialog" aria-labelledby="editSupplierModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title">
          <i class="fa fa-edit"></i> Edit Supplier
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="editSupplierForm">
        <?= csrf_field() ?>
        <div class="modal-body">
          <input type="hidden" id="supplierId" name="id">
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Supplier Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Contact Person</label>
                <input type="text" name="contact_person" id="contact_person" class="form-control">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Email <span class="text-danger">*</span></label>
                <input type="email" name="email" id="email" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Phone <span class="text-danger">*</span></label>
                <input type="text" name="phone" id="phone" class="form-control" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label>Address</label>
                <textarea name="address" id="address" class="form-control" rows="2"></textarea>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="city" id="city" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>State/Province</label>
                <input type="text" name="state" id="state" class="form-control">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Postal Code</label>
                <input type="text" name="postal_code" id="postal_code" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Country</label>
                <input type="text" name="country" id="country" class="form-control">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Tax Number/VAT</label>
                <input type="text" name="tax_number" id="tax_number" class="form-control">
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
            <label>Notes</label>
            <textarea name="notes" id="notes" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fa fa-times"></i> Cancel
          </button>
          <button type="submit" class="btn btn-warning">
            <i class="fa fa-save"></i> Update Supplier
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- View Supplier Modal -->
<div class="modal fade" id="viewSupplierModal" tabindex="-1" role="dialog" aria-labelledby="viewSupplierModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">
          <i class="fa fa-info-circle"></i> Supplier Details
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <table class="table table-borderless">
              <tr><th width="40%">Supplier Name:</th><td id="view_name">-</td></tr>
              <tr><th>Contact Person:</th><td id="view_contact_person">-</td></tr>
              <tr><th>Email:</th><td id="view_email">-</td></tr>
              <tr><th>Phone:</th><td id="view_phone">-</td></tr>
              <tr><th>Tax Number/VAT:</th><td id="view_tax_number">-</td></tr>
            </table>
          </div>
          <div class="col-md-6">
            <table class="table table-borderless">
              <tr><th width="40%">Status:</th><td id="view_status">-</td></tr>
              <tr><th>Created At:</th><td id="view_created_at">-</td></tr>
              <tr><th>Last Updated:</th><td id="view_updated_at">-</td></tr>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <hr>
            <strong>Address:</strong>
            <p id="view_address" class="text-muted mt-2">-</p>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <strong>Notes:</strong>
            <p id="view_notes" class="text-muted mt-2">-</p>
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

<div class="toasts-top-right fixed" style="position: fixed; top: 1rem; right: 1rem; z-index: 9999;"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const baseUrl = "<?= base_url() ?>";

// Use vanilla JavaScript to open modal instead of data-toggle
document.getElementById('addNewSupplierBtn').addEventListener('click', function() {
    // Reset form
    document.getElementById('addSupplierForm').reset();
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
<script src="<?= base_url('js/suppliers/suppliers.js') ?>"></script>
<?= $this->endSection() ?>