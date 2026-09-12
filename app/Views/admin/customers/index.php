<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
<div class="col-lg-8"><h2>Customers</h2></div>
<div class="col-lg-4 text-right"><?php if (!empty($canCreate)): ?><a href="<?= site_url('admin/customers/create') ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Add Customer</a><?php endif; ?></div>
</div>
<div class="ibox"><div class="ibox-title"><h5>Customers</h5><div class="ibox-tools">
<select id="filter-registered" class="form-control form-control-sm" style="width:180px;display:inline-block;margin-right:8px;">
<option value="">All customers</option>
<option value="1">Registered accounts</option>
<option value="0">Guest checkout only</option>
</select>
<input type="text" id="search" class="form-control form-control-sm" placeholder="Search name/phone/email" style="width:240px;display:inline-block;margin-right:8px;">
<button type="button" class="btn btn-sm btn-white" id="btn-export-csv"><i class="fa fa-download"></i> Export CSV</button>
</div></div>
<div class="ibox-content"><div class="table-responsive"><table class="table table-striped table-bordered" id="data-table"><thead><tr><th>ID</th><th>Name</th><th>Phone</th><th>Email</th><th>City</th><th>Account</th><th>Status</th><th width="160">Actions</th></tr></thead><tbody></tbody></table></div>
<div class="row admin-table-footer">
  <div class="col-sm-6"><div class="admin-table-info text-muted" id="table-info"></div></div>
  <div class="col-sm-6 text-right"><div class="admin-table-pager" id="table-pager"></div></div>
</div>
</div></div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
var canUpdate=<?= !empty($canUpdate)?'true':'false' ?>, canDelete=<?= !empty($canDelete)?'true':'false' ?>;
var table = AdminApp.createDataTable({
    url: ADMIN_BASE + '/api/customers',
    $tbody: $('#data-table tbody'),
    $pager: $('#table-pager'),
    $info: $('#table-info'),
    $search: $('#search'),
    emptyCols: 8,
    emptyText: 'No customers found',
    filters: function () {
        return { registered: $('#filter-registered').val() };
    },
    renderRow: function (r) {
        var a = '';
        if (canUpdate) a += '<a class="btn btn-xs btn-primary" href="' + ADMIN_BASE + '/customers/' + r.id + '/edit"><i class="fa fa-pencil"></i></a> ';
        if (canDelete) a += '<button class="btn btn-xs btn-danger btn-delete" data-id="' + r.id + '"><i class="fa fa-trash"></i></button>';
        var acct = r.is_registered ? '<span class="badge badge-success">Registered</span>' : '<span class="badge badge-default">Guest</span>';
        return '<tr><td>' + r.id + '</td><td>' + r.name + '</td><td>' + r.phone + '</td><td>' + (r.email || '') + '</td><td>' + (r.city || '') + '</td><td>' + acct + '</td><td>' + (r.status == 1 ? 'Active' : 'Inactive') + '</td><td>' + a + '</td></tr>';
    }
});
$('#btn-export-csv').on('click', function () { table.exportCsv(); });
$('#filter-registered').on('change', function () { table.load(true); });
$(document).on('click','.btn-delete',function(){var id=$(this).data('id');AdminApp.confirmDelete(function(){AdminApp.request(ADMIN_BASE+'/api/customers/'+id+'/delete','POST').done(function(res){AdminApp.toast('success',res.message);table.load(true);});});});
table.load(true);
</script>
<?= $this->endSection() ?>
