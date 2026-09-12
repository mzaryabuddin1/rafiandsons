<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
    <div class="col-lg-8"><h2>Homepage Banners</h2><ol class="breadcrumb"><li>Manage slider, side banner &amp; mid banners</li></ol></div>
    <div class="col-lg-4 text-right" style="padding-top:20px;">
        <?php if (! empty($canCreate)): ?>
            <a href="<?= admin_url('banners/create') ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Add Banner</a>
        <?php endif; ?>
    </div>
</div>

<div class="ibox">
    <div class="ibox-title">
        <h5>Banners</h5>
        <div class="ibox-tools">
            <div class="btn-group m-r-sm">
              <button type="button" class="btn btn-sm btn-primary" id="btn-view-active">Active</button>
              <button type="button" class="btn btn-sm btn-white" id="btn-view-archived">Archived</button>
            </div>
            <select id="filter-position" class="form-control form-control-sm" style="width:180px;display:inline-block;margin-right:8px;">
                <option value="">All positions</option>
                <?php foreach ($positions as $key => $label): ?>
                    <option value="<?= esc($key) ?>"><?= esc($label) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" id="search" class="form-control form-control-sm" placeholder="Search..." style="width:200px;display:inline-block;margin-right:8px;">
            <button type="button" class="btn btn-sm btn-white" id="btn-export-csv"><i class="fa fa-download"></i> Export CSV</button>
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

function setArchivedView(archived) {
    showArchived = !!archived;
    $('#btn-view-active').toggleClass('btn-primary', !showArchived).toggleClass('btn-white', showArchived);
    $('#btn-view-archived').toggleClass('btn-primary', showArchived).toggleClass('btn-white', !showArchived);
    table.load(true);
}

var table = AdminApp.createDataTable({
    url: ADMIN_BASE + '/api/banners',
    $tbody: $('#data-table tbody'),
    $pager: $('#table-pager'),
    $info: $('#table-info'),
    $search: $('#search'),
    emptyCols: 9,
    emptyText: function () { return showArchived ? 'No archived banners' : 'No banners found'; },
    filters: function () {
        return { position: $('#filter-position').val(), archived: showArchived ? 1 : 0 };
    },
    renderRow: function (r) {
        var img = r.image
            ? '<img src="' + BASE_URL + r.image + '" style="height:40px;border-radius:4px;">'
            : '-';
        var a = '';
        if (!showArchived && canUpdate) a += '<a class="btn btn-xs btn-primary" href="' + ADMIN_BASE + '/banners/' + r.id + '/edit"><i class="fa fa-pencil"></i></a> ';
        if (canDelete) {
            if (showArchived) a += '<button class="btn btn-xs btn-success btn-restore" data-id="' + r.id + '" title="Restore"><i class="fa fa-undo"></i></button>';
            else a += '<button class="btn btn-xs btn-warning btn-archive" data-id="' + r.id + '" title="Archive"><i class="fa fa-archive"></i></button>';
        }
        return '<tr>'
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
    }
});
$('#btn-export-csv').on('click', function () { table.exportCsv(); });
$('#filter-position').on('change', function () { table.load(true); });
$('#btn-view-active').on('click', function () { setArchivedView(false); });
$('#btn-view-archived').on('click', function () { setArchivedView(true); });

$(document).on('click', '.btn-archive', function () {
    var id = $(this).data('id');
    AdminApp.confirmArchive(function () {
        AdminApp.request(ADMIN_BASE + '/api/banners/' + id + '/delete', 'POST').done(function (res) {
            AdminApp.toast('success', res.message);
            table.load(true);
        });
    });
});

$(document).on('click', '.btn-restore', function () {
    var id = $(this).data('id');
    AdminApp.confirmRestore(function () {
        AdminApp.request(ADMIN_BASE + '/api/banners/' + id + '/restore', 'POST').done(function (res) {
            AdminApp.toast('success', res.message);
            table.load(true);
        });
    });
});

table.load(true);
</script>
<?= $this->endSection() ?>
