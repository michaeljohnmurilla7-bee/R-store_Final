<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Customers</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
            <li class="breadcrumb-item active">Customers</li>
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
              <h3 class="card-title">List of Customers</h3>
              <div class="float-right">
                <button type="button" class="btn btn-md btn-primary" id="addNewCustomerBtn">
                  <i class="fa fa-plus-circle"></i> Add New
                </button>
                <button type="button" class="btn btn-md btn-success ml-2" id="exportBtn">
                  <i class="fa fa-file-export"></i> Export
                </button>
                <button type="button" class="btn btn-md btn-info ml-2" id="importBtn">
                  <i class="fa fa-file-import"></i> Import
                </button>
              </div>
            </div>
            <div class="card-body">
              <table id="customersTable" class="table table-bordered table-striped table-sm">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Created At</th>
                    <th>Updated At</th>
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

<!-- Add Customer Modal -->
<div class="modal fade" id="AddNewModal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">
          <i class="fa fa-plus-circle"></i> Add New Customer
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="addCustomerForm">
        <?= csrf_field() ?>
        <div class="modal-body">
          <div class="form-group">
            <label>Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" placeholder="Enter customer name" required>
          </div>
          
          <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" placeholder="Enter phone number">
          </div>
          
          <div class="form-group">
            <label>Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" placeholder="Enter email address" required>
          </div>
          
          <div class="form-group">
            <label>Address</label>
            <textarea name="address" class="form-control" rows="3" placeholder="Enter address"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fa fa-times"></i> Cancel
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> Save Customer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Customer Modal -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">
          <i class="fa fa-edit"></i> Edit Customer
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="editCustomerForm">
        <?= csrf_field() ?>
        <div class="modal-body">
          <input type="hidden" id="customerId" name="id">
          
          <div class="form-group">
            <label>Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" required>
          </div>
          
          <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" id="phone" class="form-control">
          </div>
          
          <div class="form-group">
            <label>Email <span class="text-danger">*</span></label>
            <input type="email" name="email" id="email" class="form-control" required>
          </div>
          
          <div class="form-group">
            <label>Address</label>
            <textarea name="address" id="address" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fa fa-times"></i> Cancel
          </button>
          <button type="submit" class="btn btn-warning">
            <i class="fa fa-save"></i> Update Customer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- View Customer Modal -->
<div class="modal fade" id="viewCustomerModal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">
          <i class="fa fa-info-circle"></i> Customer Details
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <tr>
            <th width="35%">Name:</th>
            <td id="view_name">-</td>
          </tr>
          <tr>
            <th>Phone:</th>
            <td id="view_phone">-</td>
          </tr>
          <tr>
            <th>Email:</th>
            <td id="view_email">-</td>
          </tr>
          <tr>
            <th>Address:</th>
            <td id="view_address">-</td>
          </tr>
          <tr>
            <th>Created At:</th>
            <td id="view_created_at">-</td>
          </tr>
          <tr>
            <th>Updated At:</th>
            <td id="view_updated_at">-</td>
          </tr>
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

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">
          <i class="fa fa-file-import"></i> Import Customers
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?= site_url('customers/import') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="modal-body">
          <div class="form-group">
            <label>CSV File</label>
            <input type="file" name="csv_file" class="form-control" accept=".csv" required>
            <small class="text-muted">Format: Name, Phone, Email, Address</small>
          </div>
          <div class="alert alert-info">
            <i class="fa fa-download"></i> 
            <a href="<?= site_url('customers/downloadTemplate') ?>">Download CSV Template</a>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Import</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const baseUrl = "<?= base_url() ?>";

$(document).ready(function() {
    // Initialize DataTable
    var table = $('#customersTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": baseUrl + "/customers/getCustomersData",
            "type": "POST",
            "data": function(d) {
                d.<?= csrf_token() ?> = '<?= csrf_hash() ?>';
            }
        },
        "columns": [
            {
                "data": null,
                "render": function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { "data": "name" },
            { "data": "phone" },
            { "data": "email" },
            { "data": "address" },
            { "data": "created_at" },
            { "data": "updated_at" },
            {
                "data": null,
                "render": function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-info view-btn" data-id="${row.id}">
                            <i class="fa fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-success edit-btn" data-id="${row.id}">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}">
                            <i class="fa fa-trash"></i>
                        </button>
                    `;
                },
                "orderable": false,
                "searchable": false
            }
        ],
        "order": [[0, "desc"]],
        "pageLength": 10
    });
    
    // Add New Customer button
    $('#addNewCustomerBtn').click(function() {
        $('#addCustomerForm')[0].reset();
        $('#AddNewModal').modal('show');
    });
    
    // Submit Add Form
    $('#addCustomerForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: baseUrl + "/customers/store",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    $('#AddNewModal').modal('hide');
                    table.ajax.reload();
                    alert(response.message);
                } else {
                    if (response.errors) {
                        var errorMsg = '';
                        for (var key in response.errors) {
                            errorMsg += response.errors[key] + '\n';
                        }
                        alert(errorMsg);
                    } else {
                        alert(response.message);
                    }
                }
            },
            error: function(xhr) {
                alert('Error: ' + xhr.statusText);
            }
        });
    });
    
    // View Customer
    $(document).on('click', '.view-btn', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: baseUrl + "/customers/getCustomer/" + id,
            type: "GET",
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    $('#view_name').text(response.data.name);
                    $('#view_phone').text(response.data.phone || '-');
                    $('#view_email').text(response.data.email);
                    $('#view_address').text(response.data.address || '-');
                    $('#view_created_at').text(response.data.created_at || '-');
                    $('#view_updated_at').text(response.data.updated_at || '-');
                    $('#viewCustomerModal').modal('show');
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Failed to fetch customer details');
            }
        });
    });
    
    // Edit Customer
    $(document).on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: baseUrl + "/customers/getCustomer/" + id,
            type: "GET",
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    $('#customerId').val(response.data.id);
                    $('#name').val(response.data.name);
                    $('#phone').val(response.data.phone);
                    $('#email').val(response.data.email);
                    $('#address').val(response.data.address);
                    $('#editCustomerModal').modal('show');
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Failed to fetch customer data');
            }
        });
    });
    
    // Submit Edit Form
    $('#editCustomerForm').submit(function(e) {
        e.preventDefault();
        
        var id = $('#customerId').val();
        
        $.ajax({
            url: baseUrl + "/customers/update/" + id,
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    $('#editCustomerModal').modal('hide');
                    table.ajax.reload();
                    alert(response.message);
                } else {
                    if (response.errors) {
                        var errorMsg = '';
                        for (var key in response.errors) {
                            errorMsg += response.errors[key] + '\n';
                        }
                        alert(errorMsg);
                    } else {
                        alert(response.message);
                    }
                }
            },
            error: function() {
                alert('Failed to update customer');
            }
        });
    });
    
    // Delete Customer
    $(document).on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this customer?')) {
            $.ajax({
                url: baseUrl + "/customers/delete/" + id,
                type: "DELETE",
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        table.ajax.reload();
                        alert(response.message);
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Failed to delete customer');
                }
            });
        }
    });
    
    // Export button
    $('#exportBtn').click(function() {
        window.location.href = baseUrl + "/customers/export";
    });
    
    // Import button
    $('#importBtn').click(function() {
        $('#importModal').modal('show');
    });
});
</script>
<?= $this->endSection() ?>