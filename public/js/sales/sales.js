// sales.js - Complete JavaScript for Sales Management

$(document).ready(function() {
    // Initialize DataTable
    var salesTable = $('#salesTable').DataTable({
    ajax: {
        url: "/sales/getSalesData",
        type: "POST"  // ← Add this line
    },
            "data": function(d) {
                d.<?= csrf_token() ?> = '<?= csrf_hash() ?>';
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
        
        $('#totalAmount').text(formatCurrency(totalAmount));
        $('#totalPaid').text(formatCurrency(totalPaid));
        $('#totalDue').text(formatCurrency(totalDue));
    }
    
    // Format currency
    function formatCurrency(amount) {
        return '₱ ' + parseFloat(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    
    // Get payment status badge
    function getPaymentStatusBadge(status) {
        switch(status) {
            case 'paid':
                return '<span class="badge badge-success"><i class="fa fa-check-circle"></i> Paid</span>';
            case 'partial':
                return '<span class="badge badge-warning"><i class="fa fa-clock"></i> Partial</span>';
            case 'unpaid':
                return '<span class="badge badge-danger"><i class="fa fa-times-circle"></i> Unpaid</span>';
            default:
                return '<span class="badge badge-secondary">' + status + '</span>';
        }
    }
    
    // Get order status badge
    function getOrderStatusBadge(status) {
        switch(status) {
            case 'completed':
                return '<span class="badge badge-success"><i class="fa fa-check"></i> Completed</span>';
            case 'pending':
                return '<span class="badge badge-warning"><i class="fa fa-hourglass-half"></i> Pending</span>';
            case 'cancelled':
                return '<span class="badge badge-danger"><i class="fa fa-ban"></i> Cancelled</span>';
            case 'refunded':
                return '<span class="badge badge-info"><i class="fa fa-undo"></i> Refunded</span>';
            default:
                return '<span class="badge badge-secondary">' + status + '</span>';
        }
    }
    
    // Generate action buttons
    function generateActionButtons(row) {
        var buttons = '';
        
        // View button
        buttons += '<button class="btn btn-sm btn-info view-sale" data-id="' + row.id + '" title="View Details">';
        buttons += '<i class="fa fa-eye"></i></button>&nbsp;';
        
        // Pay button (only if not fully paid)
        if (row.payment_status !== 'paid' && row.status !== 'cancelled') {
            buttons += '<button class="btn btn-sm btn-success pay-sale" data-id="' + row.id + '" ';
            buttons += 'data-invoice="' + row.invoice_number + '" ';
            buttons += 'data-customer="' + row.customer_name + '" ';
            buttons += 'data-total="' + row.total_amount + '" ';
            buttons += 'data-paid="' + row.amount_paid + '" ';
            buttons += 'data-due="' + row.due_amount + '" title="Process Payment">';
            buttons += '<i class="fa fa-money-bill"></i></button>&nbsp;';
        }
        
        // Print Invoice button
        buttons += '<a href="' + baseUrl + '/sales/invoice/' + row.id + '" class="btn btn-sm btn-secondary" target="_blank" title="Print Invoice">';
        buttons += '<i class="fa fa-print"></i></a>&nbsp;';
        
        // Cancel button (only if not completed or cancelled)
        if (row.status !== 'completed' && row.status !== 'cancelled') {
            buttons += '<button class="btn btn-sm btn-danger cancel-sale" data-id="' + row.id + '" title="Cancel Sale">';
            buttons += '<i class="fa fa-times"></i></button>';
        }
        
        return buttons;
    }
    
    // New Sale button
    $('#addNewSaleBtn').click(function() {
        window.location.href = baseUrl + "/sales/create";
    });
    
    // Toggle Filter Row
    $('#filterBtn').click(function() {
        $('#filterRow').slideToggle(300);
    });
    
    // Apply filters
    $('#startDate, #endDate, #paymentStatus, #orderStatus').on('change', function() {
        salesTable.ajax.reload();
    });
    
    // Clear filters
    $('#clearFiltersBtn').click(function() {
        $('#startDate').val('');
        $('#endDate').val('');
        $('#paymentStatus').val('');
        $('#orderStatus').val('');
        salesTable.ajax.reload();
        $('#filterRow').slideUp(300);
        showNotification('Filters cleared', 'success');
    });
    
    // View Sale Details
    $(document).on('click', '.view-sale', function() {
        var id = $(this).data('id');
        showLoading();
        
        $.ajax({
            url: baseUrl + "/sales/getSale/" + id,
            type: "GET",
            dataType: "json",
            success: function(response) {
                hideLoading();
                if (response.success) {
                    displaySaleDetails(response.data, response.items);
                    $('#viewSaleModal').modal('show');
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function(xhr) {
                hideLoading();
                showNotification('Failed to fetch sale details', 'error');
                console.error('Error:', xhr.responseText);
            }
        });
    });
    
    // Display sale details in modal
    function displaySaleDetails(sale, items) {
        $('#view_invoice').text(sale.invoice_number);
        $('#view_customer').text(sale.customer_name || 'Walk-in Customer');
        $('#view_customer_phone').text(sale.customer_phone || '-');
        $('#view_customer_email').text(sale.customer_email || '-');
        $('#view_sale_date').text(formatDate(sale.sale_date));
        $('#view_status').html(getOrderStatusBadge(sale.status));
        $('#view_payment_status').html(getPaymentStatusBadge(sale.payment_status));
        $('#view_total').html(formatCurrency(sale.total_amount));
        $('#view_discount').html(formatCurrency(sale.discount || 0));
        $('#view_paid').html(formatCurrency(sale.amount_paid));
        $('#view_due').html(formatCurrency(sale.due_amount || 0));
        $('#view_notes').text(sale.notes || 'No notes');
        
        // Display items
        var itemsHtml = '';
        if (items && items.length > 0) {
            items.forEach(function(item, index) {
                itemsHtml += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.product_name}</td>
                        <td>${item.product_sku || '-'}</td>
                        <td class="text-center">${item.quantity}</td>
                        <td class="text-right">${formatCurrency(item.unit_price)}</td>
                        <td class="text-right">${formatCurrency(item.subtotal)}</td>
                    </tr>
                `;
            });
        } else {
            itemsHtml = '<tr><td colspan="6" class="text-center">No items found</td></tr>';
        }
        $('#view_items').html(itemsHtml);
        
        // Set print button
        $('#printInvoiceBtn').attr('href', baseUrl + '/sales/invoice/' + sale.id);
    }
    
    // Process Payment
    $(document).on('click', '.pay-sale', function() {
        var saleId = $(this).data('id');
        var invoice = $(this).data('invoice');
        var customer = $(this).data('customer');
        var total = $(this).data('total');
        var paid = $(this).data('paid');
        var due = $(this).data('due');
        
        $('#payment_sale_id').val(saleId);
        $('#payment_invoice').val(invoice);
        $('#payment_customer').val(customer);
        $('#payment_total').val(formatCurrency(total));
        $('#payment_paid').val(formatCurrency(paid));
        $('#payment_due').val(formatCurrency(due));
        $('#payment_amount').val('');
        $('#payment_amount').attr('max', due);
        
        // Add validation on input
        $('#payment_amount').off('input').on('input', function() {
            var amount = parseFloat($(this).val());
            if (amount > due) {
                $(this).addClass('is-invalid');
                $('#payment_amount_error').text('Amount cannot exceed due amount');
            } else {
                $(this).removeClass('is-invalid');
                $('#payment_amount_error').text('');
            }
        });
        
        $('#paymentModal').modal('show');
    });
    
    // Submit Payment
    $('#paymentForm').submit(function(e) {
        e.preventDefault();
        
        var saleId = $('#payment_sale_id').val();
        var amount = $('#payment_amount').val();
        var dueAmount = parseFloat($('#payment_due').val().replace('₱', '').replace(/,/g, ''));
        
        if (!amount || amount <= 0) {
            showNotification('Please enter a valid amount', 'error');
            return false;
        }
        
        if (parseFloat(amount) > dueAmount) {
            showNotification('Payment amount cannot exceed due amount', 'error');
            return false;
        }
        
        showLoading();
        
        $.ajax({
            url: baseUrl + "/sales/processPayment/" + saleId,
            type: "POST",
            data: { 
                amount: amount,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            dataType: "json",
            success: function(response) {
                hideLoading();
                if (response.success) {
                    $('#paymentModal').modal('hide');
                    salesTable.ajax.reload();
                    showNotification(response.message, 'success');
                    
                    // Reset form
                    $('#paymentForm')[0].reset();
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function(xhr) {
                hideLoading();
                showNotification('Failed to process payment', 'error');
                console.error('Error:', xhr.responseText);
            }
        });
    });
    
    // Cancel Sale
    $(document).on('click', '.cancel-sale', function() {
        var id = $(this).data('id');
        
        Swal.fire({
            title: 'Cancel Sale?',
            text: "This action will cancel the sale and return products to stock. This cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, cancel it!',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                
                $.ajax({
                    url: baseUrl + "/sales/cancelSale/" + id,
                    type: "POST",
                    data: { <?= csrf_token() ?>: '<?= csrf_hash() ?>' },
                    dataType: "json",
                    success: function(response) {
                        hideLoading();
                        if (response.success) {
                            salesTable.ajax.reload();
                            Swal.fire('Cancelled!', response.message, 'success');
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        hideLoading();
                        Swal.fire('Error!', 'Failed to cancel sale', 'error');
                        console.error('Error:', xhr.responseText);
                    }
                });
            }
        });
    });
    
    // Export Sales
    $('#exportBtn').click(function() {
        $('#exportModal').modal('show');
    });
    
    // Export form submit
    $('#exportForm').submit(function(e) {
        var startDate = $('input[name="start_date"]').val();
        var endDate = $('input[name="end_date"]').val();
        
        if (startDate && endDate && startDate > endDate) {
            e.preventDefault();
            showNotification('Start date cannot be after end date', 'error');
        }
    });
    
    // Reload DataTable periodically (optional - every 30 seconds)
    setInterval(function() {
        if (salesTable) {
            salesTable.ajax.reload(null, false);
        }
    }, 30000);
    
    // Show loading indicator
    function showLoading() {
        if ($('#loadingOverlay').length === 0) {
            $('body').append(`
                <div id="loadingOverlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
                     background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;">
                    <div class="spinner-border text-light" style="width: 3rem; height: 3rem;" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            `);
        } else {
            $('#loadingOverlay').show();
        }
    }
    
    function hideLoading() {
        $('#loadingOverlay').hide();
    }
    
    // Show notification
    function showNotification(message, type) {
        var bgColor = type === 'success' ? 'bg-success' : (type === 'error' ? 'bg-danger' : 'bg-info');
        
        if (typeof toastr !== 'undefined') {
            // If toastr is available
            if (type === 'success') toastr.success(message);
            else if (type === 'error') toastr.error(message);
            else toastr.info(message);
        } else {
            // Fallback to browser alert or custom toast
            alert(message);
        }
    }
    
    // Format date
    function formatDate(dateString) {
        if (!dateString) return '-';
        var date = new Date(dateString);
        return date.toLocaleString();
    }
    
    // Keyboard shortcuts
    $(document).keydown(function(e) {
        // Ctrl + N for new sale
        if (e.ctrlKey && e.keyCode === 78) {
            e.preventDefault();
            $('#addNewSaleBtn').click();
        }
        // Ctrl + F for filter
        if (e.ctrlKey && e.keyCode === 70) {
            e.preventDefault();
            $('#filterBtn').click();
        }
        // Ctrl + R for refresh
        if (e.ctrlKey && e.keyCode === 82) {
            e.preventDefault();
            salesTable.ajax.reload();
        }
    });
    
    
    // Print functionality
    $('#printInvoiceBtn').click(function() {
        var win = window.open($(this).attr('href'), '_blank');
        win.focus();
    });
    
    // Auto-refresh on visibility change
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden && salesTable) {
            salesTable.ajax.reload(null, false);
        }
    });
    
    console.log('Sales management system initialized');
});