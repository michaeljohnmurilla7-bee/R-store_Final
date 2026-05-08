<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Sales Orders</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
            <li class="breadcrumb-item active">Sales Orders</li>
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
              <h3 class="card-title">List of Sales Orders</h3>
              <div class="float-right">
                <button type="button" class="btn btn-md btn-success" id="addNewSaleBtn">
                  <i class="fa fa-plus-circle"></i> New Sale
                </button>
                <button type="button" class="btn btn-md btn-success ml-2" id="exportBtn">
                  <i class="fa fa-file-export"></i> Export
                </button>
                <button type="button" class="btn btn-md btn-info ml-2" id="filterBtn">
                  <i class="fa fa-filter"></i> Filter
                </button>
              </div>
            </div>
            <div class="card-body">
              <!-- Filter Row -->
              <div class="row mb-3" id="filterRow" style="display: none;">
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" id="startDate" class="form-control">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>End Date</label>
                    <input type="date" id="endDate" class="form-control">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Payment Status</label>
                    <select id="paymentStatus" class="form-control">
                      <option value="">All</option>
                      <option value="paid">Paid</option>
                      <option value="partial">Partial</option>
                      <option value="unpaid">Unpaid</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Order Status</label>
                    <select id="orderStatus" class="form-control">
                      <option value="">All</option>
                      <option value="pending">Pending</option>
                      <option value="completed">Completed</option>
                      <option value="cancelled">Cancelled</option>
                      <option value="refunded">Refunded</option>
                    </select>
                  </div>
                </div>
              </div>
              
              <!-- Sales Table -->
              <table id="salesTable" class="table table-bordered table-striped table-sm">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>Invoice No.</th>
                    <th>Customer</th>
                    <th>Sale Date</th>
                    <th>Total Amount</th>
                    <th>Amount Paid</th>
                    <th>Due Amount</th>
                    <th>Payment Status</th>
                    <th>Order Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                  <tr class="bg-light font-weight-bold">
                    <td colspan="4" class="text-right">Totals:</td>
                    <td id="totalAmount">0.00</td>
                    <td id="totalPaid">0.00</td>
                    <td id="totalDue">0.00</td>
                    <td colspan="3"></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- View Sale Modal -->
