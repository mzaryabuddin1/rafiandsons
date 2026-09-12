<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
    <div class="col-lg-8">
        <h2>Bank Accounts</h2>
        <ol class="breadcrumb"><li>Accounts shown on checkout for customer payments</li></ol>
    </div>
    <div class="col-lg-4 text-right" style="padding-top:20px;">
        <?php if (! empty($canCreate)): ?>
            <a href="<?= site_url('admin/bank-accounts/create') ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Add Account</a>
        <?php endif; ?>
    </div>
</div>

<div class="ibox">
    <div class="ibox-title"><h5>Payment Accounts</h5><div class="ibox-tools">
        <input type="text" id="search" class="form-control form-control-sm" placeholder="Search..." style="width:220px;display:inline-block;margin-right:8px;">
        <button type="button" class="btn btn-sm btn-white" id="btn-export-csv"><i class="fa fa-download"></i> Export CSV</button>
    </div></div>
    <div class="ibox-content">
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="data-table">
                <thead>
                <tr>
                    <th width="70">Logo</th>
                    <th>Bank</th>
                    <th>Account Title</th>
                    <th>Account Number</th>
                    <th>IBAN</th>
                    <th>Branch</th>
                    <th>Sort</th>
                    <th>Status</th>
                    <th width="120">Actions</th>
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

var table = AdminApp.createDataTable({
    url: ADMIN_BASE + '/api/bank-accounts',
    $tbody: $('#data-table tbody'),
    $pager: $('#table-pager'),
    $info: $('#table-info'),
    $search: $('#search'),
    emptyCols: 9,
    emptyText: 'No bank accounts yet',
    filters: function () { return {}; },
    renderRow: function (r) {
        var a = '';
        if (canUpdate) a += '<a class="btn btn-xs btn-primary" href="' + ADMIN_BASE + '/bank-accounts/' + r.id + '/edit"><i class="fa fa-pencil"></i></a> ';
        if (canDelete) a += '<button class="btn btn-xs btn-danger btn-delete" data-id="' + r.id + '"><i class="fa fa-trash"></i></button>';
        var logo = r.logo_url
            ? '<img src="' + r.logo_url + '" alt="" style="height:32px;max-width:70px;object-fit:contain;">'
            : '<span class="text-muted">-</span>';
        return '<tr>'
            + '<td>' + logo + '</td>'
            + '<td>' + (r.bank_name || '') + '</td>'
            + '<td>' + (r.account_title || '') + '</td>'
            + '<td>' + (r.account_number || '') + '</td>'
            + '<td>' + (r.iban || '-') + '</td>'
            + '<td>' + (r.branch || '-') + '</td>'
            + '<td>' + r.sort_order + '</td>'
            + '<td>' + (parseInt(r.status, 10) === 1 ? '<span class="badge badge-primary">Active</span>' : '<span class="badge">Inactive</span>') + '</td>'
            + '<td>' + a + '</td>'
            + '</tr>';
    }
});
$('#btn-export-csv').on('click', function () { table.exportCsv(); });

$(document).on('click', '.btn-delete', function () {
    var id = $(this).data('id');
    AdminApp.confirmDelete(function () {
        AdminApp.request(ADMIN_BASE + '/api/bank-accounts/' + id + '/delete', 'POST').done(function (res) {
            AdminApp.toast('success', res.message);
            table.load(true);
        });
    }, 'Delete this bank account?');
});

table.load(true);
</script>
<?= $this->endSection() ?>
