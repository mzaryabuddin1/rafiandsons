<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
    <div class="col-lg-8"><h2>Vendor Details</h2><ol class="breadcrumb"><li><a href="<?= admin_url('vendors') ?>">Vendors</a></li><li class="active">Detail</li></ol></div>
    <div class="col-lg-4 text-right"><a href="<?= admin_url('vendors') ?>" class="btn btn-white"><i class="fa fa-arrow-left"></i> Back to Vendors</a></div>
</div>

<div class="ibox">
    <div class="ibox-title"><h5>Vendor Application</h5></div>
    <div class="ibox-content">
        <div id="detail-body"><p class="text-muted">Loading…</p></div>
        <?php if (! empty($canUpdate)): ?>
        <hr>
        <input type="hidden" id="vendor-id">
        <div class="form-group">
            <label>Admin Notes</label>
            <textarea id="vendor-notes" class="form-control" rows="2" placeholder="Optional notes"></textarea>
        </div>
        <button type="button" class="btn btn-success" id="btn-approve"><i class="fa fa-check"></i> Approve</button>
        <button type="button" class="btn btn-warning" id="btn-reject"><i class="fa fa-times"></i> Reject</button>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
var canUpdate = <?= ! empty($canUpdate) ? 'true' : 'false' ?>;
var recordId = <?= (int) ($recordId ?? 0) ?>;

function applyActionButtons(r) {
    if (!canUpdate) return;
    $('#btn-approve,#btn-reject').toggle(r.status !== 'approved');
    if (r.status === 'approved') $('#btn-reject').hide();
    if (r.status === 'rejected') {
        $('#btn-approve').show();
        $('#btn-reject').hide();
    }
    if (r.status === 'pending') {
        $('#btn-approve,#btn-reject').show();
    }
}

function renderDetail(r) {
    var h = '<p><strong>' + (r.business_name || '') + '</strong> — ' + (r.status_label || r.status) + '</p>';
    h += '<p>' + (r.contact_name || '') + ' | ' + (r.email || '') + ' | ' + (r.phone || '') + '</p>';
    h += '<p>' + (r.address || '') + ' ' + (r.city || '') + '</p>';
    h += '<p>CNIC: ' + (r.cnic || '-') + '</p>';
    h += '<p>Notes: ' + (r.notes || '-') + '</p>';
    h += '<p>Admin notes: ' + (r.admin_notes || '-') + '</p>';
    h += '<p>Reviewed: ' + (r.reviewed_at || '-') + '</p>';
    $('#detail-body').html(h);
    $('#vendor-id').val(r.id);
    $('#vendor-notes').val(r.admin_notes || '');
    applyActionButtons(r);
}

function loadVendor() {
    AdminApp.request(ADMIN_BASE + '/api/vendors/' + recordId, 'GET').done(function (res) {
        renderDetail(res.data);
    }).fail(function () {
        $('#detail-body').html('<p class="text-danger">Vendor not found.</p>');
        $('#btn-approve,#btn-reject').hide();
    });
}

$('#btn-approve').on('click', function () {
    var id = $('#vendor-id').val();
    AdminApp.request(ADMIN_BASE + '/api/vendors/' + id + '/approve', 'POST', {
        admin_notes: $('#vendor-notes').val()
    }).done(function (res) {
        AdminApp.toast('success', res.message);
        window.location.href = ADMIN_BASE + '/vendors';
    });
});

$('#btn-reject').on('click', function () {
    var id = $('#vendor-id').val();
    AdminApp.request(ADMIN_BASE + '/api/vendors/' + id + '/reject', 'POST', {
        admin_notes: $('#vendor-notes').val()
    }).done(function (res) {
        AdminApp.toast('success', res.message);
        window.location.href = ADMIN_BASE + '/vendors';
    });
});

loadVendor();
</script>
<?= $this->endSection() ?>
