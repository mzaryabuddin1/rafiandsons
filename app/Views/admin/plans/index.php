<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
<div class="col-lg-8"><h2>Installment Plans</h2></div>
<div class="col-lg-4 text-right"><?php if (!empty($canCreate)): ?><button class="btn btn-primary" id="btn-add"><i class="fa fa-plus"></i> Add Plan</button><?php endif; ?></div>
</div>
<div class="ibox"><div class="ibox-title"><h5>Plans</h5><div class="ibox-tools"><input type="text" id="search" class="form-control form-control-sm" placeholder="Search..." style="width:220px;display:inline-block;"></div></div>
<div class="ibox-content"><div class="table-responsive"><table class="table table-striped table-bordered" id="data-table"><thead><tr><th>ID</th><th>Name</th><th>Down</th><th>Monthly</th><th>Months</th><th>Total</th><th>Status</th><th width="140">Actions</th></tr></thead><tbody></tbody></table></div></div></div>
<div class="modal inmodal" id="form-modal" tabindex="-1"><div class="modal-dialog"><div class="modal-content animated fadeIn">
<form id="main-form"><div class="modal-header navy-bg"><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button><h4 class="modal-title" id="modal-title">Add Plan</h4></div>
<div class="modal-body">
<input type="hidden" name="id" id="record-id">
<div class="form-group"><label>Name *</label><input class="form-control" name="name" id="f-name" required></div>
<div class="row">
<div class="col-md-6"><div class="form-group"><label>Down Payment</label><input type="number" step="0.01" class="form-control" name="down_payment" id="f-down" value="0"></div></div>
<div class="col-md-6"><div class="form-group"><label>Monthly Installment</label><input type="number" step="0.01" class="form-control" name="monthly_installment" id="f-monthly" value="0"></div></div>
</div>
<div class="row">
<div class="col-md-4"><div class="form-group"><label>Months</label><input type="number" class="form-control" name="months" id="f-months" value="1"></div></div>
<div class="col-md-4"><div class="form-group"><label>Processing Charges</label><input type="number" step="0.01" class="form-control" name="processing_charges" id="f-charges" value="0"></div></div>
<div class="col-md-4"><div class="form-group"><label>Total Payable</label><input type="number" step="0.01" class="form-control" name="total_payable" id="f-total" value="0"></div></div>
</div>
<div class="form-group"><label>Terms</label><textarea class="form-control" name="terms" id="f-terms" rows="3"></textarea></div>
<div class="form-group"><label>Status</label><select class="form-control" name="status" id="f-status"><option value="1">Active</option><option value="0">Inactive</option></select></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-white" data-dismiss="modal">Close</button><button type="submit" class="btn btn-primary" id="save-btn">Save</button></div>
</form></div></div></div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
var canUpdate=<?= !empty($canUpdate)?'true':'false' ?>, canDelete=<?= !empty($canDelete)?'true':'false' ?>;
function calcTotal(){var t=(parseFloat($('#f-down').val())||0)+((parseFloat($('#f-monthly').val())||0)*(parseInt($('#f-months').val())||1))+(parseFloat($('#f-charges').val())||0);$('#f-total').val(t.toFixed(2));}
$('#f-down,#f-monthly,#f-months,#f-charges').on('input',calcTotal);
function loadList(){AdminApp.request(ADMIN_BASE+'/api/installment-plans','GET',{search:$('#search').val()}).done(function(res){var h='';(res.data.items||[]).forEach(function(r){var a='';if(canUpdate)a+='<button class="btn btn-xs btn-primary btn-edit" data-id="'+r.id+'"><i class="fa fa-pencil"></i></button> ';if(canDelete)a+='<button class="btn btn-xs btn-danger btn-delete" data-id="'+r.id+'"><i class="fa fa-trash"></i></button>';h+='<tr><td>'+r.id+'</td><td>'+r.name+'</td><td>'+r.down_payment+'</td><td>'+r.monthly_installment+'</td><td>'+r.months+'</td><td>'+r.total_payable+'</td><td>'+(r.status==1?'Active':'Inactive')+'</td><td>'+a+'</td></tr>';});if(!h)h='<tr><td colspan="8" class="text-center text-muted">No plans found</td></tr>';$('#data-table tbody').html(h);});}
$('#btn-add').on('click',function(){$('#main-form')[0].reset();$('#record-id').val('');$('#modal-title').text('Add Plan');$('#form-modal').modal('show');});
$('#search').on('keyup',loadList);
$('#main-form').on('submit',function(e){e.preventDefault();var id=$('#record-id').val(),url=id?ADMIN_BASE+'/api/installment-plans/'+id:ADMIN_BASE+'/api/installment-plans',$btn=$('#save-btn');AdminApp.setButtonLoading($btn,true);AdminApp.request(url,'POST',$(this).serialize()).done(function(res){AdminApp.toast('success',res.message);$('#form-modal').modal('hide');loadList();}).always(function(){AdminApp.setButtonLoading($btn,false);});});
$(document).on('click','.btn-edit',function(){AdminApp.request(ADMIN_BASE+'/api/installment-plans/'+$(this).data('id'),'GET').done(function(res){var r=res.data;$('#record-id').val(r.id);$('#f-name').val(r.name);$('#f-down').val(r.down_payment);$('#f-monthly').val(r.monthly_installment);$('#f-months').val(r.months);$('#f-charges').val(r.processing_charges);$('#f-total').val(r.total_payable);$('#f-terms').val(r.terms||'');$('#f-status').val(r.status);$('#modal-title').text('Edit Plan');$('#form-modal').modal('show');});});
$(document).on('click','.btn-delete',function(){var id=$(this).data('id');AdminApp.confirmDelete(function(){AdminApp.request(ADMIN_BASE+'/api/installment-plans/'+id+'/delete','POST').done(function(res){AdminApp.toast('success',res.message);loadList();});});});
$(loadList);
</script>
<?= $this->endSection() ?>
