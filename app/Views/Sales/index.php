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
                                <button type="button" class="btn btn-md btn-info ml-2" id="filterBtn">
                                    <i class="fa fa-filter"></i> Filter
                                </button>
                                <button type="button" class="btn btn-md btn-secondary ml-2" id="exportBtn">
                                    <i class="fa fa-download"></i> Export
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
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sales Table -->
                            <table id="salesTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Invoice No.</th>
                                        <th>Customer</th>
                                        <th>Sale Date</th>
                                        <th class="text-right">Total Amount</th>
                                        <th class="text-right">Amount Paid</th>
                                        <th class="text-right">Due Amount</th>
                                        <th class="text-center">Payment Status</th>
                                        <th class="text-center">Order Status</th>
                                        <th width="120" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <td colspan="4" class="text-right"><strong>Totals:</strong></td>
                                        <td id="totalAmount" class="text-right"><strong>₱ 0.00</strong></td>
                                        <td id="totalPaid" class="text-right"><strong>₱ 0.00</strong></td>
                                        <td id="totalDue" class="text-right"><strong>₱ 0.00</strong></td>
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

<!-- Sale Modal -->
<div class="modal fade" id="saleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="saleModalTitle">New Sale</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="saleForm">
                <?= csrf_field() ?>
                <input type="hidden" name="sale_id" id="sale_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Customer</label>
                                <select name="customer_id" id="customer_id" class="form-control">
                                    <option value="">Walk-in Customer</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sale Date</label>
                                <input type="datetime-local" name="sale_date" id="sale_date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h6 class="card-title">Products</h6>
                                    <button type="button" class="btn btn-sm btn-primary float-right" id="addProductBtn">
                                        <i class="fa fa-plus"></i> Add Product
                                    </button>
                                </div>
                                <div class="card-body" id="productsContainer">
                                    <div id="productsList"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Discount (₱)</label>
                                <input type="number" name="discount" id="discount" class="form-control" step="0.01" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Amount Paid</label>
                                <input type="number" name="amount_paid" id="amount_paid" class="form-control" step="0.01" value="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Order Status</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Notes</label>
                                <textarea name="notes" id="notes" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info" id="totalInfo"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Sale</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Sale Modal -->
<div class="modal fade" id="viewSaleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Sale Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr><th>Invoice:</th><td id="view_invoice">-</td></tr>
                            <tr><th>Customer:</th><td id="view_customer">-</td></tr>
                            <tr><th>Date:</th><td id="view_date">-</td></tr>
                            <tr><th>Status:</th><td id="view_status">-</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr><th>Total:</th><td id="view_total">-</td></tr>
                            <tr><th>Discount:</th><td id="view_discount">-</td></tr>
                            <tr><th>Paid:</th><td id="view_paid">-</td></tr>
                            <tr><th>Due:</th><td id="view_due">-</td></tr>
                         </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <strong>Items:</strong>
                        <table class="table table-bordered mt-2">
                            <thead>
                                <tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>                            </thead>
                            <tbody id="view_items"></tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <strong>Notes:</strong>
                        <p id="view_notes">-</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <a href="#" id="printInvoiceBtn" class="btn btn-info" target="_blank">Print Invoice</a>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Process Payment</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="paymentForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" id="payment_sale_id">
                    <div class="form-group">
                        <label>Invoice</label>
                        <input type="text" id="payment_invoice" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Customer</label>
                        <input type="text" id="payment_customer" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Due Amount</label>
                        <input type="text" id="payment_due" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Payment Amount</label>
                        <input type="number" name="amount" id="payment_amount" class="form-control" step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Process Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Delete Sale</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this sale?</p>
                <p class="text-danger">This action cannot be undone!</p>
                <input type="hidden" id="delete_sale_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title">Export Sales</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="<?= base_url('sales/export') ?>" method="get">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" name="start_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" name="end_date" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Export CSV</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Define global variables
var baseUrl = "<?= base_url() ?>";
var csrfToken = "<?= csrf_token() ?>";
var csrfHash = "<?= csrf_hash() ?>";

let selectedProducts = [];

