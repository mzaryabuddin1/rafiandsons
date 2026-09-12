<?= $this->extend('admin/layout') ?>
<?= $this->section('styles') ?>
<style>
.order-status-badge {
    display: inline-block;
    padding: 4px 8px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 3px;
    color: #fff;
    white-space: nowrap;
}
.order-status-new { background: #23c6c8; }
.order-status-under_review { background: #f8ac59; }
.order-status-customer_contacted { background: #1c84c6; }
.order-status-approved { background: #1ab394; }
.order-status-rejected { background: #ed5565; }
.order-status-processing { background: #7a5af8; }
.order-status-completed { background: #1a7f37; }
.order-status-cancelled { background: #676a6c; }
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
<div class="col-lg-8"><h2>Order Details</h2><ol class="breadcrumb"><li><a href="<?= site_url('admin/orders') ?>">Orders</a></li><li class="active">Detail</li></ol></div>
<div class="col-lg-4 text-right"><a href="<?= site_url('admin/orders') ?>" class="btn btn-white"><i class="fa fa-arrow-left"></i> Back to Orders</a></div>
</div>
<div class="ibox"><div class="ibox-title"><h5>Order</h5></div>
<div class="ibox-content">
<div id="detail-body"><p class="text-muted">Loading…</p></div>
<?php if (!empty($canUpdate)): ?>
<hr>
<form id="status-form" class="form-inline m-b-sm">
<input type="hidden" name="id" id="status-id">
<select name="status" id="status-select" class="form-control m-r-sm"><?php foreach ($statuses as $k=>$v): ?><option value="<?= esc($k) ?>"><?= esc($v) ?></option><?php endforeach; ?></select>
<input type="text" name="admin_notes" id="status-notes" class="form-control m-r-sm" placeholder="Admin notes" style="width:40%;">
<button type="submit" class="btn btn-primary">Update Status</button>
</form>
<button type="button" class="btn btn-success" id="btn-verify-payment" style="display:none;"><i class="fa fa-check"></i> Verify Payment</button>
<?php endif; ?>
</div></div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
var canUpdate=<?= !empty($canUpdate)?'true':'false' ?>;
var recordId=<?= (int) ($recordId ?? 0) ?>;
var currentOrder=null;

function statusBadge(r){
    var status = String(r.status || '').toLowerCase();
    var label = r.status_label || r.status || '-';
    var cls = 'order-status-badge order-status-' + status.replace(/[^a-z0-9_]/g, '');
    return '<span class="' + cls + '">' + label + '</span>';
}

function paymentBadge(r){
    if(parseInt(r.payment_verified,10)===1){
        return '<span class="badge badge-primary">Verified</span>';
    }
    if(r.receipt_image || r.receipt_url){
        return '<span class="badge badge-warning">Receipt Uploaded</span>';
    }
    return '<span class="badge">No Receipt</span>';
}

function renderDetail(r){
    currentOrder=r;
    var h='<p><strong>'+r.order_number+'</strong> — '+statusBadge(r)+'</p>';
    h+='<p>'+r.customer_name+' | '+r.customer_phone+' | '+(r.customer_email||'')+'</p>';
    h+='<p>'+(r.customer_address||'')+' '+(r.customer_city||'')+'</p>';
    h+='<p>Vendor: <strong>'+(r.vendor_label||'Own / No vendor')+'</strong></p>';
    h+='<p>Plan: '+(r.plan_name||'-')+' | Down: '+r.down_payment+' | Monthly: '+r.monthly_installment+' x '+r.months+'</p>';
    h+='<table class="table table-bordered"><thead><tr><th>Product</th><th>Vendor</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead><tbody>';
    (r.items||[]).forEach(function(i){h+='<tr><td>'+i.product_name+'</td><td>'+(i.vendor_name||'-')+'</td><td>'+i.quantity+'</td><td>'+i.unit_price+'</td><td>'+i.line_total+'</td></tr>';});
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
}

function loadOrder(){
    AdminApp.request(ADMIN_BASE+'/api/orders/'+recordId,'GET').done(function(res){
        renderDetail(res.data);
    }).fail(function(){
        $('#detail-body').html('<p class="text-danger">Order not found.</p>');
    });
}

$('#status-form').on('submit',function(e){
    e.preventDefault();
    var id=$('#status-id').val();
    AdminApp.request(ADMIN_BASE+'/api/orders/'+id+'/status','POST',$(this).serialize()).done(function(res){
        AdminApp.toast('success',res.message);
        loadOrder();
    });
});
$('#btn-verify-payment').on('click',function(){
    if(!currentOrder)return;
    AdminApp.request(ADMIN_BASE+'/api/orders/'+currentOrder.id+'/verify-payment','POST').done(function(res){
        AdminApp.toast('success',res.message);
        loadOrder();
    });
});
loadOrder();
</script>
<?= $this->endSection() ?>
