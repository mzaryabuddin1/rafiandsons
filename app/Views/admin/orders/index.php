<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
<div class="col-lg-8"><h2>Orders</h2></div>
<div class="col-lg-4 text-right"><?php if (!empty($canCreate)): ?><button class="btn btn-primary" id="btn-add"><i class="fa fa-plus"></i> Create Order</button><?php endif; ?></div>
</div>
<div class="ibox"><div class="ibox-title"><h5>Order Bookings</h5></div>
<div class="ibox-content">
<div class="row m-b-sm">
<div class="col-md-3"><input type="text" id="search" class="form-control" placeholder="Order # / name / phone"></div>
<div class="col-md-2"><select id="filter-status" class="form-control"><option value="">All Statuses</option><?php foreach ($statuses as $k=>$v): ?><option value="<?= esc($k) ?>"><?= esc($v) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><input type="date" id="date-from" class="form-control"></div>
<div class="col-md-2"><input type="date" id="date-to" class="form-control"></div>
<div class="col-md-2"><button class="btn btn-primary" id="btn-filter">Filter</button></div>
</div>
<div class="table-responsive"><table class="table table-striped table-bordered" id="data-table"><thead><tr><th>Order #</th><th>Customer</th><th>Phone</th><th>Plan</th><th>Total</th><th>Status</th><th>Payment</th><th>Date</th><th width="140">Actions</th></tr></thead><tbody></tbody></table></div>
</div></div>

<div class="modal inmodal" id="form-modal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content animated fadeIn">
<form id="main-form"><div class="modal-header navy-bg"><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button><h4 class="modal-title">Create Installment Order</h4></div>
<div class="modal-body">
<div class="row">
<div class="col-md-6"><div class="form-group"><label>Customer (optional existing)</label><select class="form-control" name="customer_id" id="f-customer"><option value="">New customer</option><?php foreach ($customers as $c): ?><option value="<?= $c['id'] ?>" data-name="<?= esc($c['name']) ?>" data-phone="<?= esc($c['phone']) ?>" data-email="<?= esc($c['email'] ?? '') ?>"><?= esc($c['name']) ?> (<?= esc($c['phone']) ?>)</option><?php endforeach; ?></select></div></div>
<div class="col-md-6"><div class="form-group"><label>Product *</label><select class="form-control" name="product_id" id="f-product" required><option value="">Select</option><?php foreach ($products as $p): ?><option value="<?= $p['id'] ?>"><?= esc($p['name']) ?> — <?= esc($p['price']) ?></option><?php endforeach; ?></select></div></div>
</div>
<div class="row">
<div class="col-md-4"><div class="form-group"><label>Name *</label><input class="form-control" name="customer_name" id="f-name" required></div></div>
<div class="col-md-4"><div class="form-group"><label>Phone *</label><input class="form-control" name="customer_phone" id="f-phone" required></div></div>
<div class="col-md-4"><div class="form-group"><label>Email</label><input class="form-control" name="customer_email" id="f-email"></div></div>
</div>
<div class="row">
<div class="col-md-4"><div class="form-group"><label>CNIC</label><input class="form-control" name="customer_cnic" id="f-cnic"></div></div>
<div class="col-md-4"><div class="form-group"><label>City</label><input class="form-control" name="customer_city" id="f-city"></div></div>
<div class="col-md-4"><div class="form-group"><label>Qty</label><input type="number" class="form-control" name="quantity" value="1" min="1"></div></div>
</div>
<div class="form-group"><label>Address</label><input class="form-control" name="customer_address" id="f-address"></div>
<div class="form-group"><label>Installment Plan</label><select class="form-control" name="installment_plan_id" id="f-plan"><option value="">None</option><?php foreach ($plans as $p): ?><option value="<?= $p['id'] ?>"><?= esc($p['name']) ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label>Admin Notes</label><textarea class="form-control" name="admin_notes" rows="2"></textarea></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-white" data-dismiss="modal">Close</button><button type="submit" class="btn btn-primary" id="save-btn">Create</button></div>
</form></div></div></div>

<div class="modal inmodal" id="detail-modal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content animated fadeIn">
<div class="modal-header navy-bg"><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button><h4 class="modal-title">Order Details</h4></div>
<div class="modal-body" id="detail-body"></div>
<?php if (!empty($canUpdate)): ?>
<div class="modal-footer" style="display:block;text-align:left;">
<form id="status-form" class="form-inline m-b-sm">
<input type="hidden" name="id" id="status-id">
<select name="status" id="status-select" class="form-control m-r-sm"><?php foreach ($statuses as $k=>$v): ?><option value="<?= esc($k) ?>"><?= esc($v) ?></option><?php endforeach; ?></select>
<input type="text" name="admin_notes" id="status-notes" class="form-control m-r-sm" placeholder="Admin notes" style="width:40%;">
<button type="submit" class="btn btn-primary">Update Status</button>
</form>
<button type="button" class="btn btn-success" id="btn-verify-payment" style="display:none;"><i class="fa fa-check"></i> Verify Payment</button>
</div>
<?php endif; ?>
</div></div></div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
var canUpdate=<?= !empty($canUpdate)?'true':'false' ?>, canDelete=<?= !empty($canDelete)?'true':'false' ?>;
var currentOrder=null;

function paymentBadge(r){
    if(parseInt(r.payment_verified,10)===1){
        return '<span class="badge badge-primary">Verified</span>';
    }
    if(r.receipt_image || r.receipt_url){
        return '<span class="badge badge-warning">Receipt Uploaded</span>';
    }
    return '<span class="badge">No Receipt</span>';
}

