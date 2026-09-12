<?= $this->extend('admin/layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/riode-vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px; padding:15px;">
    <div class="col-lg-8"><h2>Categories &amp; Subcategories</h2></div>
    <div class="col-lg-4 text-right">
        <?php if (! empty($canCreate)): ?>
        <a href="<?= admin_url('categories/create') ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Add Category</a>
        <?php endif; ?>
    </div>
</div>

<div class="ibox">
    <div class="ibox-title">
        <h5>Category List</h5>
        <div class="ibox-tools">
            <div class="btn-group m-r-sm">
              <button type="button" class="btn btn-sm btn-primary" id="btn-view-active">Active</button>
              <button type="button" class="btn btn-sm btn-white" id="btn-view-archived">Archived</button>
            </div>
            <input type="text" id="category-search" class="form-control form-control-sm" placeholder="Search..." style="width:220px;display:inline-block;margin-right:8px;">
            <button type="button" class="btn btn-sm btn-white" id="btn-export-csv"><i class="fa fa-download"></i> Export CSV</button>
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
        <div class="row admin-table-footer">
          <div class="col-sm-6"><div class="admin-table-info text-muted" id="table-info"></div></div>
          <div class="col-sm-6 text-right"><div class="admin-table-pager" id="table-pager"></div></div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
var canUpdate = <?= ! empty($canUpdate) ? 'true' : 'false' ?>;
var canDelete = <?= ! empty($canDelete) ? 'true' : 'false' ?>;
var showArchived = false;

function normalizeIconClass(raw) {
    raw = String(raw || '').trim();
    if (!raw) return '';
    var parts = raw.split(/\s+/);
    for (var i = 0; i < parts.length; i++) {
        if (parts[i].indexOf('fa-') === 0) return parts[i];
    }
    return '';
}

function setArchivedView(archived) {
    showArchived = !!archived;
    $('#btn-view-active').toggleClass('btn-primary', !showArchived).toggleClass('btn-white', showArchived);
    $('#btn-view-archived').toggleClass('btn-primary', showArchived).toggleClass('btn-white', !showArchived);
    table.load(true);
}

var table = AdminApp.createDataTable({
    url: ADMIN_BASE + '/api/categories',
    $tbody: $('#categories-table tbody'),
    $pager: $('#table-pager'),
    $info: $('#table-info'),
    $search: $('#category-search'),
    emptyCols: 10,
    emptyText: function () { return showArchived ? 'No archived categories' : 'No categories found'; },
    filters: function () { return { archived: showArchived ? 1 : 0 }; },
    renderRow: function (row) {
        var iconClass = normalizeIconClass(row.icon || row.description) || 'fa-box';
        var icon = '<i class="fas ' + iconClass + '" style="font-size:18px;color:#ed5565;"></i>';
        var img = row.image ? '<img src="' + BASE_URL + row.image + '" style="height:40px;width:40px;object-fit:cover;border-radius:4px;">' : '-';
        var actions = '';
        if (!showArchived && canUpdate) actions += '<a href="' + ADMIN_BASE + '/categories/' + row.id + '/edit" class="btn btn-xs btn-primary" title="Edit"><i class="fas fa-pencil-alt"></i></a> ';
        if (canDelete) {
            if (showArchived) actions += '<button class="btn btn-xs btn-success btn-restore" data-id="' + row.id + '" title="Restore"><i class="fas fa-undo"></i></button>';
            else actions += '<button class="btn btn-xs btn-warning btn-archive" data-id="' + row.id + '" title="Archive"><i class="fas fa-archive"></i></button>';
        }
        var typeBadge = row.parent_id
            ? '<span class="badge badge-warning">Subcategory</span>'
            : '<span class="badge badge-primary">Category</span>';
        return '<tr>' +
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
    }
});
$('#btn-export-csv').on('click', function () { table.exportCsv(); });
$('#btn-view-active').on('click', function () { setArchivedView(false); });
$('#btn-view-archived').on('click', function () { setArchivedView(true); });

$(document).on('click', '.btn-archive', function () {
    var id = $(this).data('id');
    AdminApp.confirmArchive(function () {
        AdminApp.request(ADMIN_BASE + '/api/categories/' + id + '/delete', 'POST').done(function (res) {
            AdminApp.toast('success', res.message);
            table.load(true);
        });
    }, 'Archive this category? Subcategories under a parent will also be archived.');
});

$(document).on('click', '.btn-restore', function () {
    var id = $(this).data('id');
    AdminApp.confirmRestore(function () {
        AdminApp.request(ADMIN_BASE + '/api/categories/' + id + '/restore', 'POST').done(function (res) {
            AdminApp.toast('success', res.message);
            table.load(true);
        });
    });
});

table.load(true);
</script>
<?= $this->endSection() ?>
