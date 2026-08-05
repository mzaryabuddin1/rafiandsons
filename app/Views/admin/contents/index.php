<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
<div class="col-lg-8"><h2>Website Contents</h2></div>
<div class="col-lg-4 text-right"><?php if (!empty($canCreate)): ?><button class="btn btn-primary" id="btn-add"><i class="fa fa-plus"></i> Add Content</button><?php endif; ?></div>
</div>
<div class="ibox"><div class="ibox-title"><h5>Pages / Sections</h5><div class="ibox-tools"><input type="text" id="search" class="form-control form-control-sm" placeholder="Search..." style="width:220px;display:inline-block;"></div></div>
<div class="ibox-content"><div class="table-responsive"><table class="table table-striped table-bordered" id="data-table"><thead><tr><th>ID</th><th>Title</th><th>Slug</th><th>Status</th><th width="140">Actions</th></tr></thead><tbody></tbody></table></div></div></div>
<div class="modal inmodal" id="form-modal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content animated fadeIn">
<form id="main-form"><div class="modal-header navy-bg"><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button><h4 class="modal-title" id="modal-title">Add Content</h4></div>
<div class="modal-body">
<input type="hidden" name="id" id="record-id">
<div class="row"><div class="col-md-6"><div class="form-group"><label>Title *</label><input class="form-control" name="title" id="f-title" required></div></div>
<div class="col-md-6"><div class="form-group"><label>Slug</label><input class="form-control" name="slug" id="f-slug"></div></div></div>
<div class="form-group"><label>Body</label><textarea class="form-control" name="body" id="f-body" rows="8"></textarea></div>
<div class="form-group"><label>Status</label><select class="form-control" name="status" id="f-status"><option value="1">Active</option><option value="0">Inactive</option></select></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-white" data-dismiss="modal">Close</button><button type="submit" class="btn btn-primary" id="save-btn">Save</button></div>
</form></div></div></div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
var canUpdate=<?= !empty($canUpdate)?'true':'false' ?>, canDelete=<?= !empty($canDelete)?'true':'false' ?>;
function loadList(){AdminApp.request(ADMIN_BASE+'/api/contents','GET',{search:$('#search').val()}).done(function(res){var h='';(res.data.items||[]).forEach(function(r){var a='';if(canUpdate)a+='<button class="btn btn-xs btn-primary btn-edit" data-id="'+r.id+'"><i class="fa fa-pencil"></i></button> ';if(canDelete)a+='<button class="btn btn-xs btn-danger btn-delete" data-id="'+r.id+'"><i class="fa fa-trash"></i></button>';h+='<tr><td>'+r.id+'</td><td>'+r.title+'</td><td>'+r.slug+'</td><td>'+(r.status==1?'Active':'Inactive')+'</td><td>'+a+'</td></tr>';});if(!h)h='<tr><td colspan="5" class="text-center text-muted">No content found</td></tr>';$('#data-table tbody').html(h);});}
$('#btn-add').on('click',function(){$('#main-form')[0].reset();$('#record-id').val('');$('#modal-title').text('Add Content');$('#form-modal').modal('show');});
$('#search').on('keyup',loadList);
$('#main-form').on('submit',function(e){e.preventDefault();var id=$('#record-id').val(),url=id?ADMIN_BASE+'/api/contents/'+id:ADMIN_BASE+'/api/contents',$btn=$('#save-btn');AdminApp.setButtonLoading($btn,true);AdminApp.request(url,'POST',$(this).serialize()).done(function(res){AdminApp.toast('success',res.message);$('#form-modal').modal('hide');loadList();}).always(function(){AdminApp.setButtonLoading($btn,false);});});
$(document).on('click','.btn-edit',function(){AdminApp.request(ADMIN_BASE+'/api/contents/'+$(this).data('id'),'GET').done(function(res){var r=res.data;$('#record-id').val(r.id);$('#f-title').val(r.title);$('#f-slug').val(r.slug);$('#f-body').val(r.body||'');$('#f-status').val(r.status);$('#modal-title').text('Edit Content');$('#form-modal').modal('show');});});
$(document).on('click','.btn-delete',function(){var id=$(this).data('id');AdminApp.confirmDelete(function(){AdminApp.request(ADMIN_BASE+'/api/contents/'+id+'/delete','POST').done(function(res){AdminApp.toast('success',res.message);loadList();});});});
$(loadList);
</script>
<?= $this->endSection() ?>
