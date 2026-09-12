<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php $isEdit = ! empty($isEdit); ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
    <div class="col-lg-8"><h2><?= esc($pageTitle) ?></h2></div>
    <div class="col-lg-4 text-right" style="padding-top:20px;">
        <a href="<?= admin_url('bank-accounts') ?>" class="btn btn-white"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
</div>

<div class="ibox">
    <div class="ibox-title"><h5><?= $isEdit ? 'Edit Bank Account' : 'Bank Account Details' ?></h5></div>
    <div class="ibox-content">
        <form id="main-form" enctype="multipart/form-data">
            <input type="hidden" name="id" id="record-id" value="<?= $isEdit ? (int) $recordId : '' ?>">
            <div class="form-group">
                <label>Bank Name *</label>
                <input class="form-control" name="bank_name" id="f-bank" required placeholder="e.g. HBL, Meezan Bank">
            </div>
            <div class="form-group">
                <label>Bank Logo</label>
                <input type="file" class="form-control" name="logo" id="f-logo" accept="image/jpeg,image/png,image/webp,image/gif">
                <small class="text-muted">Optional. JPG, PNG, WEBP, or GIF.</small>
                <div id="logo-preview-wrap" class="m-t-sm" style="display:none;">
                    <img id="logo-preview" src="" alt="Bank logo" style="max-height:48px;max-width:120px;border:1px solid #ddd;border-radius:4px;padding:4px;background:#fff;">
                </div>
            </div>
            <div class="form-group">
                <label>Account Title *</label>
                <input class="form-control" name="account_title" id="f-title" required placeholder="Account holder name">
            </div>
            <div class="form-group">
                <label>Account Number *</label>
                <input class="form-control" name="account_number" id="f-number" required>
            </div>
            <div class="form-group">
                <label>IBAN</label>
                <input class="form-control" name="iban" id="f-iban" placeholder="Optional">
            </div>
            <div class="form-group">
                <label>Branch</label>
                <input class="form-control" name="branch" id="f-branch" placeholder="Optional">
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" id="f-sort" value="0">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status" id="f-status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="m-t-md">
                <button type="submit" class="btn btn-primary" id="save-btn">Save</button>
                <a href="<?= admin_url('bank-accounts') ?>" class="btn btn-white">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
var isEdit = <?= $isEdit ? 'true' : 'false' ?>;
var recordId = <?= $isEdit ? (int) $recordId : 'null' ?>;

function setLogoPreview(url) {
    if (url) {
        $('#logo-preview').attr('src', url);
        $('#logo-preview-wrap').show();
    } else {
        $('#logo-preview').attr('src', '');
        $('#logo-preview-wrap').hide();
    }
}

$('#f-logo').on('change', function () {
    var file = this.files && this.files[0];
    if (!file) return;
    setLogoPreview(URL.createObjectURL(file));
});

if (isEdit && recordId) {
    AdminApp.request(ADMIN_BASE + '/api/bank-accounts/' + recordId, 'GET').done(function (res) {
        var r = res.data;
        $('#record-id').val(r.id);
        $('#f-bank').val(r.bank_name);
        $('#f-title').val(r.account_title);
        $('#f-number').val(r.account_number);
        $('#f-iban').val(r.iban || '');
        $('#f-branch').val(r.branch || '');
        $('#f-sort').val(r.sort_order);
        $('#f-status').val(String(r.status));
        setLogoPreview(r.logo_url || null);
    });
}

$('#main-form').on('submit', function (e) {
    e.preventDefault();
    var id = $('#record-id').val();
    var url = id ? ADMIN_BASE + '/api/bank-accounts/' + id : ADMIN_BASE + '/api/bank-accounts';
    var $btn = $('#save-btn');
    AdminApp.setButtonLoading($btn, true);
    AdminApp.request(url, 'POST', new FormData(this)).done(function (res) {
        AdminApp.toast('success', res.message);
        window.location.href = ADMIN_BASE + '/bank-accounts';
    }).always(function () {
        AdminApp.setButtonLoading($btn, false);
    });
});
</script>
<?= $this->endSection() ?>
