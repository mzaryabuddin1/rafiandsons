<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php $isEdit = ! empty($isEdit); ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
    <div class="col-lg-8"><h2><?= esc($pageTitle) ?></h2></div>
    <div class="col-lg-4 text-right" style="padding-top:20px;">
        <a href="<?= site_url('admin/customers') ?>" class="btn btn-white"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
</div>

<div class="ibox">
    <div class="ibox-title"><h5><?= $isEdit ? 'Edit Customer' : 'Customer Details' ?></h5></div>
    <div class="ibox-content">
        <form id="main-form">
            <input type="hidden" name="id" id="record-id" value="<?= $isEdit ? (int) $recordId : '' ?>">
            <div class="form-group"><label>Name *</label><input class="form-control" name="name" id="f-name" required></div>
            <div class="row">
                <div class="col-md-6"><div class="form-group"><label>Phone *</label><input class="form-control" name="phone" id="f-phone" required></div></div>
                <div class="col-md-6"><div class="form-group"><label>Email</label><input type="email" class="form-control" name="email" id="f-email"></div></div>
            </div>
            <div class="row">
                <div class="col-md-6"><div class="form-group"><label>CNIC</label><input class="form-control" name="cnic" id="f-cnic"></div></div>
                <div class="col-md-6"><div class="form-group"><label>City</label><input class="form-control" name="city" id="f-city"></div></div>
            </div>
            <div class="form-group"><label>Address</label><input class="form-control" name="address" id="f-address"></div>
            <div class="form-group"><label>Notes</label><textarea class="form-control" name="notes" id="f-notes" rows="2"></textarea></div>
            <div class="form-group"><label>Status</label><select class="form-control" name="status" id="f-status"><option value="1">Active</option><option value="0">Inactive</option></select></div>
            <?php if ($isEdit): ?>
                <div id="order-history" class="m-t-sm"></div>
            <?php endif; ?>
            <div class="m-t-md">
                <button type="submit" class="btn btn-primary" id="save-btn">Save</button>
                <a href="<?= site_url('admin/customers') ?>" class="btn btn-white">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
var isEdit = <?= $isEdit ? 'true' : 'false' ?>;
var recordId = <?= $isEdit ? (int) $recordId : 'null' ?>;

function fillOrderHistory(r) {
    if (!isEdit) return;
    var oh = '<p><strong>Account:</strong> ' + (r.is_registered
        ? '<span class="badge badge-success">Registered</span>'
        : '<span class="badge badge-default">Guest checkout</span>');
    if (r.last_login_at) oh += ' &nbsp; <strong>Last login:</strong> ' + r.last_login_at;
    oh += '</p><strong>Order History</strong><ul class="m-t-xs">';
    (r.orders || []).forEach(function (o) {
        oh += '<li>' + o.order_number + ' — ' + o.status + ' — ' + o.total_payable + '</li>';
    });
    if (!(r.orders || []).length) oh += '<li class="text-muted">No orders</li>';
    oh += '</ul>';
    $('#order-history').html(oh);
}

if (isEdit && recordId) {
    AdminApp.request(ADMIN_BASE + '/api/customers/' + recordId, 'GET').done(function (res) {
        var r = res.data;
        $('#record-id').val(r.id);
        $('#f-name').val(r.name);
        $('#f-phone').val(r.phone);
        $('#f-email').val(r.email || '');
        $('#f-cnic').val(r.cnic || '');
        $('#f-city').val(r.city || '');
        $('#f-address').val(r.address || '');
        $('#f-notes').val(r.notes || '');
        $('#f-status').val(r.status);
        fillOrderHistory(r);
    });
}

$('#main-form').on('submit', function (e) {
    e.preventDefault();
    var id = $('#record-id').val();
    var url = id ? ADMIN_BASE + '/api/customers/' + id : ADMIN_BASE + '/api/customers';
    var $btn = $('#save-btn');
    AdminApp.setButtonLoading($btn, true);
    AdminApp.request(url, 'POST', $(this).serialize()).done(function (res) {
        AdminApp.toast('success', res.message);
        window.location.href = ADMIN_BASE + '/customers';
    }).always(function () {
        AdminApp.setButtonLoading($btn, false);
    });
});
</script>
<?= $this->endSection() ?>
