<?= $this->extend('admin/layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/riode-vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet">
<style>
    #category-modal .modal-dialog {
        width: 100%;
        max-width: 100%;
        height: 100%;
        margin: 0;
    }
    #category-modal .modal-content {
        height: 100vh;
        border-radius: 0;
        display: flex;
        flex-direction: column;
    }
    #category-modal #category-form {
        display: flex;
        flex-direction: column;
        height: 100%;
        margin: 0;
    }
    #category-modal .modal-body {
        flex: 1 1 auto;
        overflow-y: auto;
    }
    #category-modal .modal-header,
    #category-modal .modal-footer {
        flex: 0 0 auto;
    }
    .cat-icon-picker {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(64px, 1fr));
        gap: 8px;
        max-height: 280px;
        overflow-y: auto;
        padding: 10px;
        border: 1px solid #e5e6e7;
        border-radius: 4px;
        background: #fafafa;
    }
    .cat-icon-option {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        min-height: 58px;
        border: 1px solid #e5e6e7;
        border-radius: 4px;
        background: #fff;
        cursor: pointer;
        padding: 6px 4px;
        color: #444;
        transition: border-color .15s, background .15s, color .15s;
    }
    .cat-icon-option i {
        font-size: 18px;
    }
    .cat-icon-option span {
        font-size: 9px;
        line-height: 1.1;
        text-align: center;
        word-break: break-all;
        max-width: 100%;
    }
    .cat-icon-option:hover {
        border-color: #1ab394;
        color: #1ab394;
    }
    .cat-icon-option.is-selected {
        border-color: #ed5565;
        background: #fff5f5;
        color: #ed5565;
        box-shadow: 0 0 0 1px #ed5565;
    }
    .cat-icon-preview {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 8px;
        font-weight: 600;
    }
    .cat-icon-preview i {
        font-size: 20px;
        color: #ed5565;
    }
