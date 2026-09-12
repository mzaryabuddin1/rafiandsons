<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
    <div class="col-lg-8"><h2>Products</h2></div>
    <div class="col-lg-4 text-right"><?php if (!empty($canCreate)): ?><a href="<?= admin_url('products/create') ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Add Product</a><?php endif; ?></div>
</div>
<div class="ibox"><div class="ibox-title"><h5>Product List</h5><div class="ibox-tools">
<div class="btn-group m-r-sm">
  <button type="button" class="btn btn-sm btn-primary" id="btn-view-active">Active</button>
  <button type="button" class="btn btn-sm btn-white" id="btn-view-archived">Archived</button>
</div>
<input type="text" id="search" class="form-control form-control-sm" placeholder="Search..." style="width:220px;display:inline-block;margin-right:8px;">
<button type="button" class="btn btn-sm btn-white" id="btn-export-csv"><i class="fa fa-download"></i> Export CSV</button>
</div></div>
<div class="ibox-content"><div class="table-responsive"><table class="table table-striped table-bordered" id="data-table"><thead><tr><th>ID</th><th>Image</th><th>Name</th><th>SKU</th><th>Category</th><th>Vendor</th><th>Price</th><th>Payment</th><th>Stock</th><th>Status</th><th width="140">Actions</th></tr></thead><tbody></tbody></table></div>
<div class="row admin-table-footer">
  <div class="col-sm-6"><div class="admin-table-info text-muted" id="table-info"></div></div>
  <div class="col-sm-6 text-right"><div class="admin-table-pager" id="table-pager"></div></div>
</div>
</div></div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
var canUpdate=<?= !empty($canUpdate)?'true':'false' ?>, canDelete=<?= !empty($canDelete)?'true':'false' ?>;
var showArchived = false;

function paymentLabel(r){
    var cash=parseInt(r.cash_available,10)===1;
    var inst=parseInt(r.installment_available,10)===1;
    if(cash && inst) return 'Cash + Installment';
    if(cash) return 'Cash only';
    if(inst) return 'Installment only';
    return '-';
}

function setArchivedView(archived) {
    showArchived = !!archived;
    $('#btn-view-active').toggleClass('btn-primary', !showArchived).toggleClass('btn-white', showArchived);
    $('#btn-view-archived').toggleClass('btn-primary', showArchived).toggleClass('btn-white', !showArchived);
    table.load(true);
}

var table = AdminApp.createDataTable({
    url: ADMIN_BASE + '/api/products',
    $tbody: $('#data-table tbody'),
    $pager: $('#table-pager'),
    $info: $('#table-info'),
    $search: $('#search'),
    emptyCols: 11,
    emptyText: function () { return showArchived ? 'No archived products' : 'No products found'; },
    filters: function () { return { archived: showArchived ? 1 : 0 }; },
    renderRow: function (r) {
        var a = '';
        if (!showArchived && canUpdate) a += '<a href="' + ADMIN_BASE + '/products/' + r.id + '/edit" class="btn btn-xs btn-primary" title="Edit"><i class="fa fa-pencil"></i></a> ';
        if (canDelete) {
            if (showArchived) a += '<button class="btn btn-xs btn-success btn-restore" data-id="' + r.id + '" title="Restore"><i class="fa fa-undo"></i></button>';
            else a += '<button class="btn btn-xs btn-warning btn-archive" data-id="' + r.id + '" title="Archive"><i class="fa fa-archive"></i></button>';
        }
        var price = r.price;
        if (r.compare_price && parseFloat(r.compare_price) > parseFloat(r.price)) {
            price = '<s class="text-muted">' + r.compare_price + '</s> ' + r.price;
        }
        var thumb = '-';
        try {
            var imgs = typeof r.images === 'string' ? JSON.parse(r.images || '[]') : (r.images || []);
            if (imgs && imgs.length && imgs[0]) {
                thumb = '<img src="' + BASE_URL + imgs[0] + '" alt="" style="height:40px;width:40px;object-fit:cover;border-radius:4px;">';
            }
        } catch (e) {}
        return '<tr><td>' + r.id + '</td><td>' + thumb + '</td><td>' + r.name + '</td><td>' + (r.sku || '') + '</td><td>' + (r.category_name || '-') + '</td><td>' + (r.vendor_name || '-') + '</td><td>' + price + '</td><td>' + paymentLabel(r) + '</td><td>' + r.stock_status + '</td><td>' + (r.status == 1 ? 'Active' : 'Inactive') + '</td><td>' + a + '</td></tr>';
    }
});
$('#btn-export-csv').on('click', function () { table.exportCsv(); });
$('#btn-view-active').on('click', function () { setArchivedView(false); });
$('#btn-view-archived').on('click', function () { setArchivedView(true); });
$(document).on('click','.btn-archive',function(){var id=$(this).data('id');AdminApp.confirmArchive(function(){AdminApp.request(ADMIN_BASE+'/api/products/'+id+'/delete','POST').done(function(res){AdminApp.toast('success',res.message);table.load(true);});});});
$(document).on('click','.btn-restore',function(){var id=$(this).data('id');AdminApp.confirmRestore(function(){AdminApp.request(ADMIN_BASE+'/api/products/'+id+'/restore','POST').done(function(res){AdminApp.toast('success',res.message);table.load(true);});});});
table.load(true);
</script>
<?= $this->endSection() ?>
