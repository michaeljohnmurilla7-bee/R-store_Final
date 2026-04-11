// products.js - Compatible with rStore Products Module

// Toastr notification helper
// Ensure Bootstrap modal is properly initialized
$(document).ready(function() {
    // Test if modal works
    console.log('jQuery version:', $.fn.jquery);
    console.log('Bootstrap modal available:', typeof $.fn.modal !== 'undefined');
    
    // Manual modal trigger to avoid data-toggle issues
    $('#addNewProductBtn').on('click', function(e) {
        e.preventDefault();
        $('#AddNewModal').modal('show');
    });
});

function showToast(type, message) {
    if (typeof toastr !== 'undefined') {
        if (type === 'success') {
            toastr.success(message, 'Success');
        } else {
            toastr.error(message, 'Error');
        }
    } else {
        alert(message);
    }
}

$(document).ready(function() {
    // Initialize DataTable (client-side)
    $('#productsTable').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "order": [[1, 'asc']]
    });

    // Add Product Button
    $('#addProductBtn').click(function() {
        $('#productModalLabel').text('Add New Product');
        $.ajax({
            url: baseUrl + 'products/create',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#productModalBody').html(response.html);
                    $('#productModal').modal('show');
                } else {
                    showToast('error', 'Failed to load form.');
                }
            },
            error: function() {
                showToast('error', 'Server error.');
            }
        });
    });

    // Edit Product Button
    $(document).on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        $('#productModalLabel').text('Edit Product');
        $.ajax({
            url: baseUrl + 'products/' + id + '/edit',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#productModalBody').html(response.html);
                    $('#productModal').modal('show');
                } else {
                    showToast('error', response.message || 'Failed to load product.');
                }
            },
            error: function() {
                showToast('error', 'Server error.');
            }
        });
    });

    // Restock Button
    $(document).on('click', '.restock-btn', function() {
        var id = $(this).data('id');
        $.ajax({
            url: baseUrl + 'products/' + id + '/restock',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#restockModalBody').html(response.html);
                    $('#restockModal').modal('show');
                }
            },
            error: function() {
                showToast('error', 'Failed to load restock form.');
            }
        });
    });

    // Submit Product Form (Add/Edit) via AJAX
    $(document).on('submit', '#productForm', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var method = form.find('input[name="_method"]').val() || 'POST';

        $.ajax({
            url: url,
            type: method,
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#productModal').modal('hide');
                    showToast('success', response.message || 'Product saved successfully!');
                    setTimeout(function() { location.reload(); }, 1000);
                } else {
                    if (response.errors) {
                        $.each(response.errors, function(key, value) {
                            $('#' + key + '_error').text(value);
                        });
                    } else {
                        showToast('error', response.message || 'Failed to save.');
                    }
                }
            },
            error: function(xhr) {
                showToast('error', 'Server error occurred.');
                console.error(xhr.responseText);
            }
        });
    });

    // Submit Restock Form
    $(document).on('submit', '#restockForm', function(e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#restockModal').modal('hide');
                    showToast('success', response.message || 'Stock updated!');
                    setTimeout(function() { location.reload(); }, 1000);
                } else {
                    showToast('error', response.message || 'Restock failed.');
                }
            },
            error: function() {
                showToast('error', 'Server error.');
            }
        });
    });

    // Delete Product (confirmation handled by link class)
    $(document).on('click', '.delete-btn', function(e) {
        if (!confirm('Are you sure you want to delete this product?')) {
            e.preventDefault();
        }
    });

    // Load product count on dashboard
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