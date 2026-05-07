$(document).ready(function() {
    // Initialize DataTable
    var table = $('#customersTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": baseUrl + "/customers/getCustomersData",
            "type": "POST",
            "data": function(d) {
                d.<?= csrf_token() ?> = '<?= csrf_hash() ?>';
                return d;
            }
        },
        "columns": [
            {
                "data": null,
                "render": function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { "data": "name" },
            { "data": "phone" },
            { "data": "email" },
            { "data": "address" },
            { 
                "data": "created_at",
                "render": function(data) {
                    return data ? data : '-';
                }
            },
            { 
                "data": "updated_at",
                "render": function(data) {
                    return data ? data : '-';
                }
            },
            {
                "data": null,
                "render": function(data, type, row) {
                    return '<button class="btn btn-sm btn-info view-btn" data-id="' + row.id + '">' +
                           '<i class="fa fa-eye"></i></button> ' +
                           '<button class="btn btn-sm btn-warning edit-btn" data-id="' + row.id + '">' +
                           '<i class="fa fa-edit"></i></button> ' +
                           '<button class="btn btn-sm btn-danger delete-btn" data-id="' + row.id + '">' +
                           '<i class="fa fa-trash"></i></button>';
                },
                "orderable": false,
                "searchable": false
            }
        ],
        "order": [[0, "desc"]],
        "pageLength": 10
    });
    
    // Add New Customer button
    $('#addNewCustomerBtn').click(function() {
        $('#addCustomerForm')[0].reset();
        $('#AddNewModal').modal('show');
    });
    
    // Submit Add Form
    $('#addCustomerForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: baseUrl + "/customers/store",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    $('#AddNewModal').modal('hide');
                    table.ajax.reload();
                    alert(response.message);
                } else {
                    if (response.errors) {
                        var errorMsg = '';
                        for (var key in response.errors) {
                            errorMsg += response.errors[key] + '\n';
                        }
                        alert(errorMsg);
                    } else {
                        alert(response.message);
                    }
                }
            },
            error: function(xhr) {
                alert('Error: ' + xhr.statusText);
            }
        });
    });
    
    // View Customer
    $(document).on('click', '.view-btn', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: baseUrl + "/customers/getCustomer/" + id,
            type: "GET",
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    $('#view_name').text(response.data.name);
                    $('#view_phone').text(response.data.phone || '-');
                    $('#view_email').text(response.data.email);
                    $('#view_address').text(response.data.address || '-');
                    $('#view_created_at').text(response.data.created_at || '-');
                    $('#view_updated_at').text(response.data.updated_at || '-');
                    $('#viewCustomerModal').modal('show');
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Failed to fetch customer details');
            }
        });
    });
    
    // Edit Customer
    $(document).on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: baseUrl + "/customers/getCustomer/" + id,
            type: "GET",
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    $('#customerId').val(response.data.id);
                    $('#name').val(response.data.name);
                    $('#phone').val(response.data.phone);
                    $('#email').val(response.data.email);
                    $('#address').val(response.data.address);
                    $('#editCustomerModal').modal('show');
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Failed to fetch customer data');
            }
        });
    });
    
    // Submit Edit Form
    $('#editCustomerForm').submit(function(e) {
        e.preventDefault();
        
        var id = $('#customerId').val();
        
        $.ajax({
            url: baseUrl + "/customers/update/" + id,
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    $('#editCustomerModal').modal('hide');
                    table.ajax.reload();
                    alert(response.message);
                } else {
                    if (response.errors) {
                        var errorMsg = '';
                        for (var key in response.errors) {
                            errorMsg += response.errors[key] + '\n';
                        }
                        alert(errorMsg);
                    } else {
                        alert(response.message);
                    }
                }
            },
            error: function() {
                alert('Failed to update customer');
            }
        });
    });
    
    // Delete Customer
    $(document).on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this customer?')) {
            $.ajax({
                url: baseUrl + "/customers/delete/" + id,
                type: "DELETE",
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        table.ajax.reload();
                        alert(response.message);
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Failed to delete customer');
                }
            });
        }
    });
    
    // Export button
    $('#exportBtn').click(function() {
        window.location.href = baseUrl + "/customers/export";
    });
    
    // Import button
    $('#importBtn').click(function() {
        $('#importModal').modal('show');
    });
});