<?= $this->extend('vendor_panel/layout') ?>
<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
    <div class="col-lg-12"><h2>Orders</h2><ol class="breadcrumb"><li>Orders that include your products</li></ol></div>
</div>

<div class="ibox">
    <div class="ibox-title"><h5>My Orders</h5></div>
    <div class="ibox-content">
        <div class="row m-b-sm">
            <div class="col-md-4"><input type="text" id="search" class="form-control" placeholder="Order # / name / phone"></div>
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
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Items</th>
                    <th>Your Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th width="80">Actions</th>
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
                <h4 class="modal-title">Order Details</h4>
            </div>
            <div class="modal-body" id="detail-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function loadList() {
    AdminApp.request(VENDOR_BASE + '/api/orders', 'GET', {
        search: $('#search').val(),
        status: $('#filter-status').val()
    }).done(function (res) {
        var h = '';
        (res.data.items || []).forEach(function (r) {
            h += '<tr>'
                + '<td>' + r.order_number + '</td>'
                + '<td>' + r.customer_name + '</td>'
                + '<td>' + r.customer_phone + '</td>'
                + '<td>' + r.item_count + '</td>'
                + '<td>PKR ' + Number(r.vendor_total || 0).toLocaleString() + '</td>'
                + '<td><span class="badge badge-primary">' + (r.status_label || r.status) + '</span></td>'
                + '<td>' + (r.created_at || '') + '</td>'
                + '<td><button class="btn btn-xs btn-primary btn-view" data-id="' + r.id + '"><i class="fa fa-eye"></i></button></td>'
                + '</tr>';
        });
        if (!h) h = '<tr><td colspan="8" class="text-center text-muted">No orders found</td></tr>';
        $('#data-table tbody').html(h);
    });
}

$('#btn-filter,#search').on('click keyup', function (e) {
    if (e.type === 'keyup' && e.keyCode !== 13 && e.target.id === 'search') return;
    loadList();
});

$(document).on('click', '.btn-view', function () {
    AdminApp.request(VENDOR_BASE + '/api/orders/' + $(this).data('id'), 'GET').done(function (res) {
        var r = res.data;
        var h = '<p><strong>' + r.order_number + '</strong> — ' + r.status_label + '</p>';
        h += '<p>' + r.customer_name + ' | ' + r.customer_phone + '</p>';
        h += '<p>' + (r.customer_address || '') + ' ' + (r.customer_city || '') + '</p>';
        h += '<table class="table table-bordered"><thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead><tbody>';
        (r.items || []).forEach(function (i) {
            h += '<tr><td>' + i.product_name + '</td><td>' + i.quantity + '</td><td>' + i.unit_price + '</td><td>' + i.line_total + '</td></tr>';
        });
        h += '</tbody></table>';
        h += '<p><strong>Your total:</strong> PKR ' + Number(r.vendor_total || 0).toLocaleString() + '</p>';
        $('#detail-body').html(h);
        $('#detail-modal').modal('show');
    });
});

$(loadList);
</script>
<?= $this->endSection() ?>
