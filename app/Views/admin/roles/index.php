<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
<div class="col-lg-8"><h2>Roles & Permissions</h2></div>
<div class="col-lg-4 text-right"><?php if (!empty($canCreate)): ?><button class="btn btn-primary" id="btn-add"><i class="fa fa-plus"></i> Add Role</button><?php endif; ?></div>
</div>
<div class="ibox"><div class="ibox-title"><h5>Roles</h5></div>
<div class="ibox-content"><div class="table-responsive"><table class="table table-striped table-bordered" id="data-table"><thead><tr><th>ID</th><th>Name</th><th>Slug</th><th>Super</th><th>Status</th><th width="140">Actions</th></tr></thead><tbody></tbody></table></div></div></div>
<div class="modal inmodal" id="form-modal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content animated fadeIn">
<form id="main-form"><div class="modal-header navy-bg"><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button><h4 class="modal-title" id="modal-title">Add Role</h4></div>
<div class="modal-body">
<input type="hidden" name="id" id="record-id">
<div class="row"><div class="col-md-8"><div class="form-group"><label>Name *</label><input class="form-control" name="name" id="f-name" required></div></div>
<div class="col-md-4"><div class="form-group"><label>Status</label><select class="form-control" name="status" id="f-status"><option value="1">Active</option><option value="0">Inactive</option></select></div></div></div>
<div class="form-group"><label>Permissions</label><div id="permissions-box" style="max-height:320px;overflow:auto;border:1px solid #e7eaec;padding:12px;"></div></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-white" data-dismiss="modal">Close</button><button type="submit" class="btn btn-primary" id="save-btn">Save</button></div>
</form></div></div></div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
var canUpdate=<?= !empty($canUpdate)?'true':'false' ?>, canDelete=<?= !empty($canDelete)?'true':'false' ?>, groupedPermissions={};
function renderPermissions(selected){selected=selected||[];var html='';Object.keys(groupedPermissions).forEach(function(mod){html+='<div class="m-b-sm"><strong>'+mod+'</strong><div>';groupedPermissions[mod].forEach(function(p){var checked=selected.indexOf(parseInt(p.id,10))>-1?' checked':'';html+='<label class="checkbox-inline m-r-sm"><input type="checkbox" name="permission_ids[]" value="'+p.id+'"'+checked+'> '+p.action+'</label>';});html+='</div></div>';});$('#permissions-box').html(html);}
function loadPermissions(cb){AdminApp.request(ADMIN_BASE+'/api/permissions','GET').done(function(res){groupedPermissions=res.data.grouped||{};if(cb)cb();});}
function loadList(){AdminApp.request(ADMIN_BASE+'/api/roles','GET').done(function(res){var h='';(res.data.items||[]).forEach(function(r){var a='';if(canUpdate && r.is_super!=1)a+='<button class="btn btn-xs btn-primary btn-edit" data-id="'+r.id+'"><i class="fa fa-pencil"></i></button> ';if(canDelete && r.is_super!=1)a+='<button class="btn btn-xs btn-danger btn-delete" data-id="'+r.id+'"><i class="fa fa-trash"></i></button>';if(r.is_super==1)a+='<span class="text-muted">Locked</span>';h+='<tr><td>'+r.id+'</td><td>'+r.name+'</td><td>'+r.slug+'</td><td>'+(r.is_super==1?'Yes':'No')+'</td><td>'+(r.status==1?'Active':'Inactive')+'</td><td>'+a+'</td></tr>';});$('#data-table tbody').html(h||'<tr><td colspan="6" class="text-center text-muted">No roles</td></tr>');});}
$('#btn-add').on('click',function(){$('#main-form')[0].reset();$('#record-id').val('');renderPermissions([]);$('#modal-title').text('Add Role');$('#form-modal').modal('show');});
$('#main-form').on('submit',function(e){e.preventDefault();var id=$('#record-id').val(),url=id?ADMIN_BASE+'/api/roles/'+id:ADMIN_BASE+'/api/roles',$btn=$('#save-btn');AdminApp.setButtonLoading($btn,true);AdminApp.request(url,'POST',$(this).serialize()).done(function(res){AdminApp.toast('success',res.message);$('#form-modal').modal('hide');loadList();}).always(function(){AdminApp.setButtonLoading($btn,false);});});
$(document).on('click','.btn-edit',function(){AdminApp.request(ADMIN_BASE+'/api/roles/'+$(this).data('id'),'GET').done(function(res){var r=res.data;$('#record-id').val(r.id);$('#f-name').val(r.name);$('#f-status').val(r.status);renderPermissions(r.permission_ids||[]);$('#modal-title').text('Edit Role');$('#form-modal').modal('show');});});
$(document).on('click','.btn-delete',function(){var id=$(this).data('id');AdminApp.confirmDelete(function(){AdminApp.request(ADMIN_BASE+'/api/roles/'+id+'/delete','POST').done(function(res){AdminApp.toast('success',res.message);loadList();});});});
loadPermissions(loadList);
</script>
<?= $this->endSection() ?>
