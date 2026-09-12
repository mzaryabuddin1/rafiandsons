<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<?php $isEdit = ! empty($isEdit); ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
    <div class="col-lg-8">
        <h2><?= $isEdit ? 'Edit Role' : 'Add Role' ?></h2>
    </div>
    <div class="col-lg-4 text-right">
        <a href="<?= admin_url('roles') ?>" class="btn btn-white"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
</div>

<div class="ibox">
    <div class="ibox-title">
        <h5><?= $isEdit ? 'Edit Role' : 'New Role' ?></h5>
    </div>
    <div class="ibox-content">
        <form id="main-form">
            <input type="hidden" name="id" id="record-id" value="<?= $isEdit ? (int) $recordId : '' ?>">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Name *</label>
                        <input class="form-control" name="name" id="f-name" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status" id="f-status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Permissions</label>
                <div id="permissions-box" style="max-height:420px;overflow:auto;border:1px solid #e7eaec;padding:12px;"></div>
            </div>
            <div class="hr-line-dashed"></div>
            <div class="text-right">
                <a href="<?= admin_url('roles') ?>" class="btn btn-white">Cancel</a>
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
var groupedPermissions = {};

function renderPermissions(selected) {
    selected = selected || [];
    var html = '';
    Object.keys(groupedPermissions).forEach(function (mod) {
        html += '<div class="m-b-sm"><strong>' + mod + '</strong><div>';
        groupedPermissions[mod].forEach(function (p) {
            var checked = selected.indexOf(parseInt(p.id, 10)) > -1 ? ' checked' : '';
            html += '<label class="checkbox-inline m-r-sm"><input type="checkbox" name="permission_ids[]" value="' + p.id + '"' + checked + '> ' + p.action + '</label>';
        });
        html += '</div></div>';
    });
    $('#permissions-box').html(html);
}

$('#main-form').on('submit', function (e) {
    e.preventDefault();
    var url = isEdit ? ADMIN_BASE + '/api/roles/' + recordId : ADMIN_BASE + '/api/roles';
    var $btn = $('#save-btn');
    AdminApp.setButtonLoading($btn, true);
    AdminApp.request(url, 'POST', $(this).serialize()).done(function (res) {
        AdminApp.toast('success', res.message);
        window.location.href = ADMIN_BASE + '/roles';
    }).always(function () {
        AdminApp.setButtonLoading($btn, false);
    });
});

AdminApp.request(ADMIN_BASE + '/api/permissions', 'GET').done(function (res) {
    groupedPermissions = res.data.grouped || {};
    if (isEdit && recordId) {
        AdminApp.request(ADMIN_BASE + '/api/roles/' + recordId, 'GET').done(function (roleRes) {
            var r = roleRes.data;
            $('#f-name').val(r.name);
            $('#f-status').val(r.status);
            renderPermissions(r.permission_ids || []);
        });
    } else {
        renderPermissions([]);
    }
});
</script>
<?= $this->endSection() ?>
