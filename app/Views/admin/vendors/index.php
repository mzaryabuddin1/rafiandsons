<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
    <div class="col-lg-8"><h2>Vendors</h2><ol class="breadcrumb"><li>Review applications and manage vendor accounts</li></ol></div>
</div>

<div class="ibox">
    <div class="ibox-title"><h5>Vendor Applications</h5><div class="ibox-tools">
        <button type="button" class="btn btn-sm btn-white" id="btn-export-csv"><i class="fa fa-download"></i> Export CSV</button>
    </div></div>
    <div class="ibox-content">
        <div class="row m-b-sm">
            <div class="col-md-4"><input type="text" id="search" class="form-control" placeholder="Business / name / email / phone"></div>
            <div class="col-md-3">
                <select id="filter-status" class="form-control">
                    <option value="">All Statuses</option>
                    <?php foreach ($statuses as $k => $v): ?>
                        <option value="<?= esc($k) ?>"><?= esc($v) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-primary" id="btn-filter">Filter</button></div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="data-table">
                <thead>
                <tr>
                    <th>Business</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Applied</th>
                    <th width="180">Actions</th>
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
var canDelete = <?= ! empty($canDelete) ? 'true' : 'false' ?>;

var table = AdminApp.createDataTable({
    url: ADMIN_BASE + '/api/vendors',
    $tbody: $('#data-table tbody'),
    $pager: $('#table-pager'),
    $info: $('#table-info'),
    $search: $('#search'),
    emptyCols: 7,
    emptyText: 'No vendors found',
    filters: function () {
        return { status: $('#filter-status').val() };
    },
    renderRow: function (r) {
        var a = '<a class="btn btn-xs btn-primary" href="' + ADMIN_BASE + '/vendors/' + r.id + '"><i class="fa fa-eye"></i></a> ';
        if (canDelete) a += '<button class="btn btn-xs btn-danger btn-delete" data-id="' + r.id + '"><i class="fa fa-trash"></i></button>';
        return '<tr>'
            + '<td>' + (r.business_name || '') + '</td>'
            + '<td>' + (r.contact_name || '') + '</td>'
            + '<td>' + (r.email || '') + '</td>'
            + '<td>' + (r.phone || '') + '</td>'
            + '<td><span class="badge badge-primary">' + (r.status_label || r.status) + '</span></td>'
            + '<td>' + (r.created_at || '') + '</td>'
            + '<td>' + a + '</td>'
            + '</tr>';
    }
});
$('#btn-export-csv').on('click', function () { table.exportCsv(); });

$('#btn-filter').on('click', function () { table.load(true); });

$(document).on('click', '.btn-delete', function () {
    var id = $(this).data('id');
    AdminApp.confirmDelete(function () {
        AdminApp.request(ADMIN_BASE + '/api/vendors/' + id + '/delete', 'POST').done(function (res) {
            AdminApp.toast('success', res.message);
            table.load(true);
        });
    }, 'Archive this vendor?');
});

table.load(true);
</script>
<?= $this->endSection() ?>
