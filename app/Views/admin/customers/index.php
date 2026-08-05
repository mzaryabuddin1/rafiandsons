<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
<div class="col-lg-8"><h2>Customers</h2></div>
<div class="col-lg-4 text-right"><?php if (!empty($canCreate)): ?><button class="btn btn-primary" id="btn-add"><i class="fa fa-plus"></i> Add Customer</button><?php endif; ?></div>
</div>
<div class="ibox"><div class="ibox-title"><h5>Customers</h5><div class="ibox-tools"><input type="text" id="search" class="form-control form-control-sm" placeholder="Search name/phone/email" style="width:240px;display:inline-block;"></div></div>
<div class="ibox-content"><div class="table-responsive"><table class="table table-striped table-bordered" id="data-table"><thead><tr><th>ID</th><th>Name</th><th>Phone</th><th>Email</th><th>City</th><th>Status</th><th width="160">Actions</th></tr></thead><tbody></tbody></table></div></div></div>
<div class="modal inmodal" id="form-modal" tabindex="-1"><div class="modal-dialog"><div class="modal-content animated fadeIn">
<form id="main-form"><div class="modal-header navy-bg"><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button><h4 class="modal-title" id="modal-title">Add Customer</h4></div>
<div class="modal-body">
<input type="hidden" name="id" id="record-id">
<div class="form-group"><label>Name *</label><input class="form-control" name="name" id="f-name" required></div>
<div class="row"><div class="col-md-6"><div class="form-group"><label>Phone *</label><input class="form-control" name="phone" id="f-phone" required></div></div>
<div class="col-md-6"><div class="form-group"><label>Email</label><input type="email" class="form-control" name="email" id="f-email"></div></div></div>
<div class="row"><div class="col-md-6"><div class="form-group"><label>CNIC</label><input class="form-control" name="cnic" id="f-cnic"></div></div>
<div class="col-md-6"><div class="form-group"><label>City</label><input class="form-control" name="city" id="f-city"></div></div></div>
<div class="form-group"><label>Address</label><input class="form-control" name="address" id="f-address"></div>
<div class="form-group"><label>Notes</label><textarea class="form-control" name="notes" id="f-notes" rows="2"></textarea></div>
<div class="form-group"><label>Status</label><select class="form-control" name="status" id="f-status"><option value="1">Active</option><option value="0">Inactive</option></select></div>
<div id="order-history" class="m-t-sm"></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-white" data-dismiss="modal">Close</button><button type="submit" class="btn btn-primary" id="save-btn">Save</button></div>
</form></div></div></div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
var canUpdate=<?= !empty($canUpdate)?'true':'false' ?>, canDelete=<?= !empty($canDelete)?'true':'false' ?>;
function loadList(){AdminApp.request(ADMIN_BASE+'/api/customers','GET',{search:$('#search').val()}).done(function(res){var h='';(res.data.items||[]).forEach(function(r){var a='';if(canUpdate)a+='<button class="btn btn-xs btn-primary btn-edit" data-id="'+r.id+'"><i class="fa fa-pencil"></i></button> ';if(canDelete)a+='<button class="btn btn-xs btn-danger btn-delete" data-id="'+r.id+'"><i class="fa fa-trash"></i></button>';h+='<tr><td>'+r.id+'</td><td>'+r.name+'</td><td>'+r.phone+'</td><td>'+(r.email||'')+'</td><td>'+(r.city||'')+'</td><td>'+(r.status==1?'Active':'Inactive')+'</td><td>'+a+'</td></tr>';});if(!h)h='<tr><td colspan="7" class="text-center text-muted">No customers found</td></tr>';$('#data-table tbody').html(h);});}
$('#btn-add').on('click',function(){$('#main-form')[0].reset();$('#record-id').val('');$('#order-history').html('');$('#modal-title').text('Add Customer');$('#form-modal').modal('show');});
$('#search').on('keyup',loadList);
$('#main-form').on('submit',function(e){e.preventDefault();var id=$('#record-id').val(),url=id?ADMIN_BASE+'/api/customers/'+id:ADMIN_BASE+'/api/customers',$btn=$('#save-btn');AdminApp.setButtonLoading($btn,true);AdminApp.request(url,'POST',$(this).serialize()).done(function(res){AdminApp.toast('success',res.message);$('#form-modal').modal('hide');loadList();}).always(function(){AdminApp.setButtonLoading($btn,false);});});
$(document).on('click','.btn-edit',function(){AdminApp.request(ADMIN_BASE+'/api/customers/'+$(this).data('id'),'GET').done(function(res){var r=res.data;$('#record-id').val(r.id);$('#f-name').val(r.name);$('#f-phone').val(r.phone);$('#f-email').val(r.email||'');$('#f-cnic').val(r.cnic||'');$('#f-city').val(r.city||'');$('#f-address').val(r.address||'');$('#f-notes').val(r.notes||'');$('#f-status').val(r.status);var oh='<strong>Order History</strong><ul class="m-t-xs">';(r.orders||[]).forEach(function(o){oh+='<li>'+o.order_number+' — '+o.status+' — '+o.total_payable+'</li>';});if(!(r.orders||[]).length)oh+='<li class="text-muted">No orders</li>';oh+='</ul>';$('#order-history').html(oh);$('#modal-title').text('Edit Customer');$('#form-modal').modal('show');});});
$(document).on('click','.btn-delete',function(){var id=$(this).data('id');AdminApp.confirmDelete(function(){AdminApp.request(ADMIN_BASE+'/api/customers/'+id+'/delete','POST').done(function(res){AdminApp.toast('success',res.message);loadList();});});});
$(loadList);
</script>
<?= $this->endSection() ?>
