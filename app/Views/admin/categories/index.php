<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px; padding:15px;">
    <div class="col-lg-8"><h2>Categories &amp; Subcategories</h2></div>
    <div class="col-lg-4 text-right">
        <?php if (! empty($canCreate)): ?>
        <button class="btn btn-primary" id="btn-add-category"><i class="fa fa-plus"></i> Add Category</button>
        <?php endif; ?>
    </div>
</div>

<div class="ibox">
    <div class="ibox-title">
        <h5>Category List</h5>
        <div class="ibox-tools">
            <input type="text" id="category-search" class="form-control form-control-sm" placeholder="Search..." style="width:220px;display:inline-block;">
        </div>
    </div>
    <div class="ibox-content">
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="categories-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Parent</th>
                    <th>Type</th>
                    <th>Slug</th>
                    <th>Sort</th>
                    <th>Status</th>
                    <th width="140">Actions</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal inmodal" id="category-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content animated fadeIn">
            <form id="category-form" enctype="multipart/form-data">
                <div class="modal-header navy-bg">
                    <button type="button" class="close" data-dismiss="modal" style="color:#fff;"><span>&times;</span></button>
                    <h4 class="modal-title" id="category-modal-title">Add Category</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="category-id">
                    <div class="form-group">
                        <label>Parent Category</label>
                        <select class="form-control" name="parent_id" id="category-parent">
                            <option value="">— Top-level category —</option>
                        </select>
                        <small class="text-muted">Leave empty for a main category. Choose a parent to create a subcategory.</small>
                    </div>
                    <div class="form-group"><label>Name *</label><input type="text" class="form-control" name="name" id="category-name" required></div>
                    <div class="form-group"><label>Description</label><textarea class="form-control" name="description" id="category-description" rows="3"></textarea></div>
                    <div class="form-group"><label>Image</label><input type="file" class="form-control" name="image" id="category-image" accept="image/*"></div>
                    <div class="form-group"><label>Sort Order</label><input type="number" class="form-control" name="sort_order" id="category-sort" value="0"></div>
                    <div class="form-group"><label>Status</label>
                        <select class="form-control" name="status" id="category-status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Meta Title</label><input type="text" class="form-control" name="meta_title" id="category-meta-title"></div>
                    <div class="form-group"><label>Meta Description</label><textarea class="form-control" name="meta_description" id="category-meta-description" rows="2"></textarea></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="category-save-btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
var canUpdate = <?= ! empty($canUpdate) ? 'true' : 'false' ?>;
var canDelete = <?= ! empty($canDelete) ? 'true' : 'false' ?>;
var parentOptions = [];

function fillParentSelect(selected, excludeId) {
    var html = '<option value="">— Top-level category —</option>';
    parentOptions.forEach(function (p) {
        if (excludeId && String(p.id) === String(excludeId)) return;
        var sel = String(selected || '') === String(p.id) ? ' selected' : '';
        html += '<option value="' + p.id + '"' + sel + '>' + p.name + '</option>';
    });
    $('#category-parent').html(html);
}

function loadCategories() {
    AdminApp.request(ADMIN_BASE + '/api/categories', 'GET', { search: $('#category-search').val() }).done(function (res) {
        parentOptions = res.data.parents || [];
        var html = '';
        (res.data.items || []).forEach(function (row) {
            var img = row.image ? '<img src="' + BASE_URL + row.image + '" style="height:40px;width:40px;object-fit:cover;border-radius:4px;">' : '-';
            var actions = '';
            if (canUpdate) actions += '<button class="btn btn-xs btn-primary btn-edit" data-id="' + row.id + '"><i class="fa fa-pencil"></i></button> ';
            if (canDelete) actions += '<button class="btn btn-xs btn-danger btn-delete" data-id="' + row.id + '"><i class="fa fa-trash"></i></button>';
            var typeBadge = row.parent_id
                ? '<span class="badge badge-warning">Subcategory</span>'
                : '<span class="badge badge-primary">Category</span>';
            html += '<tr>' +
                '<td>' + row.id + '</td>' +
                '<td>' + img + '</td>' +
                '<td>' + (row.display_name || row.name) + '</td>' +
                '<td>' + (row.parent_name || '—') + '</td>' +
                '<td>' + typeBadge + '</td>' +
                '<td>' + row.slug + '</td>' +
                '<td>' + row.sort_order + '</td>' +
                '<td>' + (row.status == 1 ? '<span class="badge badge-primary">Active</span>' : '<span class="badge badge-danger">Inactive</span>') + '</td>' +
                '<td>' + actions + '</td></tr>';
        });
        if (!html) html = '<tr><td colspan="9" class="text-center text-muted">No categories found</td></tr>';
        $('#categories-table tbody').html(html);
        fillParentSelect();
    });
}

function resetCategoryForm() {
    $('#category-form')[0].reset();
    $('#category-id').val('');
    fillParentSelect();
    $('#category-modal-title').text('Add Category / Subcategory');
}

$('#btn-add-category').on('click', function () {
    resetCategoryForm();
    $('#category-modal').modal('show');
});

$('#category-search').on('keyup', function () { loadCategories(); });

$('#category-form').on('submit', function (e) {
    e.preventDefault();
    var id = $('#category-id').val();
    var url = id ? (ADMIN_BASE + '/api/categories/' + id) : (ADMIN_BASE + '/api/categories');
    var fd = new FormData(this);
    var $btn = $('#category-save-btn');
    AdminApp.setButtonLoading($btn, true);
    AdminApp.request(url, 'POST', fd).done(function (res) {
        AdminApp.toast('success', res.message);
        $('#category-modal').modal('hide');
        loadCategories();
    }).always(function () { AdminApp.setButtonLoading($btn, false); });
});

$(document).on('click', '.btn-edit', function () {
    var id = $(this).data('id');
    AdminApp.request(ADMIN_BASE + '/api/categories/' + id, 'GET').done(function (res) {
        var row = res.data;
        fillParentSelect(row.parent_id, row.id);
        $('#category-id').val(row.id);
        $('#category-name').val(row.name);
        $('#category-description').val(row.description || '');
        $('#category-sort').val(row.sort_order);
        $('#category-status').val(row.status);
        $('#category-meta-title').val(row.meta_title || '');
        $('#category-meta-description').val(row.meta_description || '');
        $('#category-modal-title').text(row.parent_id ? 'Edit Subcategory' : 'Edit Category');
        $('#category-modal').modal('show');
    });
});

$(document).on('click', '.btn-delete', function () {
    var id = $(this).data('id');
    AdminApp.confirmDelete(function () {
        AdminApp.request(ADMIN_BASE + '/api/categories/' + id + '/delete', 'POST').done(function (res) {
            AdminApp.toast('success', res.message);
            loadCategories();
        });
    }, 'Delete this category? Subcategories under a parent will also be deleted.');
});

$(loadCategories);
</script>
<?= $this->endSection() ?>
