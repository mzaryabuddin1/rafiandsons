<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
<div class="col-lg-8"><h2>Users</h2></div>
<div class="col-lg-4 text-right"><?php if (!empty($canCreate)): ?><a href="<?= admin_url('users/create') ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Add User</a><?php endif; ?></div>
</div>
<div class="ibox"><div class="ibox-title"><h5>Admin Users</h5><div class="ibox-tools">
<div class="btn-group m-r-sm">
  <button type="button" class="btn btn-sm btn-primary" id="btn-view-active">Active</button>
  <button type="button" class="btn btn-sm btn-white" id="btn-view-archived">Archived</button>
</div>
<input type="text" id="search" class="form-control form-control-sm" placeholder="Search..." style="width:220px;display:inline-block;margin-right:8px;">
<button type="button" class="btn btn-sm btn-white" id="btn-export-csv"><i class="fa fa-download"></i> Export CSV</button>
</div></div>
<div class="ibox-content"><div class="table-responsive"><table class="table table-striped table-bordered" id="data-table"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last Login</th><th width="140">Actions</th></tr></thead><tbody></tbody></table></div>
<div class="row admin-table-footer">
  <div class="col-sm-6"><div class="admin-table-info text-muted" id="table-info"></div></div>
  <div class="col-sm-6 text-right"><div class="admin-table-pager" id="table-pager"></div></div>
</div>
</div></div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
var canUpdate=<?= !empty($canUpdate)?'true':'false' ?>, canDelete=<?= !empty($canDelete)?'true':'false' ?>;
var showArchived = false;

function setArchivedView(archived) {
    showArchived = !!archived;
    $('#btn-view-active').toggleClass('btn-primary', !showArchived).toggleClass('btn-white', showArchived);
    $('#btn-view-archived').toggleClass('btn-primary', showArchived).toggleClass('btn-white', !showArchived);
    table.load(true);
}

var table = AdminApp.createDataTable({
    url: ADMIN_BASE + '/api/users',
    $tbody: $('#data-table tbody'),
    $pager: $('#table-pager'),
    $info: $('#table-info'),
    $search: $('#search'),
    emptyCols: 7,
    emptyText: function () { return showArchived ? 'No archived users' : 'No users found'; },
    filters: function () { return { archived: showArchived ? 1 : 0 }; },
    renderRow: function (r) {
        var a = '';
        if (!showArchived && canUpdate) a += '<a class="btn btn-xs btn-primary" href="' + ADMIN_BASE + '/users/' + r.id + '/edit"><i class="fa fa-pencil"></i></a> ';
        if (canDelete) {
            if (showArchived) a += '<button class="btn btn-xs btn-success btn-restore" data-id="' + r.id + '" title="Restore"><i class="fa fa-undo"></i></button>';
            else a += '<button class="btn btn-xs btn-warning btn-archive" data-id="' + r.id + '" title="Archive"><i class="fa fa-archive"></i></button>';
        }
        return '<tr><td>' + r.id + '</td><td>' + r.name + '</td><td>' + r.email + '</td><td>' + (r.role_name || '') + '</td><td>' + (r.status == 1 ? 'Active' : 'Inactive') + '</td><td>' + (r.last_login_at || '-') + '</td><td>' + a + '</td></tr>';
    }
});
$('#btn-export-csv').on('click', function () { table.exportCsv(); });
$('#btn-view-active').on('click', function () { setArchivedView(false); });
$('#btn-view-archived').on('click', function () { setArchivedView(true); });
$(document).on('click','.btn-archive',function(){var id=$(this).data('id');AdminApp.confirmArchive(function(){AdminApp.request(ADMIN_BASE+'/api/users/'+id+'/delete','POST').done(function(res){AdminApp.toast('success',res.message);table.load(true);});});});
$(document).on('click','.btn-restore',function(){var id=$(this).data('id');AdminApp.confirmRestore(function(){AdminApp.request(ADMIN_BASE+'/api/users/'+id+'/restore','POST').done(function(res){AdminApp.toast('success',res.message);table.load(true);});});});
table.load(true);
</script>
<?= $this->endSection() ?>