function loadList(){
    AdminApp.request(ADMIN_BASE+'/api/orders','GET',{search:$('#search').val(),status:$('#filter-status').val(),date_from:$('#date-from').val(),date_to:$('#date-to').val()}).done(function(res){
        var h='';
        (res.data.items||[]).forEach(function(r){
            var a='<button class="btn btn-xs btn-primary btn-view" data-id="'+r.id+'"><i class="fa fa-eye"></i></button> ';
            if(canDelete)a+='<button class="btn btn-xs btn-danger btn-delete" data-id="'+r.id+'"><i class="fa fa-trash"></i></button>';
            h+='<tr><td>'+r.order_number+'</td><td>'+r.customer_name+'</td><td>'+r.customer_phone+'</td><td>'+(r.plan_name||'-')+'</td><td>'+r.total_payable+'</td><td><span class="badge badge-primary">'+(r.status_label||r.status)+'</span></td><td>'+paymentBadge(r)+'</td><td>'+(r.created_at||'')+'</td><td>'+a+'</td></tr>';
        });
        if(!h)h='<tr><td colspan="9" class="text-center text-muted">No orders found</td></tr>';
        $('#data-table tbody').html(h);
    });
}

function renderDetail(r){
    currentOrder=r;
    var h='<p><strong>'+r.order_number+'</strong> — '+r.status_label+'</p>';
    h+='<p>'+r.customer_name+' | '+r.customer_phone+' | '+(r.customer_email||'')+'</p>';
    h+='<p>'+(r.customer_address||'')+' '+(r.customer_city||'')+'</p>';
    h+='<p>Plan: '+(r.plan_name||'-')+' | Down: '+r.down_payment+' | Monthly: '+r.monthly_installment+' x '+r.months+'</p>';
    h+='<table class="table table-bordered"><thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead><tbody>';
    (r.items||[]).forEach(function(i){h+='<tr><td>'+i.product_name+'</td><td>'+i.quantity+'</td><td>'+i.unit_price+'</td><td>'+i.line_total+'</td></tr>';});
    h+='</tbody></table>';
    h+='<p>Notes: '+(r.admin_notes||'-')+'</p>';
    h+='<hr><h4>Payment Receipt</h4>';
    h+='<p>Status: '+paymentBadge(r)+(r.payment_verified_at?' <small class="text-muted">('+r.payment_verified_at+')</small>':'')+'</p>';
    if(r.receipt_url){
        h+='<p><a href="'+r.receipt_url+'" target="_blank" rel="noopener"><img src="'+r.receipt_url+'" alt="Payment receipt" style="max-width:100%;max-height:320px;border:1px solid #ddd;border-radius:6px;"></a></p>';
        h+='<p><a href="'+r.receipt_url+'" target="_blank" rel="noopener">Open full receipt</a></p>';
    }else{
        h+='<p class="text-muted">Customer did not upload a receipt.</p>';
    }
    $('#detail-body').html(h);
    $('#status-id').val(r.id);
    $('#status-select').val(r.status);
    $('#status-notes').val(r.admin_notes||'');
    if(canUpdate && r.receipt_url && parseInt(r.payment_verified,10)!==1){
        $('#btn-verify-payment').show();
    }else{
        $('#btn-verify-payment').hide();
    }
    $('#detail-modal').modal('show');
}

$('#btn-filter,#search').on('click keyup',function(e){if(e.type==='keyup'&&e.keyCode!==13&&e.target.id==='search')return;loadList();});
$('#btn-add').on('click',function(){$('#main-form')[0].reset();$('#form-modal').modal('show');});
$('#f-customer').on('change',function(){var o=$(this).find(':selected');if(o.val()){$('#f-name').val(o.data('name'));$('#f-phone').val(o.data('phone'));$('#f-email').val(o.data('email'));}});
$('#main-form').on('submit',function(e){e.preventDefault();var $btn=$('#save-btn');AdminApp.setButtonLoading($btn,true);AdminApp.request(ADMIN_BASE+'/api/orders','POST',$(this).serialize()).done(function(res){AdminApp.toast('success',res.message+' ('+res.data.order_number+')');$('#form-modal').modal('hide');loadList();}).always(function(){AdminApp.setButtonLoading($btn,false);});});
$(document).on('click','.btn-view',function(){AdminApp.request(ADMIN_BASE+'/api/orders/'+$(this).data('id'),'GET').done(function(res){renderDetail(res.data);});});
$('#status-form').on('submit',function(e){e.preventDefault();var id=$('#status-id').val();AdminApp.request(ADMIN_BASE+'/api/orders/'+id+'/status','POST',$(this).serialize()).done(function(res){AdminApp.toast('success',res.message);$('#detail-modal').modal('hide');loadList();});});
$('#btn-verify-payment').on('click',function(){
    if(!currentOrder)return;
    AdminApp.request(ADMIN_BASE+'/api/orders/'+currentOrder.id+'/verify-payment','POST').done(function(res){
        AdminApp.toast('success',res.message);
        AdminApp.request(ADMIN_BASE+'/api/orders/'+currentOrder.id,'GET').done(function(r){renderDetail(r.data);loadList();});
    });
});
$(document).on('click','.btn-delete',function(){var id=$(this).data('id');AdminApp.confirmDelete(function(){AdminApp.request(ADMIN_BASE+'/api/orders/'+id+'/delete','POST').done(function(res){AdminApp.toast('success',res.message);loadList();});},'Archive this order?');});
$(loadList);
</script>
<?= $this->endSection() ?>
