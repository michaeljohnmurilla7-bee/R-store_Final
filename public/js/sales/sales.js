// sales.js - Fixed Version
$(document).ready(function() {
    // Initialize DataTable
    var salesTable = $('#salesTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": baseUrl + "/sales/getSalesData",  // FIXED: use baseUrl
            "type": "POST",
            "data": function(d) {
                 var csrfToken = $('meta[name="csrf-token"]').attr('content');
                 var csrfHash = $('meta[name="csrf-hash"]').attr('content');
                 d[csrfToken] = csrfHash;
                // FIXED: Use global variables instead of PHP
                d[csrfToken] = csrfHash;
                d.start_date = $('#startDate').val();
                d.end_date = $('#endDate').val();
                d.payment_status = $('#paymentStatus').val();
                d.order_status = $('#orderStatus').val();
                return d;
            },
            "error": function(xhr, error, code) {
                console.log('DataTable Error:', xhr.responseText);
                showNotification('Error loading sales data', 'error');
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
                    return formatCurrency(data);
                },
                "className": "text-right"
            },
            { 
                "data": "amount_paid",
                "render": function(data) {
                    return formatCurrency(data);
                },
                "className": "text-right"
            },
            { 
                "data": "due_amount",
                "render": function(data) {
                    return formatCurrency(data);
                },
                "className": "text-right"
            },
            { 
                "data": "payment_status",
                "render": function(data) {
                    return getPaymentStatusBadge(data);
                },
                "className": "text-center"
            },
            { 
                "data": "status",
                "render": function(data) {
                    return getOrderStatusBadge(data);
                },
                "className": "text-center"
            },
            {
                "data": null,
                "render": function(data, type, row) {
                    return generateActionButtons(row);
                },
                "orderable": false,
                "searchable": false,
                "className": "text-center"
            }
        ],
        "order": [[3, "DESC"]],
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "responsive": true,
        "language": {
            "processing": '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>',
            "emptyTable": "No sales data available",
            "zeroRecords": "No matching sales found",
            "info": "Showing _START_ to _END_ of _TOTAL_ sales",
            "infoEmpty": "Showing 0 to 0 of 0 sales",
            "infoFiltered": "(filtered from _MAX_ total sales)",
            "search": "Search:",
            "lengthMenu": "Show _MENU_ sales"
        },
        "drawCallback": function(settings) {
            updateTotals();
        }
    });
    
    // Function to update totals
    function updateTotals() {
        var api = salesTable.api();
        var totalAmount = 0;
        var totalPaid = 0;
        var totalDue = 0;
        
        api.rows({page: 'current'}).data().each(function(row) {
            totalAmount += parseFloat(row.total_amount);
            totalPaid += parseFloat(row.amount_paid);
            totalDue += parseFloat(row.due_amount);
        });
        
        $('#totalAmount').html('<strong>' + formatCurrency(totalAmount) + '</strong>');
        $('#totalPaid').html('<strong>' + formatCurrency(totalPaid) + '</strong>');
        $('#totalDue').html('<strong>' + formatCurrency(totalDue) + '</strong>');
    }
    
    // Format currency
    function formatCurrency(amount) {
        return '₱ ' + parseFloat(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    
    // Get payment status badge
    function getPaymentStatusBadge(status) {
        switch(status) {
            case 'paid':
                return '<span class="badge badge-success">Paid</span>';
            case 'partial':
                return '<span class="badge badge-warning">Partial</span>';
            case 'unpaid':
                return '<span class="badge badge-danger">Unpaid</span>';
            default:
                return '<span class="badge badge-secondary">' + status + '</span>';
        }
    }
    
    // Get order status badge
    function getOrderStatusBadge(status) {
        switch(status) {
            case 'completed':
                return '<span class="badge badge-success">Completed</span>';
            case 'pending':
                return '<span class="badge badge-warning">Pending</span>';
            case 'cancelled':
                return '<span class="badge badge-danger">Cancelled</span>';
            default:
                return '<span class="badge badge-secondary">' + status + '</span>';
        }
    }
    
    // Generate action buttons
    function generateActionButtons(row) {
        var buttons = '';
        
        // View button
        buttons += '<button class="btn btn-sm btn-info view-sale" data-id="' + row.id + '" title="View">';
        buttons += '<i class="fa fa-eye"></i></button> ';
        
        // Edit button
        buttons += '<button class="btn btn-sm btn-warning edit-sale" data-id="' + row.id + '" title="Edit">';
        buttons += '<i class="fa fa-edit"></i></button> ';
        
        // Print Invoice button - FIXED: use baseUrl
        buttons += '<a href="' + baseUrl + '/sales/invoice/' + row.id + '" class="btn btn-sm btn-secondary" target="_blank" title="Print">';
        buttons += '<i class="fa fa-print"></i></a> ';
        
        // Pay button (only if not fully paid)
        if (row.payment_status !== 'paid' && row.status !== 'cancelled') {
            buttons += '<button class="btn btn-sm btn-success pay-sale" data-id="' + row.id + '" ';
            buttons += 'data-invoice="' + row.invoice_number + '" ';
            buttons += 'data-customer="' + row.customer_name + '" ';
            buttons += 'data-due="' + row.due_amount + '" title="Pay">';
            buttons += '<i class="fa fa-money"></i></button> ';
        }
        
        // Delete button
        buttons += '<button class="btn btn-sm btn-danger delete-sale" data-id="' + row.id + '" title="Delete">';
        buttons += '<i class="fa fa-trash"></i></button>';
        
        return buttons;
    }
    
    // New Sale button
    $('#addNewSaleBtn').click(function() {
        window.location.href = baseUrl + "/sales/create";
    });
    
    // Apply filters
    $('#startDate, #endDate, #paymentStatus, #orderStatus').on('change', function() {
        salesTable.ajax.reload();
    });
    
    $('#filterBtn').click(function() {
        $('#filterRow').slideToggle(300);
    });
    
    // Show notification
    function showNotification(message, type) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: type === 'success' ? 'Success' : 'Error',
                text: message,
                icon: type,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        } else {
            alert(message);
        }
    }
    
    console.log('Sales management system initialized');
});