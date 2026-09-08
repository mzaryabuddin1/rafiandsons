<?= $this->extend('admin/layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('admintheme/css/plugins/summernote/summernote-bs4.css') ?>" rel="stylesheet">
<style>
    #form-modal .modal-dialog {
        width: 100%;
        max-width: 100%;
        height: 100%;
        margin: 0;
    }
    #form-modal .modal-content {
        height: 100vh;
        border-radius: 0;
        display: flex;
        flex-direction: column;
    }
    #form-modal #main-form {
        display: flex;
        flex-direction: column;
        height: 100%;
        margin: 0;
    }
    #form-modal .modal-body {
        flex: 1 1 auto;
        overflow-y: auto;
    }
    #form-modal .modal-header,
    #form-modal .modal-footer {
        flex: 0 0 auto;
    }
    .note-editor.note-frame {
        border: 1px solid #e5e6e7;
        border-radius: 2px;
    }
    .note-editor .note-editing-area .note-editable {
        min-height: calc(100vh - 340px);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
    <div class="col-lg-8"><h2>Website Contents</h2></div>
    <div class="col-lg-4 text-right">
        <?php if (! empty($canCreate)): ?>
            <button class="btn btn-primary" id="btn-add"><i class="fa fa-plus"></i> Add Content</button>
        <?php endif; ?>
    </div>
</div>

<div class="ibox">
    <div class="ibox-title">
        <h5>Pages / Sections</h5>
        <div class="ibox-tools">
            <input type="text" id="search" class="form-control form-control-sm" placeholder="Search..." style="width:220px;display:inline-block;margin-right:8px;">
            <button type="button" class="btn btn-sm btn-white" id="btn-export-csv"><i class="fa fa-download"></i> Export CSV</button>
        </div>
    </div>
    <div class="ibox-content">
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="data-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th width="140">Actions</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="row admin-table-footer">
          <div class="col-sm-6"><div class="admin-table-info text-muted" id="table-info"></div></div>
          <div class="col-sm-6 text-right"><div class="admin-table-pager" id="table-pager"></div></div>
        </div>
    </div>
</div>

<div class="modal inmodal" id="form-modal" tabindex="-1">
    <div class="modal-dialog modal-full">
        <div class="modal-content animated fadeIn">
            <form id="main-form">
                <div class="modal-header navy-bg">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title" id="modal-title">Add Content</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="record-id">
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="save-btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('admintheme/js/plugins/summernote/summernote-bs4.js') ?>"></script>
<script>
var canUpdate = <?= ! empty($canUpdate) ? 'true' : 'false' ?>;
var canDelete = <?= ! empty($canDelete) ? 'true' : 'false' ?>;

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

function destroyBodyEditor() {
    var $body = $('#f-body');
    if ($body.next('.note-editor').length) {
        $body.summernote('destroy');
    }
}

var table = AdminApp.createDataTable({
    url: ADMIN_BASE + '/api/contents',
    $tbody: $('#data-table tbody'),
    $pager: $('#table-pager'),
    $info: $('#table-info'),
    $search: $('#search'),
    emptyCols: 5,
    emptyText: 'No content found',
    filters: function () { return {}; },
    renderRow: function (r) {
        var a = '';
        if (canUpdate) a += '<button class="btn btn-xs btn-primary btn-edit" data-id="' + r.id + '"><i class="fa fa-pencil"></i></button> ';
        if (canDelete) a += '<button class="btn btn-xs btn-danger btn-delete" data-id="' + r.id + '"><i class="fa fa-trash"></i></button>';
        return '<tr><td>' + r.id + '</td><td>' + r.title + '</td><td>' + r.slug + '</td><td>' + (r.status == 1 ? 'Active' : 'Inactive') + '</td><td>' + a + '</td></tr>';
    }
});
$('#btn-export-csv').on('click', function () { table.exportCsv(); });

$('#btn-add').on('click', function () {
    $('#main-form')[0].reset();
    $('#record-id').val('');
    $('#modal-title').text('Add Content');
    $('#form-modal').modal('show');
    initBodyEditor('');
});

$('#main-form').on('submit', function (e) {
    e.preventDefault();
    if ($('#f-body').next('.note-editor').length) {
        $('#f-body').val($('#f-body').summernote('code'));
    }
    var id = $('#record-id').val();
    var url = id ? ADMIN_BASE + '/api/contents/' + id : ADMIN_BASE + '/api/contents';
    var $btn = $('#save-btn');
    AdminApp.setButtonLoading($btn, true);
    AdminApp.request(url, 'POST', $(this).serialize()).done(function (res) {
        AdminApp.toast('success', res.message);
        $('#form-modal').modal('hide');
        table.load(true);
    }).always(function () {
        AdminApp.setButtonLoading($btn, false);
    });
});

$(document).on('click', '.btn-edit', function () {
    AdminApp.request(ADMIN_BASE + '/api/contents/' + $(this).data('id'), 'GET').done(function (res) {
        var r = res.data;
        $('#record-id').val(r.id);
        $('#f-title').val(r.title);
        $('#f-slug').val(r.slug);
        $('#f-status').val(r.status);
        $('#modal-title').text('Edit Content');
        $('#form-modal').modal('show');
        initBodyEditor(r.body || '');
    });
});

$(document).on('click', '.btn-delete', function () {
    var id = $(this).data('id');
    AdminApp.confirmDelete(function () {
        AdminApp.request(ADMIN_BASE + '/api/contents/' + id + '/delete', 'POST').done(function (res) {
            AdminApp.toast('success', res.message);
            table.load(true);
        });
    });
});

$('#form-modal').on('hidden.bs.modal', function () {
    destroyBodyEditor();
});

table.load(true);
</script>
<?= $this->endSection() ?>
