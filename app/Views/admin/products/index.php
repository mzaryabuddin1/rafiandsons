<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
    <div class="col-lg-8"><h2>Products</h2></div>
    <div class="col-lg-4 text-right"><?php if (!empty($canCreate)): ?><button class="btn btn-primary" id="btn-add"><i class="fa fa-plus"></i> Add Product</button><?php endif; ?></div>
</div>
<div class="ibox"><div class="ibox-title"><h5>Product List</h5><div class="ibox-tools"><input type="text" id="search" class="form-control form-control-sm" placeholder="Search..." style="width:220px;display:inline-block;"></div></div>
<div class="ibox-content"><div class="table-responsive"><table class="table table-striped table-bordered" id="data-table"><thead><tr><th>ID</th><th>Name</th><th>SKU</th><th>Category</th><th>Vendor</th><th>Price</th><th>Payment</th><th>Stock</th><th>Status</th><th width="140">Actions</th></tr></thead><tbody></tbody></table></div></div></div>

<div class="modal inmodal" id="form-modal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content animated fadeIn">
<form id="main-form" enctype="multipart/form-data">
<div class="modal-header navy-bg"><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button><h4 class="modal-title" id="modal-title">Add Product</h4></div>
<div class="modal-body">
<input type="hidden" id="record-id">
<div class="row">
<div class="col-md-6"><div class="form-group"><label>Name *</label><input class="form-control" name="name" id="f-name" required></div></div>
<div class="col-md-3"><div class="form-group"><label>SKU</label><input class="form-control" name="sku" id="f-sku"></div></div>
<div class="col-md-3"><div class="form-group"><label>Price (Cash) *</label><input type="number" step="0.01" class="form-control" name="price" id="f-price" value="0" required></div></div>
<div class="col-md-3"><div class="form-group"><label>Compare Price</label><input type="number" step="0.01" class="form-control" name="compare_price" id="f-compare-price" placeholder="Original / MRP"></div></div>
</div>
<div class="row">
<div class="col-md-4"><div class="form-group"><label>Category</label><select class="form-control" name="category_id" id="f-category"><option value="">Select</option><?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>"><?= esc($c['label']) ?></option><?php endforeach; ?></select></div></div>
<div class="col-md-4"><div class="form-group"><label>Vendor</label><select class="form-control" name="vendor_id" id="f-vendor"><option value="">Own / No vendor</option><?php foreach (($vendors ?? []) as $v): ?><option value="<?= (int) $v['id'] ?>"><?= esc($v['business_name']) ?></option><?php endforeach; ?></select></div></div>
<div class="col-md-4"><div class="form-group"><label>Stock</label><select class="form-control" name="stock_status" id="f-stock"><option value="in_stock">In Stock</option><option value="out_of_stock">Out of Stock</option></select></div></div>
</div>
<div class="row">
<div class="col-md-4"><div class="form-group"><label>Status</label><select class="form-control" name="status" id="f-status"><option value="1">Active</option><option value="0">Inactive</option></select></div></div>
</div>
<div class="form-group"><label>Description</label><textarea class="form-control" name="description" id="f-description" rows="3"></textarea></div>
<div class="form-group"><label>Images</label><input type="file" class="form-control" name="images[]" id="f-images" accept="image/*" multiple></div>
<div class="row">
<div class="col-md-6"><div class="form-group"><label>Cash Purchase</label><select class="form-control" name="cash_available" id="f-cash"><option value="1">Available</option><option value="0">Not Available</option></select></div></div>
<div class="col-md-6"><div class="form-group"><label>Installment</label><select class="form-control" name="installment_available" id="f-installment"><option value="1">Available</option><option value="0">Not Available</option></select></div></div>
</div>

<div class="form-group" id="plans-section">
    <label>Product Installment Plans</label>
    <p class="text-muted" style="margin-top:0;">Each product can have its own plans. Set down payment, monthly installment, and months below.</p>
    <div class="table-responsive">
        <table class="table table-bordered table-sm" id="plans-table">
            <thead>
                <tr>
                    <th>Plan Name</th>
                    <th width="120">Down</th>
                    <th width="120">Monthly</th>
                    <th width="90">Months</th>
                    <th width="50"></th>
                </tr>
            </thead>
            <tbody id="plans-body"></tbody>
        </table>
    </div>
    <button type="button" class="btn btn-xs btn-primary" id="btn-add-plan"><i class="fa fa-plus"></i> Add Plan</button>
</div>

<div class="row"><div class="col-md-6"><div class="form-group"><label>Meta Title</label><input class="form-control" name="meta_title" id="f-meta-title"></div></div>
<div class="col-md-6"><div class="form-group"><label>Meta Description</label><input class="form-control" name="meta_description" id="f-meta-description"></div></div></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-white" data-dismiss="modal">Close</button><button type="submit" class="btn btn-primary" id="save-btn">Save</button></div>
</form></div></div></div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
var canUpdate=<?= !empty($canUpdate)?'true':'false' ?>, canDelete=<?= !empty($canDelete)?'true':'false' ?>;
var planRowIndex=0;

