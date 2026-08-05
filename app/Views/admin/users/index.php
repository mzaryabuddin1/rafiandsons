<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
<div class="col-lg-8"><h2>Users</h2></div>
<div class="col-lg-4 text-right"><?php if (!empty($canCreate)): ?><button class="btn btn-primary" id="btn-add"><i class="fa fa-plus"></i> Add User</button><?php endif; ?></div>
</div>
<div class="ibox"><div class="ibox-title"><h5>Admin Users</h5><div class="ibox-tools"><input type="text" id="search" class="form-control form-control-sm" placeholder="Search..." style="width:220px;display:inline-block;"></div></div>
<div class="ibox-content"><div class="table-responsive"><table class="table table-striped table-bordered" id="data-table"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last Login</th><th width="140">Actions</th></tr></thead><tbody></tbody></table></div></div></div>
<div class="modal inmodal" id="form-modal" tabindex="-1"><div class="modal-dialog"><div class="modal-content animated fadeIn">
<form id="main-form"><div class="modal-header navy-bg"><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button><h4 class="modal-title" id="modal-title">Add User</h4></div>
<div class="modal-body">
<input type="hidden" name="id" id="record-id">
<div class="form-group"><label>Name *</label><input class="form-control" name="name" id="f-name" required></div>
<div class="form-group"><label>Email *</label><input type="email" class="form-control" name="email" id="f-email" required></div>
<div class="form-group"><label>Role *</label><select class="form-control" name="role_id" id="f-role" required><option value="">Select</option><?php foreach ($roles as $r): ?><option value="<?= $r['id'] ?>"><?= esc($r['name']) ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label>Password <small id="pwd-hint">(required for new)</small></label><input type="password" class="form-control" name="password" id="f-password"></div>
<div class="form-group"><label>Status</label><select class="form-control" name="status" id="f-status"><option value="1">Active</option><option value="0">Inactive</option></select></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-white" data-dismiss="modal">Close</button><button type="submit" class="btn btn-primary" id="save-btn">Save</button></div>
</form></div></div></div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
var canUpdate=<?= !empty($canUpdate)?'true':'false' ?>, canDelete=<?= !empty($canDelete)?'true':'false' ?>;
function loadList(){AdminApp.request(ADMIN_BASE+'/api/users','GET',{search:$('#search').val()}).done(function(res){var h='';(res.data.items||[]).forEach(function(r){var a='';if(canUpdate)a+='<button class="btn btn-xs btn-primary btn-edit" data-id="'+r.id+'"><i class="fa fa-pencil"></i></button> ';if(canDelete)a+='<button class="btn btn-xs btn-danger btn-delete" data-id="'+r.id+'"><i class="fa fa-trash"></i></button>';h+='<tr><td>'+r.id+'</td><td>'+r.name+'</td><td>'+r.email+'</td><td>'+(r.role_name||'')+'</td><td>'+(r.status==1?'Active':'Inactive')+'</td><td>'+(r.last_login_at||'-')+'</td><td>'+a+'</td></tr>';});if(!h)h='<tr><td colspan="7" class="text-center text-muted">No users found</td></tr>';$('#data-table tbody').html(h);});}
$('#btn-add').on('click',function(){$('#main-form')[0].reset();$('#record-id').val('');$('#pwd-hint').text('(required for new)');$('#modal-title').text('Add User');$('#form-modal').modal('show');});
$('#search').on('keyup',loadList);
$('#main-form').on('submit',function(e){e.preventDefault();var id=$('#record-id').val(),url=id?ADMIN_BASE+'/api/users/'+id:ADMIN_BASE+'/api/users',$btn=$('#save-btn');AdminApp.setButtonLoading($btn,true);AdminApp.request(url,'POST',$(this).serialize()).done(function(res){AdminApp.toast('success',res.message);$('#form-modal').modal('hide');loadList();}).always(function(){AdminApp.setButtonLoading($btn,false);});});
$(document).on('click','.btn-edit',function(){AdminApp.request(ADMIN_BASE+'/api/users/'+$(this).data('id'),'GET').done(function(res){var r=res.data;$('#record-id').val(r.id);$('#f-name').val(r.name);$('#f-email').val(r.email);$('#f-role').val(r.role_id);$('#f-status').val(r.status);$('#f-password').val('');$('#pwd-hint').text('(leave blank to keep)');$('#modal-title').text('Edit User');$('#form-modal').modal('show');});});
$(document).on('click','.btn-delete',function(){var id=$(this).data('id');AdminApp.confirmDelete(function(){AdminApp.request(ADMIN_BASE+'/api/users/'+id+'/delete','POST').done(function(res){AdminApp.toast('success',res.message);loadList();});});});
$(loadList);
</script>
<?= $this->endSection() ?>
