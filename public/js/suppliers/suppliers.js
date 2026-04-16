// suppliers.js - Compatible with rStore Suppliers Module

// Toastr notification helper
function showToast(type, message) {
    if (typeof toastr !== 'undefined') {
        if (type === 'success') {
            toastr.success(message, 'Success');
        } else if (type === 'error') {
            toastr.error(message, 'Error');
        } else if (type === 'warning') {
            toastr.warning(message, 'Warning');
        } else {
            toastr.info(message, 'Info');
        }
    } else {
        alert(message);
    }
}

$(document).ready(function() {
    // Test if modal works
    console.log('jQuery version:', $.fn.jquery);
    console.log('Bootstrap modal available:', typeof $.fn.modal !== 'undefined');
    
    // Manual modal trigger to avoid data-toggle issues
    $('#addNewSupplierBtn').on('click', function(e) {
        e.preventDefault();
        $('#AddNewModal').modal('show');
    });
    
    // Initialize DataTable
    var dataTable = $('#example1').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "processing": true,
        "serverSide": false, // Set to true if you want server-side processing
        "ajax": {
            "url": baseUrl + 'suppliers/getSuppliers',
            "type": "GET",
            "dataType": "json",
            "dataSrc": function(json) {
                if (json.status === 'success') {
                    return json.data;
                } else {
                    showToast('error', 'Failed to load suppliers data');
                    return [];
                }
            },
            "error": function(xhr, status, error) {
                console.error('DataTable AJAX Error:', error);
                showToast('error', 'Failed to load suppliers');
                return [];
            }
        },
        "columns": [
            { 
                "data": null,
                "render": function(data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { "data": "id", "visible": false },
            { "data": "name" },
            { "data": "contact_person", "defaultContent": "-" },
            { "data": "email", "defaultContent": "-" },
            { "data": "phone", "defaultContent": "-" },
            { 
                "data": "address",
                "render": function(data) {
                    if (data) {
                        return data.length > 50 ? data.substring(0, 50) + '...' : data;
                    }
                    return '-';
                }
            },
            { 
                "data": "is_active",
                "render": function(data) {
                    if (data == 1) {
                        return '<span class="badge badge-success">Active</span>';
                    } else {
                        return '<span class="badge badge-danger">Inactive</span>';
                    }
                }
            },
            {
                "data": null,
                "render": function(data) {
                    return `
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-info view-btn" data-id="${data.id}" title="View">
                                <i class="fa fa-eye"></i>
                            </button>
                            <button class="btn btn-warning edit-btn" data-id="${data.id}" title="Edit">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger delete-btn" data-id="${data.id}" title="Delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        "order": [[1, 'asc']],
        "language": {
            "emptyTable": "No suppliers found",
            "zeroRecords": "No matching suppliers found"
        }
    });

    // Submit Add Supplier Form
    $('#addSupplierForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        var originalText = submitBtn.html();
        
        // Disable button and show loading
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: baseUrl + 'suppliers/create',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#AddNewModal').modal('hide');
                    showToast('success', response.message || 'Supplier added successfully!');
                    dataTable.ajax.reload(null, false); // Reload table data
                    form[0].reset(); // Reset form
                } else {
                    if (response.errors) {
                        // Display validation errors
                        $.each(response.errors, function(key, value) {
                            var input = form.find('[name="' + key + '"]');
                            input.addClass('is-invalid');
                            input.after('<div class="invalid-feedback">' + value + '</div>');
                        });
                        showToast('error', 'Please fix the form errors');
                    } else {
                        showToast('error', response.message || 'Failed to add supplier.');
                    }
                }
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr.responseText);
                showToast('error', 'Server error occurred. Please try again.');
            },
            complete: function() {
                // Re-enable button and restore text
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Clear form errors when modal is closed
    $('#AddNewModal').on('hidden.bs.modal', function() {
        $('#addSupplierForm')[0].reset();
        $('#addSupplierForm .is-invalid').removeClass('is-invalid');
        $('#addSupplierForm .invalid-feedback').remove();
    });

    // View Supplier
    $(document).on('click', '.view-btn', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: baseUrl + 'suppliers/getSupplier/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var supplier = response.data;
                    
                    // Populate view modal
                    $('#view_name').text(supplier.name || '-');
                    $('#view_contact_person').text(supplier.contact_person || '-');
                    $('#view_email').text(supplier.email || '-');
                    $('#view_phone').text(supplier.phone || '-');
                    $('#view_address').text(supplier.address || '-');
                    $('#view_status').html(supplier.is_active == 1 ? 
                        '<span class="badge badge-success">Active</span>' : 
                        '<span class="badge badge-danger">Inactive</span>');
                    $('#view_created_at').text(supplier.created_at ? new Date(supplier.created_at).toLocaleString() : '-');
                    $('#view_updated_at').text(supplier.updated_at ? new Date(supplier.updated_at).toLocaleString() : '-');
                    
                    $('#viewSupplierModal').modal('show');
                } else {
                    showToast('error', response.message || 'Failed to load supplier details');
                }
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr.responseText);
                showToast('error', 'Server error occurred');
            }
        });
    });

    // Edit Supplier - Load data into edit modal
    $(document).on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: baseUrl + 'suppliers/getSupplier/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var supplier = response.data;
                    
                    // Populate edit modal
                    $('#supplierId').val(supplier.id);
                    $('#name').val(supplier.name);
                    $('#contact_person').val(supplier.contact_person || '');
                    $('#email').val(supplier.email || '');
                    $('#phone').val(supplier.phone || '');
                    $('#address').val(supplier.address || '');
                    $('#is_active').val(supplier.is_active);
                    
                    $('#editSupplierModal').modal('show');
                } else {
                    showToast('error', response.message || 'Failed to load supplier data');
                }
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr.responseText);
                showToast('error', 'Server error occurred');
            }
        });
    });

    // Submit Edit Supplier Form
    $('#editSupplierForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#supplierId').val();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        var originalText = submitBtn.html();
        
        // Disable button and show loading
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
        
        $.ajax({
            url: baseUrl + 'suppliers/update/' + id,
            type: 'POST',
            data: form.serialize() + '&_method=PUT', // Spoof PUT method
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#editSupplierModal').modal('hide');
                    showToast('success', response.message || 'Supplier updated successfully!');
                    dataTable.ajax.reload(null, false); // Reload table data
                } else {
                    if (response.errors) {
                        // Display validation errors
                        $.each(response.errors, function(key, value) {
                            var input = form.find('[name="' + key + '"]');
                            input.addClass('is-invalid');
                            input.after('<div class="invalid-feedback">' + value + '</div>');
                        });
                        showToast('error', 'Please fix the form errors');
                    } else {
                        showToast('error', response.message || 'Failed to update supplier.');
                    }
                }
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr.responseText);
                showToast('error', 'Server error occurred. Please try again.');
            },
            complete: function() {
                // Re-enable button and restore text
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Clear edit form errors when modal is closed
    $('#editSupplierModal').on('hidden.bs.modal', function() {
        $('#editSupplierForm .is-invalid').removeClass('is-invalid');
        $('#editSupplierForm .invalid-feedback').remove();
    });

    // Delete Supplier
    $(document).on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        var supplierName = $(this).closest('tr').find('td:eq(2)').text(); // Get supplier name from table
        
        if (confirm('Are you sure you want to delete supplier "' + supplierName + '"? This action cannot be undone.')) {
            $.ajax({
                url: baseUrl + 'suppliers/delete/' + id,
                type: 'DELETE',
                data: {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        showToast('success', response.message || 'Supplier deleted successfully!');
                        dataTable.ajax.reload(null, false); // Reload table data
                    } else {
                        showToast('error', response.message || 'Failed to delete supplier.');
                    }
                },
                error: function(xhr) {
                    console.error('AJAX Error:', xhr.responseText);
                    showToast('error', 'Server error occurred. Please try again.');
                }
            });
        }
    });

    // Load supplier count on dashboard (if element exists)
    if ($('#supplierCount').length) {
        loadSupplierCount();
    }
});

// Supplier count function (called on dashboard)
function loadSupplierCount() {
    $.ajax({
        url: baseUrl + 'suppliers/getCount',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.count !== undefined) {
                $('#supplierCount').text(response.count);
            } else if (response.status === 'success' && response.data) {
                $('#supplierCount').text(response.data.total || '0');
            } else {
                $('#supplierCount').text('0');
            }
        },
        error: function(xhr) {
            $('#supplierCount').text('Error');
            console.error('Failed to load supplier count:', xhr.status);
        }
    });
}

// Helper function to refresh the suppliers table
function refreshSuppliersTable() {
    $('#example1').DataTable().ajax.reload(null, false);
}

// Export function for suppliers data (if needed)
function exportSuppliers() {
    window.location.href = baseUrl + 'suppliers/export';
}