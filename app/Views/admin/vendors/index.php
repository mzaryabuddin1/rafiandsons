<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
    <div class="col-lg-8"><h2>Vendors</h2><ol class="breadcrumb"><li>Review applications and manage vendor accounts</li></ol></div>
</div>

<div class="ibox">
    <div class="ibox-title"><h5>Vendor Applications</h5></div>
    <div class="ibox-content">
        <div class="row m-b-sm">
            <div class="col-md-4"><input type="text" id="search" class="form-control" placeholder="Business / name / email / phone"></div>
            <div class="col-md-3">
                <select id="filter-status" class="form-control">
                    <option value="">All Statuses</option>
                    <?php foreach ($statuses as $k => $v): ?>
                        <option value="<?= esc($k) ?>"><?= esc($v) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-primary" id="btn-filter">Filter</button></div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="data-table">
                <thead>
                <tr>
                    <th>Business</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Applied</th>
                    <th width="180">Actions</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal inmodal" id="detail-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated fadeIn">
            <div class="modal-header navy-bg">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Vendor Details</h4>
            </div>
            <div class="modal-body" id="detail-body"></div>
            <?php if (! empty($canUpdate)): ?>
            <div class="modal-footer" style="display:block;text-align:left;">
                <input type="hidden" id="vendor-id">
                <div class="form-group">
                    <label>Admin Notes</label>
                    <textarea id="vendor-notes" class="form-control" rows="2" placeholder="Optional notes"></textarea>
                </div>
                <button type="button" class="btn btn-success" id="btn-approve"><i class="fa fa-check"></i> Approve</button>
                <button type="button" class="btn btn-warning" id="btn-reject"><i class="fa fa-times"></i> Reject</button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
var canUpdate = <?= ! empty($canUpdate) ? 'true' : 'false' ?>;
var canDelete = <?= ! empty($canDelete) ? 'true' : 'false' ?>;

function loadList() {
    AdminApp.request(ADMIN_BASE + '/api/vendors', 'GET', {
        search: $('#search').val(),
        status: $('#filter-status').val()
    }).done(function (res) {
        var h = '';
        (res.data.items || []).forEach(function (r) {
            var a = '<button class="btn btn-xs btn-primary btn-view" data-id="' + r.id + '"><i class="fa fa-eye"></i></button> ';
            if (canDelete) a += '<button class="btn btn-xs btn-danger btn-delete" data-id="' + r.id + '"><i class="fa fa-trash"></i></button>';
            h += '<tr>'
                + '<td>' + (r.business_name || '') + '</td>'
                + '<td>' + (r.contact_name || '') + '</td>'
                + '<td>' + (r.email || '') + '</td>'
                + '<td>' + (r.phone || '') + '</td>'
                + '<td><span class="badge badge-primary">' + (r.status_label || r.status) + '</span></td>'
                + '<td>' + (r.created_at || '') + '</td>'
                + '<td>' + a + '</td>'
                + '</tr>';
        });
        if (!h) h = '<tr><td colspan="7" class="text-center text-muted">No vendors found</td></tr>';
        $('#data-table tbody').html(h);
    });
}

$('#btn-filter,#search').on('click keyup', function (e) {
    if (e.type === 'keyup' && e.keyCode !== 13 && e.target.id === 'search') return;
    loadList();
});

$(document).on('click', '.btn-view', function () {
    AdminApp.request(ADMIN_BASE + '/api/vendors/' + $(this).data('id'), 'GET').done(function (res) {
        var r = res.data;
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
        if (canUpdate) {
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
        $('#detail-modal').modal('show');
    });
});

$('#btn-approve').on('click', function () {
    var id = $('#vendor-id').val();
    AdminApp.request(ADMIN_BASE + '/api/vendors/' + id + '/approve', 'POST', {
        admin_notes: $('#vendor-notes').val()
    }).done(function (res) {
        AdminApp.toast('success', res.message);
        $('#detail-modal').modal('hide');
        loadList();
    });
});

$('#btn-reject').on('click', function () {
    var id = $('#vendor-id').val();
    AdminApp.request(ADMIN_BASE + '/api/vendors/' + id + '/reject', 'POST', {
        admin_notes: $('#vendor-notes').val()
    }).done(function (res) {
        AdminApp.toast('success', res.message);
        $('#detail-modal').modal('hide');
        loadList();
    });
});

$(document).on('click', '.btn-delete', function () {
    var id = $(this).data('id');
    AdminApp.confirmDelete(function () {
        AdminApp.request(ADMIN_BASE + '/api/vendors/' + id + '/delete', 'POST').done(function (res) {
            AdminApp.toast('success', res.message);
            loadList();
        });
    }, 'Archive this vendor?');
});

$(loadList);
</script>
<?= $this->endSection() ?>
