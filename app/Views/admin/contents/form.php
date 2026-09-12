<?= $this->extend('admin/layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('admintheme/css/plugins/summernote/summernote-bs4.css') ?>" rel="stylesheet">
<style>
    .note-editor.note-frame {
        border: 1px solid #e5e6e7;
        border-radius: 2px;
    }
    .note-editor .note-editing-area .note-editable {
        min-height: 360px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $isEdit = ! empty($isEdit); ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
    <div class="col-lg-8">
        <h2><?= $isEdit ? 'Edit Content' : 'Add Content' ?></h2>
    </div>
    <div class="col-lg-4 text-right">
        <a href="<?= site_url('admin/contents') ?>" class="btn btn-white"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
</div>

<div class="ibox">
    <div class="ibox-title">
        <h5><?= $isEdit ? 'Edit Content' : 'New Content' ?></h5>
    </div>
    <div class="ibox-content">
        <form id="main-form">
            <input type="hidden" name="id" id="record-id" value="<?= $isEdit ? (int) $recordId : '' ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Title *</label>
                        <input class="form-control" name="title" id="f-title" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Slug</label>
                        <input class="form-control" name="slug" id="f-slug">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Body</label>
                <textarea class="form-control" name="body" id="f-body" rows="8"></textarea>
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
                <a href="<?= site_url('admin/contents') ?>" class="btn btn-white">Cancel</a>
                <button type="submit" class="btn btn-primary" id="save-btn">Save</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('admintheme/js/plugins/summernote/summernote-bs4.js') ?>"></script>
<script>
var isEdit = <?= $isEdit ? 'true' : 'false' ?>;
var recordId = <?= $isEdit ? (int) $recordId : 'null' ?>;

function initBodyEditor(html) {
    var $body = $('#f-body');
    if ($body.next('.note-editor').length) {
        $body.summernote('destroy');
    }
    $body.summernote({
        height: Math.max(360, window.innerHeight - 340),
        dialogsInBody: true,
        placeholder: 'Write page content…',
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'hr']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });
    $body.summernote('code', html || '');
}

$('#main-form').on('submit', function (e) {
    e.preventDefault();
    if ($('#f-body').next('.note-editor').length) {
        $('#f-body').val($('#f-body').summernote('code'));
    }
    var url = isEdit ? ADMIN_BASE + '/api/contents/' + recordId : ADMIN_BASE + '/api/contents';
    var $btn = $('#save-btn');
    AdminApp.setButtonLoading($btn, true);
    AdminApp.request(url, 'POST', $(this).serialize()).done(function (res) {
        AdminApp.toast('success', res.message);
        window.location.href = ADMIN_BASE + '/contents';
    }).always(function () {
        AdminApp.setButtonLoading($btn, false);
    });
});

if (isEdit && recordId) {
    AdminApp.request(ADMIN_BASE + '/api/contents/' + recordId, 'GET').done(function (res) {
        var r = res.data;
        $('#f-title').val(r.title);
        $('#f-slug').val(r.slug);
        $('#f-status').val(r.status);
        initBodyEditor(r.body || '');
    });
} else {
    initBodyEditor('');
}
</script>
<?= $this->endSection() ?>
