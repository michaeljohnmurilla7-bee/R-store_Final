// public/js/suppliers/suppliers.js - FIXED VERSION
$(document).ready(function() {
    console.log('Suppliers JS loaded');
    
    var baseUrl = window.baseUrl || window.location.origin;
    baseUrl = baseUrl.replace(/\/$/, '');
    
    // CSRF Functions
    function getCsrfToken() {
        var tokenHash = $('meta[name="csrf-token-hash"]').attr('content');
        var tokenName = $('meta[name="csrf-token-name"]').attr('content');
        return { name: tokenName || 'csrf_test_name', hash: tokenHash || '' };
    }
    
    function refreshCSRF() {
        $.ajax({
            url: baseUrl + '/suppliers/refreshCSRF',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.csrf_hash) {
                    $('meta[name="csrf-token-name"]').attr('content', response.csrf_token);
                    $('meta[name="csrf-token-hash"]').attr('content', response.csrf_hash);
                    console.log('CSRF refreshed');
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
                var csrf = getCsrfToken();
                d[csrf.name] = csrf.hash;
                return d;
            }
        },
        "columns": [
            { 
                "data": null,
                "render": function(data, type, row, meta) {
                    return meta.row + 1 + meta.settings._iDisplayStart;
                }
            },
            { "data": "id", "visible": false },
            { "data": "name" },
            { "data": "contact_person", "defaultContent": "—" },
            { "data": "email", "defaultContent": "—" },
            { "data": "phone", "defaultContent": "—" },
            { "data": "address", "defaultContent": "—" },
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
                        '<button class="btn btn-danger btn-sm delete-supplier" data-id="' + row.id + '"><i class="fa fa-trash"></i></button>' +
                        '</div>';
                }
            }
        ]
    });
    
    // ============ ADD SUPPLIER ============
    $('#addSupplierForm').on('submit', function(e) {
        e.preventDefault();
        console.log('Add form submitted');
        
        var submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        
        // Get form data as object
        var formDataObj = {
            name: $('#addSupplierForm [name="name"]').val(),
            contact_person: $('#addSupplierForm [name="contact_person"]').val(),
            email: $('#addSupplierForm [name="email"]').val(),
            phone: $('#addSupplierForm [name="phone"]').val(),
            address: $('#addSupplierForm [name="address"]').val(),
            city: $('#addSupplierForm [name="city"]').val(),
            state: $('#addSupplierForm [name="state"]').val(),
            postal_code: $('#addSupplierForm [name="postal_code"]').val(),
            country: $('#addSupplierForm [name="country"]').val(),
            tax_number: $('#addSupplierForm [name="tax_number"]').val(),
            is_active: $('#addSupplierForm [name="is_active"]').val(),
            notes: $('#addSupplierForm [name="notes"]').val()
        };
        
        // Add CSRF
        var csrf = getCsrfToken();
        formDataObj[csrf.name] = csrf.hash;
        
        console.log('Sending data:', formDataObj);
        
        $.ajax({
            url: baseUrl + '/suppliers/addSupplier',
            type: 'POST',
            data: formDataObj,
            dataType: 'json',
            success: function(response) {
                console.log('Response:', response);
                if (response.status === 'success') {
                    $('#AddNewModal').modal('hide');
                    alert('✓ ' + response.message);
                    table.ajax.reload();
                    $('#addSupplierForm')[0].reset();
                    refreshCSRF();
                } else {
                    let errorMsg = typeof response.message === 'object' ? JSON.stringify(response.message) : response.message;
                    alert('✗ Error: ' + errorMsg);
                }
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr);
                let errorMsg = 'Server Error: ';
                try {
                    var response = JSON.parse(xhr.responseText);
                    errorMsg += response.message || xhr.statusText;
                } catch(e) {
                    errorMsg += xhr.statusText;
                }
                alert(errorMsg);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Supplier');
            }
        });
    });
    
    // ============ EDIT - Load Data ============
    $(document).on('click', '.edit-supplier', function() {
        var id = $(this).data('id');
        console.log('Loading supplier ID:', id);
        
        $.ajax({
            url: baseUrl + '/suppliers/getSupplier/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Supplier data:', response);
                if (response.status === 'success') {
                    var data = response.data;
                    $('#supplierId').val(data.id);
                    $('#editSupplierForm #name').val(data.name || '');
                    $('#editSupplierForm #contact_person').val(data.contact_person || '');
                    $('#editSupplierForm #email').val(data.email || '');
                    $('#editSupplierForm #phone').val(data.phone || '');
                    $('#editSupplierForm #address').val(data.address || '');
                    $('#editSupplierForm #city').val(data.city || '');
                    $('#editSupplierForm #state').val(data.state || '');
                    $('#editSupplierForm #postal_code').val(data.postal_code || '');
                    $('#editSupplierForm #country').val(data.country || '');
                    $('#editSupplierForm #tax_number').val(data.tax_number || '');
                    $('#editSupplierForm #is_active').val(data.is_active || 1);
                    $('#editSupplierForm #notes').val(data.notes || '');
                    $('#editSupplierModal').modal('show');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                alert('Failed to load supplier data');
            }
        });
    });
    
    // ============ UPDATE SUPPLIER ============
    $('#editSupplierForm').on('submit', function(e) {
        e.preventDefault();
        console.log('Update form submitted');
        
        var submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
        
        // Get form data as object
        var formDataObj = {
            id: $('#supplierId').val(),
            name: $('#editSupplierForm #name').val(),
            contact_person: $('#editSupplierForm #contact_person').val(),
            email: $('#editSupplierForm #email').val(),
            phone: $('#editSupplierForm #phone').val(),
            address: $('#editSupplierForm #address').val(),
            city: $('#editSupplierForm #city').val(),
            state: $('#editSupplierForm #state').val(),
            postal_code: $('#editSupplierForm #postal_code').val(),
            country: $('#editSupplierForm #country').val(),
            tax_number: $('#editSupplierForm #tax_number').val(),
            is_active: $('#editSupplierForm #is_active').val(),
            notes: $('#editSupplierForm #notes').val()
        };
        
        // Add CSRF
        var csrf = getCsrfToken();
        formDataObj[csrf.name] = csrf.hash;
        
        console.log('Updating with data:', formDataObj);
        
        $.ajax({
            url: baseUrl + '/suppliers/updateSupplier',
            type: 'POST',
            data: formDataObj,
            dataType: 'json',
            success: function(response) {
                console.log('Update response:', response);
                if (response.status === 'success') {
                    $('#editSupplierModal').modal('hide');
                    alert('✓ ' + response.message);
                    table.ajax.reload();
                    refreshCSRF();
                } else {
                    let errorMsg = typeof response.message === 'object' ? JSON.stringify(response.message) : response.message;
                    alert('✗ Error: ' + errorMsg);
                }
            },
            error: function(xhr) {
                console.error('Update error:', xhr);
                let errorMsg = 'Server Error: ';
                try {
                    var response = JSON.parse(xhr.responseText);
                    errorMsg += response.message || xhr.statusText;
                } catch(e) {
                    errorMsg += xhr.statusText;
                }
                alert(errorMsg);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Update Supplier');
            }
        });
    });
    
    // ============ VIEW SUPPLIER ============
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
                    
                    var addressParts = [data.address, data.city, data.state, data.postal_code, data.country].filter(function(v) { return v && v != ''; });
                    $('#view_address').text(addressParts.join(', ') || '-');
                    $('#view_notes').text(data.notes || '-');
                    
                    $('#viewSupplierModal').modal('show');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Failed to load supplier details');
            }
        });
    });
    
    // ============ DELETE SUPPLIER ============
    $(document).on('click', '.delete-supplier', function() {
        var id = $(this).data('id');
        var name = $(this).data('name') || 'this supplier';
        
        if (confirm('Are you sure you want to delete "' + name + '"?')) {
            var csrf = getCsrfToken();
            var postData = { id: id };
            postData[csrf.name] = csrf.hash;
            
            $.ajax({
                url: baseUrl + '/suppliers/deleteSupplier',
                type: 'POST',
                data: postData,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert('✓ ' + response.message);
                        table.ajax.reload();
                        refreshCSRF();
                    } else {
                        alert('✗ ' + (response.message || 'Error deleting supplier'));
                    }
                },
                error: function(xhr) {
                    alert('Failed to delete supplier');
                }
            });
        }
    });
    
    // ============ BUTTON HANDLERS ============
    $('#addNewSupplierBtn').click(function() {
        $('#addSupplierForm')[0].reset();
        $('#AddNewModal').modal('show');
    });
    
    // Refresh CSRF every 5 minutes
    setInterval(refreshCSRF, 300000);
    refreshCSRF();
    
    console.log('JS Ready - Add and Edit should work now');
});