// categories.js - Complete working version

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

// Helper function to get CSRF data from meta tags
function getCsrfData() {
    var csrfTokenName = $('meta[name="csrf-token-name"]').attr('content');
    var csrfHash = $('meta[name="csrf-token-hash"]').attr('content');
    var data = {};
    data[csrfTokenName] = csrfHash;
    return data;
}

$(document).ready(function() {
    // Initialize DataTable
    var dataTable = $('#categoriesTable').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": baseUrl + 'categories/getCategoriesData',
            "type": "GET",
            "dataType": "json",
            "dataSrc": function(json) {
                if (json.status === 'success') {
                    return json.data;
                } else {
                    showToast('error', 'Failed to load categories data');
                    return [];
                }
            },
            "error": function(xhr, status, error) {
                console.error('DataTable AJAX Error:', error);
                showToast('error', 'Failed to load categories');
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
            { "data": "description", "defaultContent": "-" },
            { 
                "data": "products_count",
                "render": function(data) {
                    return data || 0;
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
                "data": "created_at",
                "render": function(data) {
                    return data ? new Date(data).toLocaleDateString() : '-';
                }
            },
            {
                "data": null,
                "render": function(data) {
                    return `
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-info view-category" data-id="${data.id}" title="View">
                                <i class="fa fa-eye"></i>
                            </button>
                            <button class="btn btn-warning edit-category" data-id="${data.id}" title="Edit">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger delete-category" data-id="${data.id}" data-name="${data.name}" data-products="${data.products_count || 0}" title="Delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        "order": [[1, 'asc']],
        "language": {
            "emptyTable": "No categories found",
            "zeroRecords": "No matching categories found"
        }
    });

    // Add New Category - Using your button ID
    $('#addNewCategoryBtn').on('click', function(e) {
        e.preventDefault();
        $('#addCategoryForm')[0].reset();
        $('#AddNewModal').modal('show');
    });

    // Submit Add Category Form
    $('#addCategoryForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        var originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        
        var csrfData = getCsrfData();
        var formData = form.serialize();
        
        // Add CSRF to form data if not present
        for (var key in csrfData) {
            if (formData.indexOf(key) === -1) {
                formData += '&' + key + '=' + encodeURIComponent(csrfData[key]);
            }
        }
        
        $.ajax({
            url: baseUrl + 'categories/store',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#AddNewModal').modal('hide');
                    showToast('success', response.message || 'Category added successfully!');
                    dataTable.ajax.reload(null, false);
                    form[0].reset();
                } else {
                    if (response.errors) {
                        $.each(response.errors, function(key, value) {
                            var input = form.find('[name="' + key + '"]');
                            input.addClass('is-invalid');
                            input.after('<div class="invalid-feedback">' + value + '</div>');
                        });
                        showToast('error', 'Please fix the form errors');
                    } else {
                        showToast('error', response.message || 'Failed to add category.');
                    }
                }
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr.responseText);
                showToast('error', 'Server error occurred. Please try again.');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // View Category
    $(document).on('click', '.view-category', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: baseUrl + 'categories/getCategory/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var category = response.data;
                    $('#view_name').text(category.name || '-');
                    $('#view_description').text(category.description || '-');
                    $('#view_products_count').text(category.products_count || '0');
                    $('#view_status').html(category.is_active == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>');
                    $('#view_created_at').text(category.created_at ? new Date(category.created_at).toLocaleString() : '-');
                    $('#view_updated_at').text(category.updated_at ? new Date(category.updated_at).toLocaleString() : '-');
                    $('#viewCategoryModal').modal('show');
                } else {
                    showToast('error', response.message || 'Failed to load category details');
                }
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr.responseText);
                showToast('error', 'Server error occurred');
            }
        });
    });

    // Edit Category
    $(document).on('click', '.edit-category', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: baseUrl + 'categories/getCategory/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var category = response.data;
                    $('#categoryId').val(category.id);
                    $('#name').val(category.name);
                    $('#description').val(category.description || '');
                    $('#is_active').val(category.is_active);
                    $('#editCategoryModal').modal('show');
                } else {
                    showToast('error', response.message || 'Failed to load category data');
                }
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr.responseText);
                showToast('error', 'Server error occurred');
            }
        });
    });

    // Submit Edit Category Form
    $('#editCategoryForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#categoryId').val();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        var originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
        
        var csrfData = getCsrfData();
        var formData = form.serialize();
        
        for (var key in csrfData) {
            if (formData.indexOf(key) === -1) {
                formData += '&' + key + '=' + encodeURIComponent(csrfData[key]);
            }
        }
        
        $.ajax({
            url: baseUrl + 'categories/update/' + id,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#editCategoryModal').modal('hide');
                    showToast('success', response.message || 'Category updated successfully!');
                    dataTable.ajax.reload(null, false);
                } else {
                    if (response.errors) {
                        $.each(response.errors, function(key, value) {
                            var input = form.find('[name="' + key + '"]');
                            input.addClass('is-invalid');
                            input.after('<div class="invalid-feedback">' + value + '</div>');
                        });
                        showToast('error', 'Please fix the form errors');
                    } else {
                        showToast('error', response.message || 'Failed to update category.');
                    }
                }
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr.responseText);
                showToast('error', 'Server error occurred. Please try again.');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Delete Category - FIXED VERSION
    $(document).on('click', '.delete-category', function() {
        var id = $(this).data('id');
        var categoryName = $(this).data('name');
        var productsCount = $(this).data('products');
        
        // Check if category has products
        if (productsCount > 0) {
            // Show warning modal with products list
            $('#productsList').html('<li>' + categoryName + ' has ' + productsCount + ' product(s)</li>');
            $('#warningModal').modal('show');
            return;
        }
        
        if (confirm('Are you sure you want to delete category "' + categoryName + '"? This action cannot be undone.')) {
            var csrfData = getCsrfData();
            var postData = {
                '_method': 'DELETE'
            };
            
            for (var key in csrfData) {
                postData[key] = csrfData[key];
            }
            
            $.ajax({
                url: baseUrl + 'categories/delete/' + id,
                type: 'POST',
                data: postData,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        showToast('success', response.message || 'Category deleted successfully!');
                        dataTable.ajax.reload(null, false);
                    } else {
                        showToast('error', response.message || 'Failed to delete category.');
                    }
                },
                error: function(xhr) {
                    console.error('AJAX Error:', xhr.responseText);
                    showToast('error', 'Server error occurred. Please try again.');
                }
            });
        }
    });

    // Clear form errors when modals are closed
    $('#AddNewModal').on('hidden.bs.modal', function() {
        $('#addCategoryForm .is-invalid').removeClass('is-invalid');
        $('#addCategoryForm .invalid-feedback').remove();
        $('#addCategoryForm')[0].reset();
    });
    
    $('#editCategoryModal').on('hidden.bs.modal', function() {
        $('#editCategoryForm .is-invalid').removeClass('is-invalid');
        $('#editCategoryForm .invalid-feedback').remove();
    });
});