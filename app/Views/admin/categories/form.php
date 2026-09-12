<?= $this->extend('admin/layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/riode-vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet">
<style>
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
<?php $isEdit = ! empty($isEdit); $recordId = ! empty($recordId) ? (int) $recordId : null; ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px; padding:15px;">
    <div class="col-lg-8">
        <h2><?= $isEdit ? 'Edit Category' : 'Add Category' ?></h2>
    </div>
    <div class="col-lg-4 text-right">
        <a href="<?= admin_url('categories') ?>" class="btn btn-white"><i class="fa fa-arrow-left"></i> Back to Categories</a>
    </div>
</div>

<div class="ibox">
    <div class="ibox-title"><h5><?= $isEdit ? 'Edit Category' : 'Add Category / Subcategory' ?></h5></div>
    <div class="ibox-content">
        <form id="category-form" enctype="multipart/form-data">
            <input type="hidden" name="id" id="category-id" value="<?= $recordId ? (int) $recordId : '' ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Parent Category</label>
                        <select class="form-control" name="parent_id" id="category-parent">
                            <option value="">— Top-level category —</option>
                            <?php foreach (($parents ?? []) as $p): ?>
                            <option value="<?= (int) $p['id'] ?>"><?= esc($p['name']) ?></option>
                            <?php endforeach; ?>
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

            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <a href="<?= admin_url('categories') ?>" class="btn btn-white">Cancel</a>
                <button type="submit" class="btn btn-primary" id="category-save-btn">Save</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
var recordId = <?= $recordId ? (int) $recordId : 'null' ?>;
var parentOptions = <?= json_encode(array_map(static function ($p) {
    return ['id' => (int) $p['id'], 'name' => $p['name']];
}, $parents ?? []), JSON_UNESCAPED_UNICODE) ?>;
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
        window.location = ADMIN_BASE + '/categories';
    }).always(function () { AdminApp.setButtonLoading($btn, false); });
});

if (recordId) {
    AdminApp.request(ADMIN_BASE + '/api/categories/' + recordId, 'GET').done(function (res) {
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
    });
} else {
    fillParentSelect();
    renderIconPicker('fa-box');
}
</script>
<?= $this->endSection() ?>
