// products.js - Updated to work with Suppliers module

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
    
    // Load categories and suppliers for dropdowns
    loadCategoriesAndSuppliers();
    
    // Initialize DataTable
    var dataTable = $('#example1').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": baseUrl + 'products/getSuppliersData', // You'll need to create this endpoint
            "type": "GET",
            "dataType": "json",
            "dataSrc": function(json) {
                if (json.status === 'success') {
                    return json.data;
                } else {
                    showToast('error', 'Failed to load products data');
                    return [];
                }
            },
            "error": function(xhr, status, error) {
                console.error('DataTable AJAX Error:', error);
                showToast('error', 'Failed to load products');
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
            { "data": "sku" },
            { "data": "category_name", "defaultContent": "-" },
            { "data": "supplier_name", "defaultContent": "-" },
            { 
                "data": "cost_price",
                "render": function(data) {
                    return data ? '₱' + parseFloat(data).toFixed(2) : '₱0.00';
                }
            },
            { 
                "data": "price",
                "render": function(data) {
                    return '₱' + parseFloat(data).toFixed(2);
                }
            },
            { "data": "stock_qty" },
            { "data": "reorder_level" },
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
                            <button class="btn btn-info view-product" data-id="${data.id}" title="View">
                                <i class="fa fa-eye"></i>
                            </button>
                            <button class="btn btn-warning edit-product" data-id="${data.id}" title="Edit">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-success adjust-stock" data-id="${data.id}" data-name="${data.name}" title="Adjust Stock">
                                <i class="fa fa-exchange-alt"></i>
                            </button>
                            <button class="btn btn-danger delete-product" data-id="${data.id}" title="Delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        "order": [[1, 'asc']],
        "language": {
            "emptyTable": "No products found",
            "zeroRecords": "No matching products found"
        }
    });

    // Function to load categories and suppliers for dropdowns
    function loadCategoriesAndSuppliers() {
        // Load categories
        $.ajax({
            url: baseUrl + 'categories/getSelectList',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response && response.length > 0) {
                    // Populate add modal category dropdown
                    var addCategorySelect = $('#AddNewModal select[name="category_id"]');
                    addCategorySelect.empty();
                    addCategorySelect.append('<option value="">Select Category</option>');
                    $.each(response, function(key, category) {
                        addCategorySelect.append('<option value="' + category.id + '">' + category.text + '</option>');
                    });
                    
                    // Populate edit modal category dropdown
                    var editCategorySelect = $('#editProductModal select[name="category_id"]');
                    editCategorySelect.empty();
                    editCategorySelect.append('<option value="">Select Category</option>');
                    $.each(response, function(key, category) {
                        editCategorySelect.append('<option value="' + category.id + '">' + category.text + '</option>');
                    });
                }
            },
            error: function(xhr) {
                console.error('Failed to load categories:', xhr);
            }
        });
        
        // Load suppliers
        $.ajax({
        url: baseUrl + 'suppliers/getSelectList',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
        // FIX: Check response.status and response.data
        if (response.status === 'success' && response.data && response.data.length > 0) {
            // Populate add modal supplier dropdown
            var addSupplierSelect = $('#AddNewModal select[name="supplier_id"]');
            addSupplierSelect.empty();
            addSupplierSelect.append('<option value="">Select Supplier</option>');
            $.each(response.data, function(key, supplier) {
                addSupplierSelect.append('<option value="' + supplier.id + '">' + supplier.name + '</option>');
            });
            
            // Populate edit modal supplier dropdown
            var editSupplierSelect = $('#editProductModal select[name="supplier_id"]');
            editSupplierSelect.empty();
            editSupplierSelect.append('<option value="">Select Supplier</option>');
            $.each(response.data, function(key, supplier) {
                editSupplierSelect.append('<option value="' + supplier.id + '">' + supplier.name + '</option>');
            });
            console.log('Loaded ' + response.data.length + ' suppliers');
        } else if (response.status === 'success' && (!response.data || response.data.length === 0)) {
            // No suppliers found
            $('#AddNewModal select[name="supplier_id"]').html('<option value="">No suppliers available. Please add suppliers first.</option>');
            $('#editProductModal select[name="supplier_id"]').html('<option value="">No suppliers available. Please add suppliers first.</option>');
            console.log('No suppliers found in database');
        } else {
            // Error in response
            $('#AddNewModal select[name="supplier_id"]').html('<option value="">Error loading suppliers</option>');
            $('#editProductModal select[name="supplier_id"]').html('<option value="">Error loading suppliers</option>');
            console.error('Invalid response format:', response);
        }
    },
    error: function(xhr) {
        console.error('Failed to load suppliers:', xhr);
        $('#AddNewModal select[name="supplier_id"]').html('<option value="">Error loading suppliers</option>');
        $('#editProductModal select[name="supplier_id"]').html('<option value="">Error loading suppliers</option>');
    }
    });
    }

    // Add New Product Button
    $('#addNewProductBtn').on('click', function(e) {
        e.preventDefault();
        $('#addProductForm')[0].reset();
        // Refresh dropdowns
        loadCategoriesAndSuppliers();
        $('#AddNewModal').modal('show');
    });

    // Submit Add Product Form
    $('#addProductForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        var originalText = submitBtn.html();
        
        // Disable button and show loading
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: baseUrl + 'products/store',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#AddNewModal').modal('hide');
                    showToast('success', response.message || 'Product added successfully!');
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
                        showToast('error', response.message || 'Failed to add product.');
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

    // View Product
    $(document).on('click', '.view-product', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: baseUrl + 'products/getProduct/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var product = response.data;
                    
                    // Populate view modal
                    $('#view_name').text(product.name || '-');
                    $('#view_sku').text(product.sku || '-');
                    $('#view_category').text(product.category_name || '-');
                    $('#view_supplier').text(product.supplier_name || '-');
                    $('#view_cost_price').text(product.cost_price ? '$' + parseFloat(product.cost_price).toFixed(2) : '$0.00');
                    $('#view_price').text(product.price ? '$' + parseFloat(product.price).toFixed(2) : '$0.00');
                    $('#view_stock_qty').text(product.stock_qty || '0');
                    $('#view_reorder_level').text(product.reorder_level || '0');
                    $('#view_status').html(product.is_active == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>');
                    $('#view_created_at').text(product.created_at ? new Date(product.created_at).toLocaleString() : '-');
                    $('#view_updated_at').text(product.updated_at ? new Date(product.updated_at).toLocaleString() : '-');
                    $('#view_description').text(product.description || '-');
                    
                    $('#viewProductModal').modal('show');
                } else {
                    showToast('error', response.message || 'Failed to load product details');
                }
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr.responseText);
                showToast('error', 'Server error occurred');
            }
        });
    });

    // Edit Product - Load data into edit modal
    $(document).on('click', '.edit-product', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: baseUrl + 'products/getProduct/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var product = response.data;
                    
                    // Populate edit modal
                    $('#productId').val(product.id);
                    $('#name').val(product.name);
                    $('#sku').val(product.sku);
                    $('#category_id').val(product.category_id);
                    $('#supplier_id').val(product.supplier_id);
                    $('#cost_price').val(product.cost_price || '0.00');
                    $('#price').val(product.price);
                    $('#stock_qty').val(product.stock_qty || '0');
                    $('#reorder_level').val(product.reorder_level || '0');
                    $('#is_active').val(product.is_active);
                    $('#description').val(product.description || '');
                    
                    $('#editProductModal').modal('show');
                } else {
                    showToast('error', response.message || 'Failed to load product data');
                }
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr.responseText);
                showToast('error', 'Server error occurred');
            }
        });
    });

    // Submit Edit Product Form
    $('#editProductForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#productId').val();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        var originalText = submitBtn.html();
        
        // Disable button and show loading
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
        
        $.ajax({
            url: baseUrl + 'products/update/' + id,
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#editProductModal').modal('hide');
                    showToast('success', response.message || 'Product updated successfully!');
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
                        showToast('error', response.message || 'Failed to update product.');
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

    // Stock Adjustment
    $(document).on('click', '.adjust-stock', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        $('#adjust_product_id').val(id);
        $('#adjust_product_name').val(name);
        $('#stockAdjustmentForm')[0].reset();
        $('#stockAdjustmentModal').modal('show');
    });

    // Submit Stock Adjustment Form
    $('#stockAdjustmentForm').on('submit', function(e) {
        e.preventDefault();
        var productId = $('#adjust_product_id').val();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        var originalText = submitBtn.html();
        
        // Disable button and show loading
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
        
        $.ajax({
            url: baseUrl + 'products/restock/' + productId,
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#stockAdjustmentModal').modal('hide');
                    showToast('success', response.message || 'Stock adjusted successfully!');
                    dataTable.ajax.reload(null, false); // Reload table data
                } else {
                    showToast('error', response.message || 'Failed to adjust stock.');
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

 // Delete Product - FIXED VERSION
  $(document).on('click', '.delete-product', function() {
    var id = $(this).data('id');
    var productName = $(this).closest('tr').find('td:eq(2)').text();
    
    if (confirm('Are you sure you want to delete product "' + productName + '"? This action cannot be undone.')) {
        // Get CSRF values from meta tags (NO PHP CODE IN .JS FILE)
        var csrfTokenName = $('meta[name="csrf-token-name"]').attr('content');
        var csrfHash = $('meta[name="csrf-token-hash"]').attr('content');
        
        var postData = {
            '_method': 'DELETE'
        };
        // Add CSRF token dynamically
        postData[csrfTokenName] = csrfHash;
        
        $.ajax({
            url: baseUrl + 'products/delete/' + id,
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    showToast('success', response.message || 'Product deleted successfully!');
                    dataTable.ajax.reload(null, false);
                } else {
                    showToast('error', response.message || 'Failed to delete product.');
                }
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr.responseText);
                let errorMsg = 'Server error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showToast('error', errorMsg);
            }
        });
    }
 });

    // Clear form errors when modals are closed
    $('#AddNewModal').on('hidden.bs.modal', function() {
        $('#addProductForm .is-invalid').removeClass('is-invalid');
        $('#addProductForm .invalid-feedback').remove();
    });
    
    $('#editProductModal').on('hidden.bs.modal', function() {
        $('#editProductForm .is-invalid').removeClass('is-invalid');
        $('#editProductForm .invalid-feedback').remove();
    });

    // Load product count on dashboard (if element exists)
    if ($('#productCount').length) {
        loadProductCount();
    }
});

// Product count function (called on dashboard)
function loadProductCount() {
    $.ajax({
        url: baseUrl + 'products/getCount',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.count !== undefined) {
                $('#productCount').text(response.count);
            } else {
                $('#productCount').text('0');
            }
        },
        error: function(xhr) {
            $('#productCount').text('Error');
            console.error('Failed to load product count:', xhr.status);
        }
    });
}