<div class="modal fade" id="viewSaleModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">
          <i class="fa fa-info-circle"></i> Sale Details
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <table class="table table-borderless table-sm">
              <tr><th width="35%">Invoice No:</th><td id="view_invoice">-</td></tr>
              <tr><th>Customer:</th><td id="view_customer">-</td></tr>
              <tr><th>Sale Date:</th><td id="view_sale_date">-</td></tr>
              <tr><th>Status:</th><td id="view_status">-</td></tr>
            </table>
          </div>
          <div class="col-md-6">
            <table class="table table-borderless table-sm">
              <tr><th>Total Amount:</th><td id="view_total">-</td></tr>
              <tr><th>Discount:</th><td id="view_discount">-</td></tr>
              <tr><th>Amount Paid:</th><td id="view_paid">-</td></tr>
              <tr><th>Due Amount:</th><td id="view_due">-</td></tr>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <hr>
            <strong>Notes:</strong>
            <p id="view_notes" class="text-muted mt-2">-</p>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <strong>Sale Items:</strong>
            <div class="table-responsive mt-2">
              <table class="table table-bordered table-sm">
                <thead>
                  <tr><th>Product</th><th>Quantity</th><th>Unit Price</th><th>Subtotal</th></tr>
                </thead>
                <tbody id="view_items"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fa fa-times"></i> Close
        </button>
        <a href="#" id="printInvoiceBtn" class="btn btn-info" target="_blank">
          <i class="fa fa-print"></i> Print Invoice
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Process Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">
          <i class="fa fa-money-bill"></i> Process Payment
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="paymentForm">
        <?= csrf_field() ?>
        <div class="modal-body">
          <input type="hidden" id="payment_sale_id" name="sale_id">
          
          <div class="form-group">
            <label>Invoice Number</label>
            <input type="text" id="payment_invoice" class="form-control" readonly>
          </div>
          
          <div class="form-group">
            <label>Customer Name</label>
            <input type="text" id="payment_customer" class="form-control" readonly>
          </div>
          
          <div class="form-group">
            <label>Total Amount</label>
            <input type="text" id="payment_total" class="form-control" readonly>
          </div>
          
          <div class="form-group">
            <label>Amount Paid</label>
            <input type="text" id="payment_paid" class="form-control" readonly>
          </div>
          
          <div class="form-group">
            <label>Due Amount</label>
            <input type="text" id="payment_due" class="form-control" readonly>
          </div>
          
          <div class="form-group">
            <label>Payment Amount <span class="text-danger">*</span></label>
            <input type="number" name="amount" id="payment_amount" class="form-control" step="0.01" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fa fa-times"></i> Cancel
          </button>
          <button type="submit" class="btn btn-success">
            <i class="fa fa-save"></i> Process Payment
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">
          <i class="fa fa-file-export"></i> Export Sales Data
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="exportForm" action="<?= site_url('sales/export') ?>" method="get">
        <div class="modal-body">
          <div class="form-group">
            <label>Start Date</label>
            <input type="date" name="start_date" class="form-control">
          </div>
          <div class="form-group">
            <label>End Date</label>
            <input type="date" name="end_date" class="form-control">
          </div>
          <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> Leave dates empty to export all sales
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Export to CSV</button>
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
    var table = $('#salesTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": baseUrl + "/sales/getSalesData",
            "type": "POST",
            "data": function(d) {
                d.<?= csrf_token() ?> = '<?= csrf_hash() ?>';
                d.start_date = $('#startDate').val();
                d.end_date = $('#endDate').val();
                d.payment_status = $('#paymentStatus').val();
                d.order_status = $('#orderStatus').val();
                return d;
            }
        },
        "columns": [
            {
                "data": null,
                "render": function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { "data": "invoice_number" },
            { "data": "customer_name" },
            { "data": "sale_date" },
            { 
                "data": "total_amount",
                "render": function(data) {
                    return '₱ ' + parseFloat(data).toFixed(2);
                }
            },
            { 
                "data": "amount_paid",
                "render": function(data) {
                    return '₱ ' + parseFloat(data).toFixed(2);
                }
            },
            { 
                "data": "due_amount",
                "render": function(data) {
                    return '₱ ' + parseFloat(data).toFixed(2);
                }
            },
            { "data": "payment_status" },
            { "data": "status" },
            {
                "data": null,
                "render": function(data, type, row) {
                    return `
                        <div class="btn-group">
                            <button class="btn btn-sm btn-info view-btn" data-id="${row.id}">
                                <i class="fa fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-success pay-btn" data-id="${row.id}" data-invoice="${row.invoice_number}" data-customer="${row.customer_name}" data-total="${row.total_amount}" data-paid="${row.amount_paid}" data-due="${row.due_amount}">
                                <i class="fa fa-money-bill"></i>
                            </button>
                            <a href="${baseUrl}/sales/invoice/${row.id}" class="btn btn-sm btn-secondary" target="_blank">
                                <i class="fa fa-print"></i>
                            </a>
                            <button class="btn btn-sm btn-danger cancel-btn" data-id="${row.id}">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    `;
                },
                "orderable": false,
                "searchable": false
            }
        ],
        "order": [[3, "DESC"]],
        "pageLength": 10,
        "responsive": true,
        "drawCallback": function(settings) {
            // Calculate totals
            var api = this.api();
            var totalAmount = 0;
            var totalPaid = 0;
            var totalDue = 0;
            
            api.rows({page: 'current'}).data().each(function(row) {
                totalAmount += parseFloat(row.total_amount);
                totalPaid += parseFloat(row.amount_paid);
                totalDue += parseFloat(row.due_amount);
            });
            
            $('#totalAmount').text('₱ ' + totalAmount.toFixed(2));
            $('#totalPaid').text('₱ ' + totalPaid.toFixed(2));
            $('#totalDue').text('₱ ' + totalDue.toFixed(2));
        }
    });
    
    // New Sale button
    $('#addNewSaleBtn').click(function() {
        window.location.href = baseUrl + "/sales/create";
    });
    
    // Filter button
    $('#filterBtn').click(function() {
        $('#filterRow').slideToggle();
    });
    
    // Apply filters when changed
    $('#startDate, #endDate, #paymentStatus, #orderStatus').on('change', function() {
        table.ajax.reload();
    });
    
    // View Sale
    $(document).on('click', '.view-btn', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: baseUrl + "/sales/getSale/" + id,
            type: "GET",
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    var sale = response.data;
                    var items = response.items;
                    
                    $('#view_invoice').text(sale.invoice_number);
                    $('#view_customer').text(sale.customer_name || 'Walk-in Customer');
                    $('#view_sale_date').text(sale.sale_date);
                    $('#view_status').html(getStatusBadge(sale.status));
                    $('#view_total').text('₱ ' + parseFloat(sale.total_amount).toFixed(2));
                    $('#view_discount').text('₱ ' + parseFloat(sale.discount || 0).toFixed(2));
                    $('#view_paid').text('₱ ' + parseFloat(sale.amount_paid).toFixed(2));
                    $('#view_due').text('₱ ' + parseFloat(sale.due_amount || 0).toFixed(2));
                    $('#view_notes').text(sale.notes || 'No notes');
                    
                    // Display items
                    var itemsHtml = '';
                    if (items && items.length > 0) {
                        items.forEach(function(item) {
                            itemsHtml += `
                                <tr>
                                    <td>${item.product_name}</td>
                                    <td>${item.quantity}</td>
                                    <td>₱ ${parseFloat(item.unit_price).toFixed(2)}</td>
                                    <td>₱ ${parseFloat(item.subtotal).toFixed(2)}</td>
                                </tr>
                            `;
                        });
                    } else {
                        itemsHtml = '<tr><td colspan="4" class="text-center">No items found</td></tr>';
                    }
                    $('#view_items').html(itemsHtml);
                    
                    $('#printInvoiceBtn').attr('href', baseUrl + '/sales/invoice/' + id);
                    $('#viewSaleModal').modal('show');
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Failed to fetch sale details');
            }
        });
    });
    
    // Process Payment button
    $(document).on('click', '.pay-btn', function() {
        var id = $(this).data('id');
        var invoice = $(this).data('invoice');
        var customer = $(this).data('customer');
        var total = $(this).data('total');
        var paid = $(this).data('paid');
        var due = $(this).data('due');
        
        $('#payment_sale_id').val(id);
        $('#payment_invoice').val(invoice);
        $('#payment_customer').val(customer);
        $('#payment_total').val('₱ ' + parseFloat(total).toFixed(2));
        $('#payment_paid').val('₱ ' + parseFloat(paid).toFixed(2));
        $('#payment_due').val('₱ ' + parseFloat(due).toFixed(2));
        $('#payment_amount').val('');
        $('#payment_amount').attr('max', due);
        
        $('#paymentModal').modal('show');
    });
    
    // Submit Payment
    $('#paymentForm').submit(function(e) {
        e.preventDefault();
        
        var saleId = $('#payment_sale_id').val();
        var amount = $('#payment_amount').val();
        var dueAmount = parseFloat($('#payment_due').val().replace('₱ ', ''));
        
        if (amount <= 0) {
            alert('Please enter a valid amount');
            return false;
        }
        
        if (amount > dueAmount) {
            alert('Payment amount cannot exceed due amount');
            return false;
        }
        
        $.ajax({
            url: baseUrl + "/sales/processPayment/" + saleId,
            type: "POST",
            data: { amount: amount, <?= csrf_token() ?>: '<?= csrf_hash() ?>' },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    $('#paymentModal').modal('hide');
                    table.ajax.reload();
                    alert(response.message);
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Failed to process payment');
            }
        });
    });
    
    // Cancel Sale
    $(document).on('click', '.cancel-btn', function() {
        var id = $(this).data('id');
        
        if (confirm('Are you sure you want to cancel this sale? This will return the products to stock.')) {
            $.ajax({
                url: baseUrl + "/sales/cancelSale/" + id,
                type: "POST",
                data: { <?= csrf_token() ?>: '<?= csrf_hash() ?>' },
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
                    alert('Failed to cancel sale');
                }
            });
        }
    });
    
    // Export button
    $('#exportBtn').click(function() {
        $('#exportModal').modal('show');
    });
    
    // Helper function for status badges
    function getStatusBadge(status) {
        switch(status) {
            case 'completed':
                return '<span class="badge badge-success">Completed</span>';
            case 'pending':
                return '<span class="badge badge-warning">Pending</span>';
            case 'cancelled':
                return '<span class="badge badge-danger">Cancelled</span>';
            case 'refunded':
                return '<span class="badge badge-info">Refunded</span>';
            default:
                return '<span class="badge badge-secondary">' + status + '</span>';
        }
    }
});
</script>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<!-- Include SweetAlert2 for better alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Include Toastr for notifications (optional) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
const baseUrl = "<?= base_url() ?>";
</script>

<!-- Your main sales.js file -->
<script src="<?= base_url('js/sales/sales.js') ?>"></script>

<!-- Additional inline scripts for CSRF -->
<script>
// CSRF token handling for AJAX
$(document).ajaxSend(function(e, xhr, options) {
    if (options.type === 'POST' || options.type === 'DELETE') {
        if (typeof csrfTokenName !== 'undefined' && typeof csrfHash !== 'undefined') {
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfHash);
        }
    }
});
</script>

<?= $this->endSection() ?>