<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Categories</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Categories</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <?= view('_partials/_alerts') ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Category List</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#categoryModal" id="addCategoryBtn">
                            <i class="fas fa-plus"></i> Add Category
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="categoriesTable">
                        <thead>
                            <tr><th>Name</th><th>Description</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td><?= esc($cat['name']) ?></td>
                                <td><?= esc($cat['description']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info edit-cat" data-id="<?= $cat['id'] ?>"><i class="fas fa-edit"></i></button>
                                    <a href="<?= base_url('categories/delete/'.$cat['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add/Edit Category</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" id="categoryModalBody"></div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(function(){
    $('#categoriesTable').DataTable();
    $('#addCategoryBtn').click(function(){
        $('#categoryModal .modal-title').text('Add Category');
        $.get('<?= base_url('categories/create') ?>', function(data){
            if(data.status === 'success') $('#categoryModalBody').html(data.html);
        });
    });
    $('.edit-cat').click(function(){
        let id = $(this).data('id');
        $('#categoryModal .modal-title').text('Edit Category');
        $.get('<?= base_url('categories') ?>/'+id+'/edit', function(data){
            if(data.status === 'success') $('#categoryModalBody').html(data.html);
        });
    });
    $(document).on('submit', '#categoryForm', function(e){
        e.preventDefault();
        let form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: form.serialize(),
            dataType: 'json',
            success: function(res){
                if(res.status === 'success'){
                    $('#categoryModal').modal('hide');
                    location.reload();
                } else {
                    $.each(res.errors, function(k,v){ $('#'+k+'_error').text(v); });
                }
            }
        });
    });
});
</script>
<?= $this->endSection() ?>