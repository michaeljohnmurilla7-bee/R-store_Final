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

$(document).ready(function() {
    console.log('Categories JS loaded');
    
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
                console.log('DataTable response:', json);
                if (json.status === 'success') {
                    return json.data;
                } else {
                    showToast('error', 'Failed to load categories data');
                    return [];
                }
            },
            "error": function(xhr, status, error) {
                console.error('DataTable AJAX Error:', error);
                console.log('XHR Response:', xhr.responseText);
                showToast('error', 'Failed to load categories');
                return [];
            }
        },
        "columns": [
            { 
                // Column 0: No.
                "data": null,
                "render": function(data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { 
                // Column 1: ID (hidden)
                "data": "id", 
                "visible": false 
            },
            { 
                // Column 2: Category Name
                "data": "name" 
            },
            { 
                // Column 3: Description
                "data": "description",
                "render": function(data) {
                    if (data) {
                        return data.length > 50 ? data.substring(0, 50) + '...' : data;
                    }
                    return '-';
                }
            },
            { 
                // Column 4: Products Count
                "data": "products_count",
                "defaultContent": "0",
                "render": function(data) {
                    return data ? data : '0';
                }
            },
            { 
                // Column 5: Status
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
                // Column 6: Created At
                "data": "created_at",
                "render": function(data) {
                    if (data) {
                        let date = new Date(data);
                        return date.toLocaleDateString();
                    }
                    return '-';
                }
            },
            { 
                // Column 7: Actions
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
                            <button class="btn btn-danger delete-category" data-id="${data.id}" data-name="${escapeHtml(data.name)}" title="Delete">
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
            "zeroRecords": "No matching categories found",
            "search": "Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        }
    });

    // Helper function to escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // Add New Category Button
    $('#addNewCategoryBtn').off('click').on('click', function(e) {
        e.preventDefault();
        console.log('Add button clicked');
        $('#addCategoryForm')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        $('#AddNewModal').modal('show');
    });

    // Submit Add Category Form
    $('#addCategoryForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted');
        
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        var originalText = submitBtn.html();
        var formData = form.serialize();
        
        console.log('Form data:', formData);
        
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: baseUrl + 'categories/store',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('AJAX Success:', response);
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
                            if (input.next('.invalid-feedback').length === 0) {
                                input.after('<div class="invalid-feedback">' + value + '</div>');
                            } else {
                                input.next('.invalid-feedback').text(value);
                            }
                        });
                        showToast('error', 'Please fix the form errors');
                    } else {
                        showToast('error', response.message || 'Failed to add category.');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.log('XHR Status:', xhr.status);
                console.log('XHR Response:', xhr.responseText);
                
                let errorMessage = 'Server error occurred. Please try again.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) errorMessage = response.message;
                    if (response.errors) {
                        errorMessage = Object.values(response.errors).join(', ');
                    }
                } catch(e) {
                    console.error('Error parsing response:', e);
                }
                
                showToast('error', errorMessage);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Clear form errors when modal is closed
    $('#AddNewModal').on('hidden.bs.modal', function() {
        $('#addCategoryForm')[0].reset();
        $('#addCategoryForm .is-invalid').removeClass('is-invalid');
        $('#addCategoryForm .invalid-feedback').remove();
    });

    // View Category
    $(document).on('click', '.view-category', function() {
        var id = $(this).data('id');
        console.log('View category:', id);
        
        $.ajax({
            url: baseUrl + 'categories/getCategory/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('View response:', response);
                if (response.status === 'success') {
                    var category = response.data;
                    
                    $('#view_name').text(category.name || '-');
                    $('#view_description').text(category.description || '-');
                    $('#view_products_count').text(category.products_count || '0');
                    $('#view_status').html(category.is_active == 1 ? 
                        '<span class="badge badge-success">Active</span>' : 
                        '<span class="badge badge-danger">Inactive</span>');
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
        console.log('Edit category:', id);
        
        $.ajax({
            url: baseUrl + 'categories/getCategory/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Edit response:', response);
                if (response.status === 'success') {
                    var category = response.data;
                    
                    $('#categoryId').val(category.id);
                    $('#editCategoryForm input[name="name"]').val(category.name);
                    $('#editCategoryForm textarea[name="description"]').val(category.description || '');
                    $('#editCategoryForm select[name="is_active"]').val(category.is_active);
                    
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
    $('#editCategoryForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        var id = $('#categoryId').val();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        var originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
        
        $.ajax({
            url: baseUrl + 'categories/update/' + id,
            type: 'POST',
            data: form.serialize(),
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
                            if (input.next('.invalid-feedback').length === 0) {
                                input.after('<div class="invalid-feedback">' + value + '</div>');
                            }
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

    // Clear edit form errors when modal is closed
    $('#editCategoryModal').on('hidden.bs.modal', function() {
        $('#editCategoryForm .is-invalid').removeClass('is-invalid');
        $('#editCategoryForm .invalid-feedback').remove();
    });

    // Delete Category
    $(document).on('click', '.delete-category', function() {
        var id = $(this).data('id');
        var categoryName = $(this).data('name');
        
        if (confirm('Are you sure you want to delete category "' + categoryName + '"? This action cannot be undone.')) {
            $.ajax({
                url: baseUrl + 'categories/delete/' + id,
                type: 'DELETE',
                data: {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
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
});

// Helper function to refresh the categories table
function refreshCategoriesTable() {
    if ($.fn.DataTable.isDataTable('#categoriesTable')) {
        $('#categoriesTable').DataTable().ajax.reload(null, false);
    }
}