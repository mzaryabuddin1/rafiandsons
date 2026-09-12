<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<?php $isEdit = ! empty($isEdit); ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
    <div class="col-lg-8">
        <h2><?= $isEdit ? 'Edit User' : 'Add User' ?></h2>
    </div>
    <div class="col-lg-4 text-right">
        <a href="<?= admin_url('users') ?>" class="btn btn-white"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
</div>

<div class="ibox">
    <div class="ibox-title">
        <h5><?= $isEdit ? 'Edit User' : 'New User' ?></h5>
    </div>
    <div class="ibox-content">
        <form id="main-form">
            <input type="hidden" name="id" id="record-id" value="<?= $isEdit ? (int) $recordId : '' ?>">
            <div class="form-group">
                <label>Name *</label>
                <input class="form-control" name="name" id="f-name" required>
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" class="form-control" name="email" id="f-email" required>
            </div>
            <div class="form-group">
                <label>Role *</label>
                <select class="form-control" name="role_id" id="f-role" required>
                    <option value="">Select</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id'] ?>"><?= esc($r['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Password <small id="pwd-hint"><?= $isEdit ? '(leave blank to keep)' : '(required for new)' ?></small></label>
                <input type="password" class="form-control" name="password" id="f-password" <?= $isEdit ? '' : 'required' ?>>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select class="form-control" name="status" id="f-status">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <div class="hr-line-dashed"></div>
            <div class="text-right">
                <a href="<?= admin_url('users') ?>" class="btn btn-white">Cancel</a>
                <button type="submit" class="btn btn-primary" id="save-btn">Save</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
var isEdit = <?= $isEdit ? 'true' : 'false' ?>;
var recordId = <?= $isEdit ? (int) $recordId : 'null' ?>;

$('#main-form').on('submit', function (e) {
    e.preventDefault();
    var url = isEdit ? ADMIN_BASE + '/api/users/' + recordId : ADMIN_BASE + '/api/users';
    var $btn = $('#save-btn');
    AdminApp.setButtonLoading($btn, true);
    AdminApp.request(url, 'POST', $(this).serialize()).done(function (res) {
        AdminApp.toast('success', res.message);
        window.location.href = ADMIN_BASE + '/users';
    }).always(function () {
        AdminApp.setButtonLoading($btn, false);
    });
});

if (isEdit && recordId) {
    AdminApp.request(ADMIN_BASE + '/api/users/' + recordId, 'GET').done(function (res) {
        var r = res.data;
        $('#f-name').val(r.name);
        $('#f-email').val(r.email);
        $('#f-role').val(r.role_id);
        $('#f-status').val(r.status);
        $('#f-password').val('');
    });
}
</script>
<?= $this->endSection() ?>
