<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php $isEdit = ! empty($isEdit); ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
    <div class="col-lg-8"><h2><?= esc($pageTitle) ?></h2></div>
    <div class="col-lg-4 text-right" style="padding-top:20px;">
        <a href="<?= admin_url('banners') ?>" class="btn btn-white"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
</div>

<div class="ibox">
    <div class="ibox-title"><h5><?= $isEdit ? 'Edit Banner' : 'Banner Details' ?></h5></div>
    <div class="ibox-content">
        <form id="main-form" enctype="multipart/form-data">
            <input type="hidden" name="id" id="record-id" value="<?= $isEdit ? (int) $recordId : '' ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Position *</label>
                        <select class="form-control" name="position" id="f-position" required>
                            <?php foreach ($positions as $key => $label): ?>
                                <option value="<?= esc($key) ?>"><?= esc($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Home Slider = main carousel. Category Section Banner = image above a homepage category block.</small>
                        <small class="text-info d-block m-t-xs" id="position-size-hint"></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Slide Style</label>
                        <select class="form-control" name="style" id="f-style">
                            <option value="light">Light (dark text)</option>
                            <option value="dark">Dark (white text)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group" id="category-section-wrap" style="display:none;">
                <label>Homepage Category *</label>
                <select class="form-control" id="f-category-slug">
                    <option value="">— Select category —</option>
                    <?php foreach (($categories ?? []) as $cat): ?>
                        <option value="<?= esc($cat['slug']) ?>" data-name="<?= esc($cat['name']) ?>">
                            <?= esc($cat['name']) ?> (<?= esc($cat['slug']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted">This banner will show <strong>above that category’s product section</strong> on the homepage. Category must be Active and have products.</small>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label id="subtitle-label">Subtitle</label>
                        <input class="form-control" name="subtitle" id="f-subtitle" placeholder="e.g. Financing Offer">
                        <small class="text-muted" id="subtitle-hint" style="display:none;">For category banners this must be the category slug (auto-filled when you pick a category).</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Title *</label>
                        <input class="form-control" name="title" id="f-title" required placeholder="e.g. Camera, Lens and Tablet">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <input class="form-control" name="description" id="f-description" placeholder="Short line under the title">
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Badge / Offer Text</label>
                        <input class="form-control" name="badge_text" id="f-badge" placeholder="e.g. 40% OFF">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Button Text</label>
                        <input class="form-control" name="button_text" id="f-button" placeholder="Shop now / Buy Now">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Background Color</label>
                        <input class="form-control" name="bg_color" id="f-bg" placeholder="#e8e8ea">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Button Link *</label>
                <input class="form-control" name="link" id="f-link" placeholder="/shop or /product/slug or https://...">
                <small class="text-muted">Internal path (e.g. <code>/shop?category=shoe</code>) or full URL.</small>
            </div>
            <div class="form-group">
                <label>Image</label>
                <input type="file" class="form-control" name="image" accept="image/*">
                <small class="text-muted" id="image-size-hint">Recommended size: <strong>580 × 460 px</strong> (JPG or PNG)</small>
                <div id="image-preview" class="m-t-sm"></div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" id="f-sort" value="0">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status" id="f-status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="m-t-md">
                <button type="submit" class="btn btn-primary" id="save-btn">Save</button>
                <a href="<?= admin_url('banners') ?>" class="btn btn-white">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
var isEdit = <?= $isEdit ? 'true' : 'false' ?>;
var recordId = <?= $isEdit ? (int) $recordId : 'null' ?>;
var imageSizes = <?= json_encode($imageSizes ?? []) ?>;

function updateSizeHints() {
    var pos = $('#f-position').val();
    var size = imageSizes[pos] || imageSizes.home_slider;
    if (!size) return;
    var label = size.label || (size.width + ' × ' + size.height + ' px');
    $('#image-size-hint').html('Recommended size: <strong>' + label + '</strong> (JPG or PNG)');
    $('#position-size-hint').html('<i class="fa fa-info-circle"></i> Image size for this position: <strong>' + label + '</strong>');
}

function toggleCategorySectionFields() {
    var isCategory = $('#f-position').val() === 'category_section';
    $('#category-section-wrap').toggle(isCategory);
    $('#subtitle-hint').toggle(isCategory);
    if (isCategory) {
        $('#subtitle-label').text('Category Slug (Subtitle)');
        $('#f-subtitle').attr('placeholder', 'e.g. shoe');
    } else {
        $('#subtitle-label').text('Subtitle');
        $('#f-subtitle').attr('placeholder', 'e.g. Financing Offer');
    }
}

function applyCategorySelection() {
    var $opt = $('#f-category-slug option:selected');
    var slug = $opt.val();
    if (!slug) return;
    $('#f-subtitle').val(slug);
    if (!$('#f-title').val()) {
        $('#f-title').val($opt.data('name') || slug);
    }
    $('#f-link').val('shop?category=' + encodeURIComponent(slug));
    if (!$('#f-button').val()) {
        $('#f-button').val('View All');
    }
}

$('#f-position').on('change', function () {
    updateSizeHints();
    toggleCategorySectionFields();
});
$('#f-category-slug').on('change', applyCategorySelection);
updateSizeHints();
toggleCategorySectionFields();

if (isEdit && recordId) {
    AdminApp.request(ADMIN_BASE + '/api/banners/' + recordId, 'GET').done(function (res) {
        var r = res.data;
        $('#record-id').val(r.id);
        $('#f-position').val(r.position || 'home_slider');
        $('#f-style').val(r.style || 'light');
        $('#f-subtitle').val(r.subtitle || '');
        $('#f-title').val(r.title);
        $('#f-description').val(r.description || '');
        $('#f-badge').val(r.badge_text || '');
        $('#f-button').val(r.button_text || '');
        $('#f-bg').val(r.bg_color || '');
        $('#f-link').val(r.link || '');
        $('#f-sort').val(r.sort_order);
        $('#f-status').val(r.status);
        $('#image-preview').html(r.image
            ? '<img src="' + BASE_URL + r.image + '" style="max-height:80px;border-radius:4px;">'
            : '');
        updateSizeHints();
        toggleCategorySectionFields();
        if (r.position === 'category_section' && r.subtitle) {
            $('#f-category-slug').val(String(r.subtitle).toLowerCase());
        }
    });
}

$('#main-form').on('submit', function (e) {
    e.preventDefault();
    if ($('#f-position').val() === 'category_section') {
        var slug = $.trim($('#f-subtitle').val() || '');
        if (!slug) {
            AdminApp.toast('error', 'Select a homepage category (or enter its slug in Subtitle).');
            return;
        }
        $('#f-subtitle').val(slug.toLowerCase());
    }
    var id = $('#record-id').val();
    var url = id ? ADMIN_BASE + '/api/banners/' + id : ADMIN_BASE + '/api/banners';
    var $btn = $('#save-btn');
    AdminApp.setButtonLoading($btn, true);
    AdminApp.request(url, 'POST', new FormData(this)).done(function (res) {
        AdminApp.toast('success', res.message);
        window.location.href = ADMIN_BASE + '/banners';
    }).always(function () {
        AdminApp.setButtonLoading($btn, false);
    });
});
</script>
<?= $this->endSection() ?>