function planRowHtml(plan){
    plan = plan || {};
    var i = planRowIndex++;
    return '<tr class="plan-row">'
        + '<td><input type="hidden" name="plans['+i+'][id]" value="'+(plan.id||'')+'">'
        + '<input class="form-control input-sm" name="plans['+i+'][name]" value="'+(plan.name||'')+'" placeholder="e.g. 12 Month Plan"></td>'
        + '<td><input type="number" step="0.01" class="form-control input-sm" name="plans['+i+'][down_payment]" value="'+(plan.down_payment!=null?plan.down_payment:'')+'" placeholder="0"></td>'
        + '<td><input type="number" step="0.01" class="form-control input-sm" name="plans['+i+'][monthly_installment]" value="'+(plan.monthly_installment!=null?plan.monthly_installment:'')+'" placeholder="0"></td>'
        + '<td><input type="number" class="form-control input-sm" name="plans['+i+'][months]" value="'+(plan.months||12)+'" min="1"></td>'
        + '<td><button type="button" class="btn btn-xs btn-danger btn-remove-plan"><i class="fa fa-times"></i></button></td>'
        + '</tr>';
}

function resetPlans(plans){
    planRowIndex=0;
    $('#plans-body').empty();
    if(plans && plans.length){
        plans.forEach(function(p){ $('#plans-body').append(planRowHtml(p)); });
    } else {
        $('#plans-body').append(planRowHtml({name:'12 Month Plan', months:12}));
    }
}

function paymentLabel(r){
    var cash=parseInt(r.cash_available,10)===1;
    var inst=parseInt(r.installment_available,10)===1;
    if(cash && inst) return 'Cash + Installment';
    if(cash) return 'Cash only';
    if(inst) return 'Installment only';
    return '-';
}

function togglePlansSection(){
    var show=$('#f-installment').val()==='1';
    $('#plans-section').toggle(show);
}

function loadList(){AdminApp.request(ADMIN_BASE+'/api/products','GET',{search:$('#search').val()}).done(function(res){var h='';(res.data.items||[]).forEach(function(r){var a='';if(canUpdate)a+='<button class="btn btn-xs btn-primary btn-edit" data-id="'+r.id+'"><i class="fa fa-pencil"></i></button> ';if(canDelete)a+='<button class="btn btn-xs btn-danger btn-delete" data-id="'+r.id+'"><i class="fa fa-trash"></i></button>';var price=r.price;if(r.compare_price && parseFloat(r.compare_price)>parseFloat(r.price)){price='<s class="text-muted">'+r.compare_price+'</s> '+r.price;}h+='<tr><td>'+r.id+'</td><td>'+r.name+'</td><td>'+(r.sku||'')+'</td><td>'+(r.category_name||'-')+'</td><td>'+(r.vendor_name||'-')+'</td><td>'+price+'</td><td>'+paymentLabel(r)+'</td><td>'+r.stock_status+'</td><td>'+(r.status==1?'Active':'Inactive')+'</td><td>'+a+'</td></tr>';});if(!h)h='<tr><td colspan="10" class="text-center text-muted">No products found</td></tr>';$('#data-table tbody').html(h);});}

$('#btn-add').on('click',function(){
    $('#main-form')[0].reset();
    $('#record-id').val('');
    $('#f-vendor').val('');
    resetPlans([{name:'12 Month Plan', down_payment:'', monthly_installment:'', months:12}]);
    $('#f-cash').val('1');
    $('#f-installment').val('1');
    togglePlansSection();
    $('#modal-title').text('Add Product');
    $('#form-modal').modal('show');
});
$('#f-installment').on('change', togglePlansSection);
$('#btn-add-plan').on('click',function(){ $('#plans-body').append(planRowHtml({months:12})); });
$(document).on('click','.btn-remove-plan',function(){ $(this).closest('tr').remove(); });
$('#search').on('keyup',loadList);
$('#main-form').on('submit',function(e){
    e.preventDefault();
    var id=$('#record-id').val(),
        url=id?ADMIN_BASE+'/api/products/'+id:ADMIN_BASE+'/api/products',
        $btn=$('#save-btn');
    var fd=new FormData(this);
    fd.set('name', $('#f-name').val() || '');
    AdminApp.setButtonLoading($btn,true);
    AdminApp.request(url,'POST',fd).done(function(res){
        AdminApp.toast('success',res.message);
        $('#form-modal').modal('hide');
        loadList();
    }).always(function(){AdminApp.setButtonLoading($btn,false);});
});
$(document).on('click','.btn-edit',function(){AdminApp.request(ADMIN_BASE+'/api/products/'+$(this).data('id'),'GET').done(function(res){var r=res.data;$('#record-id').val(r.id);$('#f-name').val(r.name);$('#f-sku').val(r.sku);$('#f-price').val(r.price);$('#f-compare-price').val(r.compare_price||'');$('#f-category').val(r.category_id||'');$('#f-vendor').val(r.vendor_id||'');$('#f-stock').val(r.stock_status);$('#f-status').val(r.status);$('#f-description').val(r.description||'');$('#f-cash').val(r.cash_available!=null?r.cash_available:1);$('#f-installment').val(r.installment_available);$('#f-meta-title').val(r.meta_title||'');$('#f-meta-description').val(r.meta_description||'');resetPlans(r.plans||[]);togglePlansSection();$('#modal-title').text('Edit Product');$('#form-modal').modal('show');});});
$(document).on('click','.btn-delete',function(){var id=$(this).data('id');AdminApp.confirmDelete(function(){AdminApp.request(ADMIN_BASE+'/api/products/'+id+'/delete','POST').done(function(res){AdminApp.toast('success',res.message);loadList();});});});
$(loadList);
</script>
<?= $this->endSection() ?>
