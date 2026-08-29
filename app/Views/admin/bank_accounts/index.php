<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
    <div class="col-lg-8">
        <h2>Bank Accounts</h2>
        <ol class="breadcrumb"><li>Accounts shown on checkout for customer payments</li></ol>
    </div>
    <div class="col-lg-4 text-right" style="padding-top:20px;">
        <?php if (! empty($canCreate)): ?>
            <button class="btn btn-primary" id="btn-add"><i class="fa fa-plus"></i> Add Account</button>
        <?php endif; ?>
    </div>
</div>

<div class="ibox">
    <div class="ibox-title"><h5>Payment Accounts</h5></div>
    <div class="ibox-content">
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="data-table">
                <thead>
                <tr>
                    <th width="70">Logo</th>
                    <th>Bank</th>
                    <th>Account Title</th>
                    <th>Account Number</th>
                    <th>IBAN</th>
                    <th>Branch</th>
                    <th>Sort</th>
                    <th>Status</th>
                    <th width="120">Actions</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal inmodal" id="form-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content animated fadeIn">
            <form id="main-form" enctype="multipart/form-data">
                <div class="modal-header navy-bg">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title" id="modal-title">Add Bank Account</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="record-id">
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
<script>
var canUpdate = <?= ! empty($canUpdate) ? 'true' : 'false' ?>;
var canDelete = <?= ! empty($canDelete) ? 'true' : 'false' ?>;

function setLogoPreview(url) {
    if (url) {
        $('#logo-preview').attr('src', url);
        $('#logo-preview-wrap').show();
    } else {
        $('#logo-preview').attr('src', '');
        $('#logo-preview-wrap').hide();
    }
}

function loadList() {
    AdminApp.request(ADMIN_BASE + '/api/bank-accounts', 'GET').done(function (res) {
        var h = '';
        (res.data.items || []).forEach(function (r) {
            var a = '';
            if (canUpdate) a += '<button class="btn btn-xs btn-primary btn-edit" data-id="' + r.id + '"><i class="fa fa-pencil"></i></button> ';
            if (canDelete) a += '<button class="btn btn-xs btn-danger btn-delete" data-id="' + r.id + '"><i class="fa fa-trash"></i></button>';
            var logo = r.logo_url
                ? '<img src="' + r.logo_url + '" alt="" style="height:32px;max-width:70px;object-fit:contain;">'
                : '<span class="text-muted">-</span>';
            h += '<tr>'
                + '<td>' + logo + '</td>'
                + '<td>' + (r.bank_name || '') + '</td>'
                + '<td>' + (r.account_title || '') + '</td>'
                + '<td>' + (r.account_number || '') + '</td>'
                + '<td>' + (r.iban || '-') + '</td>'
                + '<td>' + (r.branch || '-') + '</td>'
                + '<td>' + r.sort_order + '</td>'
                + '<td>' + (parseInt(r.status, 10) === 1 ? '<span class="badge badge-primary">Active</span>' : '<span class="badge">Inactive</span>') + '</td>'
                + '<td>' + a + '</td>'
                + '</tr>';
        });
        if (!h) h = '<tr><td colspan="9" class="text-center text-muted">No bank accounts yet</td></tr>';
        $('#data-table tbody').html(h);
    });
}

$('#btn-add').on('click', function () {
    $('#main-form')[0].reset();
    $('#record-id').val('');
    $('#f-status').val('1');
    setLogoPreview(null);
    $('#modal-title').text('Add Bank Account');
    $('#form-modal').modal('show');
});

$('#f-logo').on('change', function () {
    var file = this.files && this.files[0];
    if (!file) return;
    setLogoPreview(URL.createObjectURL(file));
});

$(document).on('click', '.btn-edit', function () {
    AdminApp.request(ADMIN_BASE + '/api/bank-accounts/' + $(this).data('id'), 'GET').done(function (res) {
        var r = res.data;
        $('#main-form')[0].reset();
        $('#record-id').val(r.id);
        $('#f-bank').val(r.bank_name);
        $('#f-title').val(r.account_title);
        $('#f-number').val(r.account_number);
        $('#f-iban').val(r.iban || '');
        $('#f-branch').val(r.branch || '');
        $('#f-sort').val(r.sort_order);
        $('#f-status').val(String(r.status));
        setLogoPreview(r.logo_url || null);
        $('#modal-title').text('Edit Bank Account');
        $('#form-modal').modal('show');
    });
});

$('#main-form').on('submit', function (e) {
    e.preventDefault();
    var id = $('#record-id').val();
    var url = id ? ADMIN_BASE + '/api/bank-accounts/' + id : ADMIN_BASE + '/api/bank-accounts';
    var $btn = $('#save-btn');
    var formData = new FormData(this);
    AdminApp.setButtonLoading($btn, true);
    AdminApp.request(url, 'POST', formData).done(function (res) {
        AdminApp.toast('success', res.message);
        $('#form-modal').modal('hide');
        loadList();
    }).always(function () {
        AdminApp.setButtonLoading($btn, false);
    });
});

$(document).on('click', '.btn-delete', function () {
    var id = $(this).data('id');
    AdminApp.confirmDelete(function () {
        AdminApp.request(ADMIN_BASE + '/api/bank-accounts/' + id + '/delete', 'POST').done(function (res) {
            AdminApp.toast('success', res.message);
            loadList();
        });
    }, 'Delete this bank account?');
});

$(loadList);
</script>
<?= $this->endSection() ?>
