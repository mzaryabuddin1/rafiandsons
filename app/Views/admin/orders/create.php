<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
<div class="col-lg-8"><h2>Create Order</h2><ol class="breadcrumb"><li><a href="<?= site_url('admin/orders') ?>">Orders</a></li><li class="active">Create</li></ol></div>
<div class="col-lg-4 text-right"><a href="<?= site_url('admin/orders') ?>" class="btn btn-white"><i class="fa fa-arrow-left"></i> Back to Orders</a></div>
</div>
<div class="ibox"><div class="ibox-title"><h5>Create Installment Order</h5></div>
<div class="ibox-content">
<form id="main-form">
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
<button type="submit" class="btn btn-primary" id="save-btn">Create</button>
<a href="<?= site_url('admin/orders') ?>" class="btn btn-white">Cancel</a>
</form>
</div></div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
$('#f-customer').on('change',function(){var o=$(this).find(':selected');if(o.val()){$('#f-name').val(o.data('name'));$('#f-phone').val(o.data('phone'));$('#f-email').val(o.data('email'));}});
$('#main-form').on('submit',function(e){
    e.preventDefault();
    var $btn=$('#save-btn');
    AdminApp.setButtonLoading($btn,true);
    AdminApp.request(ADMIN_BASE+'/api/orders','POST',$(this).serialize()).done(function(res){
        AdminApp.toast('success',res.message+' ('+res.data.order_number+')');
        var id = res.data && res.data.id;
        window.location.href = id ? (ADMIN_BASE + '/orders/' + id) : (ADMIN_BASE + '/orders');
    }).always(function(){AdminApp.setButtonLoading($btn,false);});
});
</script>
<?= $this->endSection() ?>
