// suppliers.js - Complete working version
$(document).ready(function() {
    console.log('Suppliers JS loaded');
    console.log('Base URL:', baseUrl);
    
    // Refresh CSRF token function
    function refreshCSRF() {
        $.ajax({
            url: baseUrl + '/suppliers/refreshCSRF',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.csrf_token && response.csrf_hash) {
                    $('meta[name="csrf-token-name"]').attr('content', response.csrf_token);
                    $('meta[name="csrf-token-hash"]').attr('content', response.csrf_hash);
                }
            }
        });
    }
    
    // Initialize DataTable
    var table = $('#example1').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": baseUrl + "/suppliers/getSuppliers",
            "type": "POST",
            "data": function(d) {
                // Add CSRF token to request
                d.csrf_test_name = $('meta[name="csrf-token-hash"]').attr('content');
                console.log('Sending request with data:', d);
            },
            "dataSrc": function(json) {
                console.log('Data received from server:', json);
                if (json.data) {
                    return json.data;
                }
                return [];
            },
            "error": function(xhr, status, error) {
                console.log("DataTable Error - Status:", status);
                console.log("DataTable Error - XHR:", xhr);
                console.log("DataTable Error - Response:", xhr.responseText);
                alert('Error loading data. Check console for details.');
            }
        },
        "columns": [
            { 
                "data": null,
                "render": function(data, type, row, meta) {
                    return meta.row + 1 + (meta.settings._iDisplayStart);
                }
            },
            { "data": "id", "visible": false },
            { "data": "name" },
            { "data": "contact_person" },
            { "data": "email" },
            { "data": "phone" },
            { "data": "address" },
            { 
                "data": "is_active",
                "render": function(data) {
                    return data == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>';
                }
            },
            { 
                "data": null,
                "orderable": false,
                "render": function(data, type, row) {
                    return '<div class="btn-group btn-group-sm">' +
                        '<button class="btn btn-info btn-sm view-supplier" data-id="' + row.id + '"><i class="fa fa-eye"></i></button>' +
                        '<button class="btn btn-warning btn-sm edit-supplier" data-id="' + row.id + '"><i class="fa fa-edit"></i></button>' +
                        '<button class="btn btn-danger btn-sm delete-supplier" data-id="' + row.id + '" data-name="' + (row.name || '').replace(/'/g, "\\'") + '"><i class="fa fa-trash"></i></button>' +
                        '</div>';
                }
            }
        ],
        "order": [[1, 'asc']],
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
    });
    
    // Add New Supplier
    $('#addSupplierForm').on('submit', function(e) {
        e.preventDefault();
        
        console.log('Submitting add supplier form');
        
        var formData = $(this).serialize();
        console.log('Form data:', formData);
        
        var submitBtn = $('#addSupplierForm button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: baseUrl + '/suppliers/addSupplier',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('Add supplier response:', response);
                if (response.status === 'success') {
                    $('#AddNewModal').modal('hide');
                    alert('✅ ' + response.message);
                    table.ajax.reload();
                    $('#addSupplierForm')[0].reset();
                    refreshCSRF();
                } else {
                    var errorMsg = response.message || 'Error adding supplier';
                    if (typeof errorMsg === 'object') {
                        errorMsg = Object.values(errorMsg).join('\n');
                    }
                    alert('❌ ' + errorMsg);
                }
            },
            error: function(xhr) {
                console.error('Add supplier error:', xhr);
                alert('❌ Error: ' + xhr.status + ' - ' + xhr.statusText);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Supplier');
            }
        });
    });
    
    // Edit Supplier
    $(document).on('click', '.edit-supplier', function() {
        var id = $(this).data('id');
        console.log('Edit supplier ID:', id);
        
        $.ajax({
            url: baseUrl + '/suppliers/getSupplier/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Get supplier response:', response);
                if (response.status === 'success') {
                    var data = response.data;
                    $('#supplierId').val(data.id);
                    $('#editSupplierForm #name').val(data.name);
                    $('#editSupplierForm #contact_person').val(data.contact_person || '');
                    $('#editSupplierForm #email').val(data.email);
                    $('#editSupplierForm #phone').val(data.phone);
                    $('#editSupplierForm #address').val(data.address || '');
                    $('#editSupplierForm #city').val(data.city || '');
                    $('#editSupplierForm #state').val(data.state || '');
                    $('#editSupplierForm #postal_code').val(data.postal_code || '');
                    $('#editSupplierForm #country').val(data.country || '');
                    $('#editSupplierForm #tax_number').val(data.tax_number || '');
                    $('#editSupplierForm #is_active').val(data.is_active);
                    $('#editSupplierForm #notes').val(data.notes || '');
                    $('#editSupplierModal').modal('show');
                } else {
                    alert('❌ Error loading supplier data');
                }
            },
            error: function(xhr) {
                console.error('Get supplier error:', xhr);
                alert('❌ Error loading supplier data');
            }
        });
    });
    
    // Update Supplier
    $('#editSupplierForm').on('submit', function(e) {
        e.preventDefault();
        
        console.log('Submitting update form');
        
        var formData = $(this).serialize();
        var submitBtn = $('#editSupplierForm button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
        
        $.ajax({
            url: baseUrl + '/suppliers/updateSupplier',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('Update response:', response);
                if (response.status === 'success') {
                    $('#editSupplierModal').modal('hide');
                    alert('✅ ' + response.message);
                    table.ajax.reload();
                    refreshCSRF();
                } else {
                    var errorMsg = response.message || 'Error updating supplier';
                    if (typeof errorMsg === 'object') {
                        errorMsg = Object.values(errorMsg).join('\n');
                    }
                    alert('❌ ' + errorMsg);
                }
            },
            error: function(xhr) {
                console.error('Update error:', xhr);
                alert('❌ Error updating supplier');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Update Supplier');
            }
        });
    });
    
    // View Supplier
    $(document).on('click', '.view-supplier', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: baseUrl + '/suppliers/getSupplier/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var data = response.data;
                    $('#view_name').text(data.name || '-');
                    $('#view_contact_person').text(data.contact_person || '-');
                    $('#view_email').text(data.email || '-');
                    $('#view_phone').text(data.phone || '-');
                    $('#view_tax_number').text(data.tax_number || '-');
                    $('#view_status').html(data.is_active == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>');
                    $('#view_created_at').text(data.created_at || '-');
                    $('#view_updated_at').text(data.updated_at || '-');
                    
                    var address = [data.address, data.city, data.state, data.postal_code, data.country]
                        .filter(function(v) { return v && v != ''; })
                        .join(', ');
                    $('#view_address').text(address || '-');
                    $('#view_notes').text(data.notes || '-');
                    
                    $('#viewSupplierModal').modal('show');
                } else {
                    alert('❌ Error loading supplier details');
                }
            },
            error: function(xhr) {
                console.error('View error:', xhr);
                alert('❌ Error loading supplier details');
            }
        });
    });
    
    // Delete Supplier
    $(document).on('click', '.delete-supplier', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        if (confirm('Are you sure you want to delete "' + name + '"?')) {
            var csrfHash = $('meta[name="csrf-token-hash"]').attr('content');
            
            $.ajax({
                url: baseUrl + '/suppliers/deleteSupplier',
                type: 'POST',
                data: {
                    id: id,
                    csrf_test_name: csrfHash
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Delete response:', response);
                    if (response.status === 'success') {
                        alert('✅ ' + response.message);
                        table.ajax.reload();
                        refreshCSRF();
                    } else {
                        alert('❌ ' + response.message);
                    }
                },
                error: function(xhr) {
                    console.error('Delete error:', xhr);
                    alert('❌ Error deleting supplier');
                }
            });
        }
    });
    
    // Add New button click
    $('#addNewSupplierBtn').click(function() {
        $('#addSupplierForm')[0].reset();
        $('#AddNewModal').modal('show');
    });
    
    // Test the connection immediately
    console.log('Testing connection to server...');
    $.ajax({
        url: baseUrl + '/suppliers/getSuppliers',
        type: 'POST',
        data: {
            draw: 1,
            start: 0,
            length: 10
        },
        dataType: 'json',
        success: function(response) {
            console.log('Connection test SUCCESS!');
            console.log('Server response:', response);
            if (response.data && response.data.length > 0) {
                console.log('Found', response.data.length, 'suppliers in database');
            } else {
                console.log('No suppliers found in database');
            }
        },
        error: function(xhr) {
            console.error('Connection test FAILED!');
            console.error('Status:', xhr.status);
            console.error('Status Text:', xhr.statusText);
            console.error('Response:', xhr.responseText);
            alert('Cannot connect to server. Please check:\n1. Server is running\n2. Base URL is correct: ' + baseUrl + '\n3. Check browser console for details');
        }
    });
});