</style>
<?= $this->endSection() ?>

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
                    <th>Icon</th>
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
    <div class="modal-dialog modal-full">
        <div class="modal-content animated fadeIn">
            <form id="category-form" enctype="multipart/form-data">
                <div class="modal-header navy-bg">
                    <button type="button" class="close" data-dismiss="modal" style="color:#fff;"><span>&times;</span></button>
                    <h4 class="modal-title" id="category-modal-title">Add Category</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="category-id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Parent Category</label>
                                <select class="form-control" name="parent_id" id="category-parent">
                                    <option value="">— Top-level category —</option>
                                </select>
                                <small class="text-muted">Leave empty for a main category. Choose a parent to create a subcategory.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group"><label>Name *</label><input type="text" class="form-control" name="name" id="category-name" required></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Category Icon</label>
                        <input type="hidden" name="icon" id="category-icon" value="">
                        <div class="cat-icon-picker" id="category-icon-picker"></div>
                        <div class="cat-icon-preview" id="category-icon-preview">
                            <i class="fas fa-box"></i>
                            <span>Selected: fa-box</span>
                        </div>
                        <small class="text-muted">Choose an icon for the homepage sidebar / category menus.</small>
                    </div>

                    <div class="form-group"><label>Description</label><textarea class="form-control" name="description" id="category-description" rows="3" placeholder="Optional category description"></textarea></div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group"><label>Image</label><input type="file" class="form-control" name="image" id="category-image" accept="image/*"></div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group"><label>Sort Order</label><input type="number" class="form-control" name="sort_order" id="category-sort" value="0"></div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group"><label>Status</label>
                                <select class="form-control" name="status" id="category-status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
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
var CATEGORY_ICONS = [
    'fa-mobile-alt','fa-laptop','fa-tv','fa-door-closed','fa-tshirt','fa-snowflake','fa-th-large','fa-temperature-high',
    'fa-tint','fa-wind','fa-motorcycle','fa-box','fa-car-battery','fa-bed','fa-circle','fa-tablet-alt','fa-sun',
    'fa-blender','fa-couch','fa-camera','fa-headphones','fa-gamepad','fa-keyboard','fa-plug','fa-lightbulb',
    'fa-home','fa-shopping-bag','fa-shopping-cart','fa-gift','fa-star','fa-heart','fa-tools','fa-wrench','fa-cog',
    'fa-cogs','fa-wifi','fa-bolt','fa-fire','fa-utensils','fa-coffee','fa-print','fa-clock','fa-music','fa-book',
    'fa-baby','fa-dumbbell','fa-bicycle','fa-car','fa-truck','fa-microchip','fa-sim-card','fa-shoe-prints'
];

function fillParentSelect(selected, excludeId) {
    var html = '<option value="">— Top-level category —</option>';
    parentOptions.forEach(function (p) {
        if (excludeId && String(p.id) === String(excludeId)) return;
        var sel = String(selected || '') === String(p.id) ? ' selected' : '';
        html += '<option value="' + p.id + '"' + sel + '>' + p.name + '</option>';
    });
    $('#category-parent').html(html);
}

function normalizeIconClass(raw) {
    raw = String(raw || '').trim();
    if (!raw) return '';
    var parts = raw.split(/\s+/);
    for (var i = 0; i < parts.length; i++) {
        if (parts[i].indexOf('fa-') === 0) return parts[i];
    }
    return '';
}

function renderIconPicker(selected) {
    selected = normalizeIconClass(selected) || 'fa-box';
    var html = '';
    CATEGORY_ICONS.forEach(function (icon) {
        var active = icon === selected ? ' is-selected' : '';
        html += '<button type="button" class="cat-icon-option' + active + '" data-icon="' + icon + '" title="' + icon + '">' +
            '<i class="fas ' + icon + '"></i><span>' + icon.replace('fa-', '') + '</span></button>';
    });
    // Ensure current icon is visible even if not in default list
    if (CATEGORY_ICONS.indexOf(selected) === -1 && selected) {
        html = '<button type="button" class="cat-icon-option is-selected" data-icon="' + selected + '" title="' + selected + '">' +
            '<i class="fas ' + selected + '"></i><span>' + selected.replace('fa-', '') + '</span></button>' + html;
    }
    $('#category-icon-picker').html(html);
    setSelectedIcon(selected);
}

function setSelectedIcon(icon) {
    icon = normalizeIconClass(icon) || 'fa-box';
    $('#category-icon').val(icon);
    $('#category-icon-picker .cat-icon-option').removeClass('is-selected');
    $('#category-icon-picker .cat-icon-option[data-icon="' + icon + '"]').addClass('is-selected');
    $('#category-icon-preview').html('<i class="fas ' + icon + '"></i><span>Selected: ' + icon + '</span>');
}

function loadCategories() {
    AdminApp.request(ADMIN_BASE + '/api/categories', 'GET', { search: $('#category-search').val() }).done(function (res) {
        parentOptions = res.data.parents || [];
        var html = '';
        (res.data.items || []).forEach(function (row) {
            var iconClass = normalizeIconClass(row.icon || row.description) || 'fa-box';
            var icon = '<i class="fas ' + iconClass + '" style="font-size:18px;color:#ed5565;"></i>';
            var img = row.image ? '<img src="' + BASE_URL + row.image + '" style="height:40px;width:40px;object-fit:cover;border-radius:4px;">' : '-';
            var actions = '';
            if (canUpdate) actions += '<button class="btn btn-xs btn-primary btn-edit" data-id="' + row.id + '"><i class="fa fa-pencil"></i></button> ';
            if (canDelete) actions += '<button class="btn btn-xs btn-danger btn-delete" data-id="' + row.id + '"><i class="fa fa-trash"></i></button>';
            var typeBadge = row.parent_id
                ? '<span class="badge badge-warning">Subcategory</span>'
                : '<span class="badge badge-primary">Category</span>';
            html += '<tr>' +
                '<td>' + row.id + '</td>' +
                '<td>' + icon + '</td>' +
                '<td>' + img + '</td>' +
                '<td>' + (row.display_name || row.name) + '</td>' +
                '<td>' + (row.parent_name || '—') + '</td>' +
                '<td>' + typeBadge + '</td>' +
                '<td>' + row.slug + '</td>' +
                '<td>' + row.sort_order + '</td>' +
                '<td>' + (row.status == 1 ? '<span class="badge badge-primary">Active</span>' : '<span class="badge badge-danger">Inactive</span>') + '</td>' +
                '<td>' + actions + '</td></tr>';
        });
        if (!html) html = '<tr><td colspan="10" class="text-center text-muted">No categories found</td></tr>';
        $('#categories-table tbody').html(html);
        fillParentSelect();
    });
}

function resetCategoryForm() {
    $('#category-form')[0].reset();
    $('#category-id').val('');
    fillParentSelect();
    renderIconPicker('fa-box');
    $('#category-modal-title').text('Add Category / Subcategory');
}

$('#btn-add-category').on('click', function () {
    resetCategoryForm();
    $('#category-modal').modal('show');
});

$('#category-search').on('keyup', function () { loadCategories(); });

$(document).on('click', '.cat-icon-option', function () {
    setSelectedIcon($(this).data('icon'));
});

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
        renderIconPicker(row.icon || row.description || 'fa-box');
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
