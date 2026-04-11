<?php $isEdit = isset($category); ?>
<form id="categoryForm" action="<?= $isEdit ? base_url('categories/'.$category['id']) : base_url('categories') ?>" method="POST">
    <?= csrf_field() ?>
    <?php if($isEdit): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>
    <div class="form-group">
        <label>Name</label>
        <input type="text" class="form-control" name="name" value="<?= old('name', $category['name'] ?? '') ?>" required>
        <small class="text-danger" id="name_error"></small>
    </div>
    <div class="form-group">
        <label>Description</label>
        <textarea class="form-control" name="description"><?= old('description', $category['description'] ?? '') ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
</form>