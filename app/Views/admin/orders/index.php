<?= $this->extend('admin/layout') ?>
<?= $this->section('styles') ?>
<style>
.order-status-badge {
    display: inline-block;
    padding: 4px 8px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 3px;
    color: #fff;
    white-space: nowrap;
}
.order-status-new { background: #23c6c8; }
.order-status-under_review { background: #f8ac59; }
.order-status-customer_contacted { background: #1c84c6; }
.order-status-approved { background: #1ab394; }
.order-status-rejected { background: #ed5565; }
.order-status-processing { background: #7a5af8; }
.order-status-completed { background: #1a7f37; }
.order-status-cancelled { background: #676a6c; }
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
<div class="col-lg-8"><h2>Orders</h2></div>
<div class="col-lg-4 text-right"><?php if (!empty($canCreate)): ?><a class="btn btn-primary" href="<?= admin_url('orders/create') ?>"><i class="fa fa-plus"></i> Create Order</a><?php endif; ?></div>
</div>
<div class="ibox"><div class="ibox-title"><h5>Order Bookings</h5><div class="ibox-tools">
<div class="btn-group m-r-sm">
  <button type="button" class="btn btn-sm btn-primary" id="btn-view-active">Active</button>
  <button type="button" class="btn btn-sm btn-white" id="btn-view-archived">Archived</button>
</div>
<button type="button" class="btn btn-sm btn-white" id="btn-export-csv"><i class="fa fa-download"></i> Export CSV</button>
</div></div>
<div class="ibox-content">
<div class="row m-b-sm">
<div class="col-md-3"><input type="text" id="search" class="form-control" placeholder="Order # / name / phone"></div>
<div class="col-md-2"><select id="filter-status" class="form-control"><option value="">All Statuses</option><?php foreach ($statuses as $k=>$v): ?><option value="<?= esc($k) ?>"><?= esc($v) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><input type="date" id="date-from" class="form-control"></div>
<div class="col-md-2"><input type="date" id="date-to" class="form-control"></div>
<div class="col-md-2"><button class="btn btn-primary" id="btn-filter">Filter</button></div>
</div>
<div class="table-responsive"><table class="table table-striped table-bordered" id="data-table"><thead><tr><th>Order #</th><th>Customer</th><th>Phone</th><th>Vendor</th><th>Plan</th><th>Total</th><th>Status</th><th>Payment</th><th>Date</th><th width="140">Actions</th></tr></thead><tbody></tbody></table></div>
<div class="row admin-table-footer">
  <div class="col-sm-6"><div class="admin-table-info text-muted" id="table-info"></div></div>
  <div class="col-sm-6 text-right"><div class="admin-table-pager" id="table-pager"></div></div>
</div>
</div></div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
var canDelete=<?= !empty($canDelete)?'true':'false' ?>;
var showArchived = false;

function statusBadge(r){
    var status = String(r.status || '').toLowerCase();
    var label = r.status_label || r.status || '-';
    var cls = 'order-status-badge order-status-' + status.replace(/[^a-z0-9_]/g, '');
    return '<span class="' + cls + '">' + label + '</span>';
}

function paymentBadge(r){
    if(parseInt(r.payment_verified,10)===1){
        return '<span class="badge badge-primary">Verified</span>';
    }
    if(r.receipt_image || r.receipt_url){
        return '<span class="badge badge-warning">Receipt Uploaded</span>';
    }
    return '<span class="badge">No Receipt</span>';
}

function setArchivedView(archived) {
    showArchived = !!archived;
    $('#btn-view-active').toggleClass('btn-primary', !showArchived).toggleClass('btn-white', showArchived);
    $('#btn-view-archived').toggleClass('btn-primary', showArchived).toggleClass('btn-white', !showArchived);
    table.load(true);
}

var table = AdminApp.createDataTable({
    url: ADMIN_BASE + '/api/orders',
    $tbody: $('#data-table tbody'),
    $pager: $('#table-pager'),
    $info: $('#table-info'),
    $search: $('#search'),
    emptyCols: 10,
    emptyText: function () { return showArchived ? 'No archived orders' : 'No orders found'; },
    filters: function () {
        return {
            status: $('#filter-status').val(),
            date_from: $('#date-from').val(),
            date_to: $('#date-to').val(),
            archived: showArchived ? 1 : 0
        };
    },
    renderRow: function (r) {
        var a = '';
        if (!showArchived) a += '<a class="btn btn-xs btn-primary" href="' + ADMIN_BASE + '/orders/' + r.id + '" title="View"><i class="fa fa-eye"></i></a> ';
        if (canDelete) {
            if (showArchived) a += '<button class="btn btn-xs btn-success btn-restore" data-id="' + r.id + '" title="Restore"><i class="fa fa-undo"></i></button>';
            else a += '<button class="btn btn-xs btn-warning btn-archive" data-id="' + r.id + '" title="Archive"><i class="fa fa-archive"></i></button>';
        }
        return '<tr><td>' + r.order_number + '</td><td>' + r.customer_name + '</td><td>' + r.customer_phone + '</td><td>' + (r.vendor_label || '-') + '</td><td>' + (r.plan_name || '-') + '</td><td>' + r.total_payable + '</td><td>' + statusBadge(r) + '</td><td>' + paymentBadge(r) + '</td><td>' + (r.created_at || '') + '</td><td>' + a + '</td></tr>';
    }
});
$('#btn-export-csv').on('click', function () { table.exportCsv(); });
$('#btn-filter').on('click', function () { table.load(true); });
$('#btn-view-active').on('click', function () { setArchivedView(false); });
$('#btn-view-archived').on('click', function () { setArchivedView(true); });
$(document).on('click','.btn-archive',function(){var id=$(this).data('id');AdminApp.confirmArchive(function(){AdminApp.request(ADMIN_BASE+'/api/orders/'+id+'/delete','POST').done(function(res){AdminApp.toast('success',res.message);table.load(true);});},'Archive this order?');});
$(document).on('click','.btn-restore',function(){var id=$(this).data('id');AdminApp.confirmRestore(function(){AdminApp.request(ADMIN_BASE+'/api/orders/'+id+'/restore','POST').done(function(res){AdminApp.toast('success',res.message);table.load(true);});});});
table.load(true);
</script>
<?= $this->endSection() ?>
