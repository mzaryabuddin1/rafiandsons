<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
    <div class="col-lg-8"><h2>Homepage Banners</h2><ol class="breadcrumb"><li>Manage slider, side banner &amp; mid banners</li></ol></div>
    <div class="col-lg-4 text-right" style="padding-top:20px;">
        <?php if (! empty($canCreate)): ?>
            <button class="btn btn-primary" id="btn-add"><i class="fa fa-plus"></i> Add Banner</button>
        <?php endif; ?>
    </div>
</div>

<div class="ibox">
    <div class="ibox-title">
        <h5>Banners</h5>
        <div class="ibox-tools">
            <select id="filter-position" class="form-control form-control-sm" style="width:180px;display:inline-block;">
                <option value="">All positions</option>
                <?php foreach ($positions as $key => $label): ?>
                    <option value="<?= esc($key) ?>"><?= esc($label) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" id="search" class="form-control form-control-sm" placeholder="Search..." style="width:200px;display:inline-block;">
        </div>
    </div>
    <div class="ibox-content">
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="data-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Position</th>
                    <th>Title</th>
                    <th>Button</th>
                    <th>Link</th>
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

<div class="modal inmodal" id="form-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated fadeIn">
            <form id="main-form" enctype="multipart/form-data">
                <div class="modal-header navy-bg">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title" id="modal-title">Add Banner</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="record-id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Position *</label>
                                <select class="form-control" name="position" id="f-position" required>
                                    <?php foreach ($positions as $key => $label): ?>
                                        <option value="<?= esc($key) ?>"><?= esc($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Home Slider = main carousel. Home Side = right promo panel.</small>
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Subtitle</label>
                                <input class="form-control" name="subtitle" id="f-subtitle" placeholder="e.g. Financing Offer">
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
                        <small class="text-muted">Internal path (e.g. <code>/shop?category=electronics</code>) or full URL.</small>
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="save-btn">Save</button>
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
var imageSizes = <?= json_encode($imageSizes ?? []) ?>;

function updateSizeHints() {
    var pos = $('#f-position').val();
    var size = imageSizes[pos] || imageSizes.home_slider;
    if (!size) return;
    var label = size.label || (size.width + ' × ' + size.height + ' px');
    $('#image-size-hint').html('Recommended size: <strong>' + label + '</strong> (JPG or PNG)');
    $('#position-size-hint').html('<i class="fa fa-info-circle"></i> Image size for this position: <strong>' + label + '</strong>');
}

function loadList() {
    AdminApp.request(ADMIN_BASE + '/api/banners', 'GET', {
        search: $('#search').val(),
        position: $('#filter-position').val()
    }).done(function (res) {
        var h = '';
        (res.data.items || []).forEach(function (r) {
            var img = r.image
                ? '<img src="' + BASE_URL + r.image + '" style="height:40px;border-radius:4px;">'
                : '-';
            var a = '';
            if (canUpdate) a += '<button class="btn btn-xs btn-primary btn-edit" data-id="' + r.id + '"><i class="fa fa-pencil"></i></button> ';
            if (canDelete) a += '<button class="btn btn-xs btn-danger btn-delete" data-id="' + r.id + '"><i class="fa fa-trash"></i></button>';
            h += '<tr>'
                + '<td>' + r.id + '</td>'
                + '<td>' + img + '</td>'
                + '<td><span class="label label-primary">' + (r.position_label || r.position) + '</span></td>'
                + '<td>' + (r.subtitle ? '<small class="text-muted">' + r.subtitle + '</small><br>' : '') + r.title + '</td>'
                + '<td>' + (r.button_text || '-') + '</td>'
                + '<td style="max-width:180px;word-break:break-all;"><small>' + (r.link || '-') + '</small></td>'
                + '<td>' + r.sort_order + '</td>'
                + '<td>' + (r.status == 1 ? 'Active' : 'Inactive') + '</td>'
                + '<td>' + a + '</td>'
                + '</tr>';
        });
        if (!h) h = '<tr><td colspan="9" class="text-center text-muted">No banners found</td></tr>';
        $('#data-table tbody').html(h);
    });
}

function resetForm() {
    $('#main-form')[0].reset();
    $('#record-id').val('');
    $('#image-preview').empty();
    $('#f-position').val('home_slider');
    $('#f-style').val('light');
    $('#f-status').val('1');
    $('#f-sort').val('0');
    updateSizeHints();
}

$('#btn-add').on('click', function () {
    resetForm();
    $('#modal-title').text('Add Banner');
    $('#form-modal').modal('show');
});

$('#f-position').on('change', updateSizeHints);

$('#search, #filter-position').on('change keyup', loadList);

$('#main-form').on('submit', function (e) {
    e.preventDefault();
    var id = $('#record-id').val();
    var url = id ? ADMIN_BASE + '/api/banners/' + id : ADMIN_BASE + '/api/banners';
    var $btn = $('#save-btn');
    AdminApp.setButtonLoading($btn, true);
    AdminApp.request(url, 'POST', new FormData(this)).done(function (res) {
        AdminApp.toast('success', res.message);
        $('#form-modal').modal('hide');
        loadList();
    }).always(function () {
        AdminApp.setButtonLoading($btn, false);
    });
});

$(document).on('click', '.btn-edit', function () {
    AdminApp.request(ADMIN_BASE + '/api/banners/' + $(this).data('id'), 'GET').done(function (res) {
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
        $('#modal-title').text('Edit Banner');
        $('#form-modal').modal('show');
        updateSizeHints();
    });
});

$(document).on('click', '.btn-delete', function () {
    var id = $(this).data('id');
    AdminApp.confirmDelete(function () {
        AdminApp.request(ADMIN_BASE + '/api/banners/' + id + '/delete', 'POST').done(function (res) {
            AdminApp.toast('success', res.message);
            loadList();
        });
    });
});

$(loadList);
</script>
<?= $this->endSection() ?>