$(document).ready(function() {
    // Set default date
    $('#sale_date').val(new Date().toISOString().slice(0, 16));
    
    // Initialize DataTable
    const table = $('#salesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: baseUrl + "/sales/getSalesData",
            type: "POST",
            data: function(d) {
                // FIXED: Use the global csrfToken and csrfHash
                d[csrfToken] = csrfHash;
                d.start_date = $('#startDate').val();
                d.end_date = $('#endDate').val();
                d.payment_status = $('#paymentStatus').val();
                d.status = $('#orderStatus').val();
                return d;
            },
            error: function(xhr, status, error) {
                console.log("DataTable Ajax Error:", xhr.responseText);
                alert("Error loading sales data. Please check console for details.");
            }
        },
        columns: [
            { data: null, render: (data, type, row, meta) => meta.row + 1 },
            { data: "invoice_number" },
            { data: "customer_name" },
            { data: "sale_date" },
            { data: "total_amount", render: d => '₱ ' + parseFloat(d).toFixed(2), className: "text-right" },
            { data: "amount_paid", render: d => '₱ ' + parseFloat(d).toFixed(2), className: "text-right" },
            { data: "due_amount", render: d => '₱ ' + parseFloat(d).toFixed(2), className: "text-right" },
            { data: "payment_status", render: getPaymentBadge, className: "text-center" },
            { data: "status", render: getStatusBadge, className: "text-center" },
            { data: null, render: (data) => getActions(data), orderable: false, className: "text-center" }
        ],
        order: [[3, "DESC"]],
        drawCallback: function() {
            updateTotals();
        }
    });
    
    function getPaymentBadge(status) {
        const badges = {
            'paid': '<span class="badge badge-success">Paid</span>',
            'partial': '<span class="badge badge-warning">Partial</span>',
            'unpaid': '<span class="badge badge-danger">Unpaid</span>'
        };
        return badges[status] || '<span class="badge badge-secondary">' + status + '</span>';
    }
    
    function getStatusBadge(status) {
        const badges = {
            'completed': '<span class="badge badge-success">Completed</span>',
            'pending': '<span class="badge badge-warning">Pending</span>',
            'cancelled': '<span class="badge badge-danger">Cancelled</span>'
        };
        return badges[status] || '<span class="badge badge-secondary">' + status + '</span>';
    }
    
    function getActions(row) {
        let buttons = `
            <button class="btn btn-sm btn-info view-sale" data-id="${row.id}" title="View"><i class="fa fa-eye"></i></button>
            <button class="btn btn-sm btn-warning edit-sale" data-id="${row.id}" title="Edit"><i class="fa fa-edit"></i></button>
            <a href="${baseUrl}/sales/invoice/${row.id}" class="btn btn-sm btn-secondary" target="_blank" title="Print"><i class="fa fa-print"></i></a>
        `;
        if (row.payment_status !== 'paid' && row.status !== 'cancelled') {
            buttons += `<button class="btn btn-sm btn-success pay-sale" data-id="${row.id}" data-invoice="${row.invoice_number}" data-customer="${row.customer_name}" data-due="${row.due_amount}" title="Pay"><i class="fa fa-money"></i></button>`;
        }
        buttons += `<button class="btn btn-sm btn-danger delete-sale" data-id="${row.id}" title="Delete"><i class="fa fa-trash"></i></button>`;
        return buttons;
    }
    
    function updateTotals() {
        let totalAmount = 0, totalPaid = 0, totalDue = 0;
        $('#salesTable').DataTable().rows({page: 'current'}).data().each(row => {
            totalAmount += parseFloat(row.total_amount);
            totalPaid += parseFloat(row.amount_paid);
            totalDue += parseFloat(row.due_amount);
        });
        $('#totalAmount').html('<strong>₱ ' + totalAmount.toFixed(2) + '</strong>');
        $('#totalPaid').html('<strong>₱ ' + totalPaid.toFixed(2) + '</strong>');
        $('#totalDue').html('<strong>₱ ' + totalDue.toFixed(2) + '</strong>');
    }
    
    // Load customers
    function loadCustomers() {
        if ($('#customer_id option').length <= 1) {
            $.ajax({
                url: baseUrl + "/sales/getCustomers",
                type: "GET",
                data: { [csrfToken]: csrfHash },
                success: function(response) {
                    if (response.success) {
                        response.customers.forEach(c => {
                            $('#customer_id').append(`<option value="${c.id}">${c.name} (${c.phone || 'No phone'})</option>`);
                        });
                    }
                }
            });
        }
    }
    
    // CRUD Operations
    $('#addNewSaleBtn').click(function() {
        resetForm();
        loadCustomers();
        $('#saleModalTitle').text('New Sale');
        $('#saleModal').modal('show');
    });
    
    $(document).on('click', '.edit-sale', function() {
        const id = $(this).data('id');
        $.ajax({
            url: baseUrl + "/sales/getSale/" + id,
            type: "GET",
            data: { [csrfToken]: csrfHash },
            success: function(response) {
                if (response.success) {
                    populateForm(response.data, response.items);
                    loadCustomers();
                    $('#saleModalTitle').text('Edit Sale');
                    $('#saleModal').modal('show');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            }
        });
    });
    
    $(document).on('click', '.view-sale', function() {
        const id = $(this).data('id');
        $.ajax({
            url: baseUrl + "/sales/getSale/" + id,
            type: "GET",
            data: { [csrfToken]: csrfHash },
            success: function(response) {
                if (response.success) {
                    displaySale(response.data, response.items);
                    $('#viewSaleModal').modal('show');
                }
            }
        });
    });
    
    $(document).on('click', '.pay-sale', function() {
        $('#payment_sale_id').val($(this).data('id'));
        $('#payment_invoice').val($(this).data('invoice'));
        $('#payment_customer').val($(this).data('customer'));
        $('#payment_due').val('₱ ' + parseFloat($(this).data('due')).toFixed(2));
        $('#paymentModal').modal('show');
    });
    
    $(document).on('click', '.delete-sale', function() {
        $('#delete_sale_id').val($(this).data('id'));
        $('#deleteModal').modal('show');
    });
    
    $('#confirmDeleteBtn').click(function() {
        const id = $('#delete_sale_id').val();
        $.ajax({
            url: baseUrl + "/sales/delete/" + id,
            type: 'DELETE',
            data: { [csrfToken]: csrfHash },
            success: function(response) {
                if (response.success) {
                    $('#deleteModal').modal('hide');
                    $('#salesTable').DataTable().ajax.reload();
                    Swal.fire('Deleted!', response.message, 'success');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            }
        });
    });
    
    $('#paymentForm').submit(function(e) {
        e.preventDefault();
        const saleId = $('#payment_sale_id').val();
        const amount = $('#payment_amount').val();
        
        let postData = { amount: amount };
        postData[csrfToken] = csrfHash;
        
        $.post(baseUrl + "/sales/processPayment/" + saleId, postData, function(response) {
            if (response.success) {
                $('#paymentModal').modal('hide');
                $('#salesTable').DataTable().ajax.reload();
                Swal.fire('Success', response.message, 'success');
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        });
    });
    
    $('#saleForm').submit(function(e) {
        e.preventDefault();
        const saleId = $('#sale_id').val();
        const url = saleId ? baseUrl + "/sales/update/" + saleId : baseUrl + "/sales/store";
        
        const formData = {
            customer_id: $('#customer_id').val(),
            sale_date: $('#sale_date').val(),
            discount: $('#discount').val(),
            amount_paid: $('#amount_paid').val(),
            status: $('#status').val(),
            notes: $('#notes').val(),
            items: selectedProducts,
            total_amount: calculateTotal()
        };
        formData[csrfToken] = csrfHash;
        
        $.post(url, formData, function(response) {
            if (response.success) {
                $('#saleModal').modal('hide');
                $('#salesTable').DataTable().ajax.reload();
                Swal.fire('Success', response.message, 'success');
                resetForm();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        });
    });
    
    // Product management
    $('#addProductBtn').click(function() {
        // Load products
        $.ajax({
            url: baseUrl + "/sales/searchProducts/all",
            type: "GET",
            data: { [csrfToken]: csrfHash },
            success: function(response) {
                if (response.success) {
                    let options = '<option value="">Select Product</option>';
                    response.products.forEach(p => {
                        options += `<option value="${p.id}" data-price="${p.price}">${p.name} - ₱${p.price} (Stock: ${p.stock})</option>`;
                    });
                    
                    Swal.fire({
                        title: 'Add Product',
                        html: `
                            <select id="product_select" class="swal2-select" style="width:100%">${options}</select>
                            <input type="number" id="product_qty" class="swal2-input" placeholder="Quantity" min="1" value="1">
                            <input type="number" id="product_price" class="swal2-input" placeholder="Price" step="0.01">
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Add',
                        didOpen: () => {
                            $('#product_select').on('change', function() {
                                $('#product_price').val($(this).find(':selected').data('price'));
                            });
                        },
                        preConfirm: () => {
                            const productId = $('#product_select').val();
                            const quantity = parseInt($('#product_qty').val());
                            const price = parseFloat($('#product_price').val());
                            const name = $('#product_select option:selected').text().split(' - ')[0];
                            if (!productId || !quantity || !price) {
                                Swal.showValidationMessage('Please fill all fields');
                                return false;
                            }
                            return { productId, name, quantity, price };
                        }
                    }).then(result => {
                        if (result.isConfirmed) {
                            selectedProducts.push({
                                product_id: result.value.productId,
                                name: result.value.name,
                                quantity: result.value.quantity,
                                price: result.value.price
                            });
                            renderProducts();
                            updateTotalInfo();
                        }
                    });
                }
            }
        });
    });
    
    function renderProducts() {
        let html = '';
        selectedProducts.forEach((p, i) => {
            html += `
                <div class="row mb-2">
                    <div class="col-md-5"><input type="text" class="form-control" value="${p.name}" readonly></div>
                    <div class="col-md-2"><input type="number" class="form-control qty-input" data-index="${i}" value="${p.quantity}"></div>
                    <div class="col-md-3"><input type="number" class="form-control price-input" data-index="${i}" value="${p.price}" step="0.01"></div>
                    <div class="col-md-2"><button type="button" class="btn btn-danger btn-sm remove-product" data-index="${i}"><i class="fa fa-trash"></i></button></div>
                </div>
            `;
        });
        $('#productsList').html(html || '<div class="alert alert-info">No products added</div>');
        
        $('.qty-input').off('change').on('change', function() {
            const idx = $(this).data('index');
            selectedProducts[idx].quantity = parseInt($(this).val());
            updateTotalInfo();
        });
        
        $('.price-input').off('change').on('change', function() {
            const idx = $(this).data('index');
            selectedProducts[idx].price = parseFloat($(this).val());
            updateTotalInfo();
        });
        
        $('.remove-product').off('click').on('click', function() {
            const idx = $(this).data('index');
            selectedProducts.splice(idx, 1);
            renderProducts();
            updateTotalInfo();
        });
    }
    
    function calculateTotal() {
        let subtotal = selectedProducts.reduce((sum, p) => sum + (p.quantity * p.price), 0);
        return subtotal - parseFloat($('#discount').val() || 0);
    }
    
    function updateTotalInfo() {
        const subtotal = selectedProducts.reduce((sum, p) => sum + (p.quantity * p.price), 0);
        const discount = parseFloat($('#discount').val() || 0);
        const total = subtotal - discount;
        const paid = parseFloat($('#amount_paid').val() || 0);
        const due = total - paid;
        $('#totalInfo').html(`Subtotal: ₱${subtotal.toFixed(2)} | Discount: ₱${discount.toFixed(2)} | Total: ₱${total.toFixed(2)} | Paid: ₱${paid.toFixed(2)} | Due: ₱${due.toFixed(2)}`);
    }
    
    function resetForm() {
        $('#sale_id').val('');
        $('#customer_id').val('');
        $('#sale_date').val(new Date().toISOString().slice(0, 16));
        $('#discount').val(0);
        $('#amount_paid').val(0);
        $('#status').val('pending');
        $('#notes').val('');
        selectedProducts = [];
        renderProducts();
        updateTotalInfo();
    }
    
    function populateForm(sale, items) {
        $('#sale_id').val(sale.id);
        $('#customer_id').val(sale.customer_id || '');
        $('#sale_date').val(sale.sale_date.substring(0, 16));
        $('#discount').val(sale.discount);
        $('#amount_paid').val(sale.amount_paid);
        $('#status').val(sale.status);
        $('#notes').val(sale.notes || '');
        selectedProducts = items.map(i => ({
            product_id: i.product_id,
            name: i.product_name,
            quantity: parseInt(i.quantity),
            price: parseFloat(i.unit_price)
        }));
        renderProducts();
        updateTotalInfo();
    }
    
    function displaySale(sale, items) {
        $('#view_invoice').text(sale.invoice_number);
        $('#view_customer').text(sale.customer_name || 'Walk-in');
        $('#view_date').text(sale.sale_date);
        $('#view_status').html(getStatusBadge(sale.status));
        $('#view_total').text('₱ ' + parseFloat(sale.total_amount).toFixed(2));
        $('#view_discount').text('₱ ' + parseFloat(sale.discount || 0).toFixed(2));
        $('#view_paid').text('₱ ' + parseFloat(sale.amount_paid).toFixed(2));
        $('#view_due').text('₱ ' + parseFloat(sale.due_amount || 0).toFixed(2));
        $('#view_notes').text(sale.notes || '-');
        
        let html = '';
        items.forEach(i => {
            html += `<tr><td>${i.product_name}</td><td>${i.quantity}</td><td>₱ ${parseFloat(i.unit_price).toFixed(2)}</td><td>₱ ${parseFloat(i.subtotal).toFixed(2)}</td></tr>`;
        });
        $('#view_items').html(html);
        $('#printInvoiceBtn').attr('href', `${baseUrl}/sales/invoice/${sale.id}`);
    }
    
    // Filters
    $('#filterBtn').click(() => $('#filterRow').slideToggle());
    $('#startDate, #endDate, #paymentStatus, #orderStatus').on('change', () => $('#salesTable').DataTable().ajax.reload());
    $('#exportBtn').click(() => $('#exportModal').modal('show'));
    
    $('#discount, #amount_paid').on('input', updateTotalInfo);
});
</script>
<?= $this->endSection() ?>