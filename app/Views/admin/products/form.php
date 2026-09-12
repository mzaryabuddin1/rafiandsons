<?= $this->extend('admin/layout') ?>
<?= $this->section('styles') ?>
<link href="<?= base_url('admintheme/css/plugins/dropzone/dropzone.css') ?>" rel="stylesheet">
<link href="<?= base_url('admintheme/css/plugins/summernote/summernote-bs4.css') ?>" rel="stylesheet">
<style>
    .product-dropzone {
        border: 2px dashed #d2d6dc;
        border-radius: 6px;
        background: #fafbfc;
        min-height: 160px;
        padding: 16px;
    }
    .product-dropzone.dz-drag-hover {
        border-color: #1ab394;
        background: #f0fffa;
    }
    .product-dropzone .dz-message {
        margin: 28px 0;
        color: #676a6c;
        font-weight: 600;
    }
    .product-dropzone .dz-message .note {
        display: block;
        font-weight: 400;
        font-size: 12px;
        color: #999;
        margin-top: 6px;
    }
    .product-dropzone .dz-preview {
        margin: 10px;
    }
    .product-dropzone .dz-preview .dz-image {
        border-radius: 6px;
        overflow: hidden;
    }
    /* Files are submitted with the product form, not uploaded by Dropzone —
       hide the idle progress bar / status marks that otherwise show as white bars. */
    .product-dropzone .dz-preview .dz-progress,
    .product-dropzone .dz-preview .dz-success-mark,
    .product-dropzone .dz-preview .dz-error-mark {
        display: none !important;
    }
    .product-dropzone .dz-preview .dz-remove {
        display: inline-block;
        margin-top: 6px;
        font-size: 12px;
        color: #ed5565;
    }
    .existing-images-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 10px;
    }
    .existing-image-card {
        position: relative;
        width: 110px;
        border: 1px solid #e7eaec;
        border-radius: 6px;
        overflow: hidden;
        background: #fff;
    }
    .existing-image-card img {
        display: block;
        width: 110px;
        height: 110px;
        object-fit: cover;
    }
    .existing-image-card .btn-remove-existing {
        display: block;
        width: 100%;
        border: 0;
        border-radius: 0;
        padding: 6px 4px;
        font-size: 12px;
    }
    #existing-images-wrap {
        display: none;
        margin-bottom: 12px;
    }
    .note-editor.note-frame {
        border: 1px solid #e5e6e7;
        border-radius: 2px;
    }
    .note-editor .note-editing-area .note-editable {
        height: 240px !important;
        min-height: 240px !important;
        max-height: 240px !important;
        overflow-y: auto !important;
        scrollbar-width: thin;
        scrollbar-color: #d2070d transparent;
    }
    .note-editor .note-editing-area .note-editable::-webkit-scrollbar {
        width: 5px;
    }
    .note-editor .note-editing-area .note-editable::-webkit-scrollbar-thumb {
        background: #d2070d;
        border-radius: 4px;
    }
    .note-editor .note-editing-area .note-editable::-webkit-scrollbar-track {
        background: transparent;
    }
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php $isEdit = ! empty($isEdit); $recordId = ! empty($recordId) ? (int) $recordId : null; ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
    <div class="col-lg-8">
        <h2><?= $isEdit ? 'Edit Product' : 'Add Product' ?></h2>
    </div>
    <div class="col-lg-4 text-right">
        <a href="<?= admin_url('products') ?>" class="btn btn-white"><i class="fa fa-arrow-left"></i> Back to Products</a>
    </div>
</div>

<div class="ibox">
    <div class="ibox-title"><h5><?= $isEdit ? 'Edit Product' : 'Add Product' ?></h5></div>
    <div class="ibox-content">
        <form id="main-form" enctype="multipart/form-data">
            <input type="hidden" id="record-id" value="<?= $recordId ? (int) $recordId : '' ?>">
            <div class="row">
                <div class="col-md-6"><div class="form-group"><label>Name *</label><input class="form-control" name="name" id="f-name" required></div></div>
                <div class="col-md-3"><div class="form-group"><label>SKU</label><input class="form-control" name="sku" id="f-sku"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Price (Cash) *</label><input type="number" step="0.01" min="0" class="form-control" name="price" id="f-price" placeholder="e.g. 50000" required></div></div>
                <div class="col-md-3"><div class="form-group"><label>Compare Price</label><input type="number" step="0.01" min="0" class="form-control" name="compare_price" id="f-compare-price" placeholder="Original / MRP"></div></div>
            </div>
            <div class="row">
                <div class="col-md-4"><div class="form-group"><label>Category</label><select class="form-control" name="category_id" id="f-category"><option value="">Select</option><?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>"><?= esc($c['label']) ?></option><?php endforeach; ?></select></div></div>
                <div class="col-md-4"><div class="form-group"><label>Vendor</label><select class="form-control" name="vendor_id" id="f-vendor"><option value="">Own / No vendor</option><?php foreach (($vendors ?? []) as $v): ?><option value="<?= (int) $v['id'] ?>"><?= esc($v['business_name']) ?></option><?php endforeach; ?></select></div></div>
                <div class="col-md-4"><div class="form-group"><label>Stock</label><select class="form-control" name="stock_status" id="f-stock"><option value="in_stock">In Stock</option><option value="out_of_stock">Out of Stock</option></select></div></div>
            </div>
            <div class="row">
                <div class="col-md-4"><div class="form-group"><label>Status</label><select class="form-control" name="status" id="f-status"><option value="1">Active</option><option value="0">Inactive</option></select></div></div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea class="form-control" name="description" id="f-description" rows="8"></textarea>
            </div>

            <div class="form-group">
                <label>Images</label>
                <div id="existing-images-wrap">
                    <p class="text-muted m-b-xs">Current images — remove any you don’t want to keep:</p>
                    <div class="existing-images-grid" id="existing-images"></div>
                </div>
                <div id="product-images-dropzone" class="dropzone product-dropzone">
                    <div class="dz-message">
                        Drop images here or click to upload
                        <span class="note">JPG, PNG, WEBP, GIF — multiple files allowed</span>
                    </div>
                </div>
                <small class="text-muted">Previews appear below. Use Remove on any image before saving.</small>
            </div>

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

            <div class="row">
                <div class="col-md-6"><div class="form-group"><label>Meta Title</label><input class="form-control" name="meta_title" id="f-meta-title"></div></div>
                <div class="col-md-6"><div class="form-group"><label>Meta Description</label><input class="form-control" name="meta_description" id="f-meta-description"></div></div>
            </div>

            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <a href="<?= admin_url('products') ?>" class="btn btn-white">Cancel</a>
                <button type="submit" class="btn btn-primary" id="save-btn">Save</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script src="<?= base_url('admintheme/js/plugins/summernote/summernote-bs4.js') ?>"></script>
<script src="<?= base_url('admintheme/js/plugins/dropzone/dropzone.js') ?>"></script>
<script>
Dropzone.autoDiscover = false;

var recordId = <?= $recordId ? (int) $recordId : 'null' ?>;
var planRowIndex = 0;
var existingImages = [];
var productDropzone = null;

function initDescriptionEditor(html) {
    var $body = $('#f-description');
    if ($body.next('.note-editor').length) {
        $body.summernote('destroy');
    }
    $body.summernote({
        height: 240,
        dialogsInBody: true,
        placeholder: 'Write product description…',
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

function syncDescriptionEditor() {
    var $body = $('#f-description');
    if ($body.next('.note-editor').length) {
        $body.val($body.summernote('code'));
    }
}

function planRowHtml(plan){
    plan = plan || {};
    var i = planRowIndex++;
    var monthsVal = (plan.months != null && plan.months !== '') ? plan.months : '';
    return '<tr class="plan-row">'
        + '<td><input type="hidden" name="plans['+i+'][id]" value="'+(plan.id||'')+'">'
        + '<input class="form-control input-sm" name="plans['+i+'][name]" value="'+(plan.name||'')+'" placeholder="e.g. 12 Month Plan"></td>'
        + '<td><input type="number" step="0.01" min="0" class="form-control input-sm" name="plans['+i+'][down_payment]" value="'+(plan.down_payment!=null && plan.down_payment!==''?plan.down_payment:'')+'" placeholder="Down payment"></td>'
        + '<td><input type="number" step="0.01" min="0" class="form-control input-sm" name="plans['+i+'][monthly_installment]" value="'+(plan.monthly_installment!=null && plan.monthly_installment!==''?plan.monthly_installment:'')+'" placeholder="Monthly"></td>'
        + '<td><input type="number" class="form-control input-sm" name="plans['+i+'][months]" value="'+monthsVal+'" min="1" placeholder="12"></td>'
        + '<td><button type="button" class="btn btn-xs btn-danger btn-remove-plan"><i class="fa fa-times"></i></button></td>'
        + '</tr>';
}

function resetPlans(plans){
    planRowIndex=0;
    $('#plans-body').empty();
    if(plans && plans.length){
        plans.forEach(function(p){ $('#plans-body').append(planRowHtml(p)); });
    }
}

function togglePlansSection(){
    var show=$('#f-installment').val()==='1';
    $('#plans-section').toggle(show);
}

function renderExistingImages(list){
    existingImages = Array.isArray(list) ? list.slice() : [];
    var $wrap = $('#existing-images-wrap');
    var $grid = $('#existing-images').empty();
    if (!existingImages.length) {
        $wrap.hide();
        return;
    }
    existingImages.forEach(function(path, index){
        var src = path.indexOf('http') === 0 ? path : (BASE_URL + path.replace(/^\//, ''));
        $grid.append(
            '<div class="existing-image-card" data-path="'+ $('<div>').text(path).html() +'">'
            + '<img src="'+ src +'" alt="Product image">'
            + '<button type="button" class="btn btn-danger btn-remove-existing" data-index="'+ index +'"><i class="fa fa-trash"></i> Remove</button>'
            + '</div>'
        );
    });
    $wrap.show();
}

function validateProductForm(){
    var name=($.trim($('#f-name').val())||'');
    if(!name){
        AdminApp.toast('error','Product name is required.');
        $('#f-name').focus();
        return false;
    }

    var priceRaw=$.trim($('#f-price').val());
    if(priceRaw===''){
        AdminApp.toast('error','Price is required.');
        $('#f-price').focus();
        return false;
    }
    var price=parseFloat(priceRaw);
    if(isNaN(price) || price < 0){
        AdminApp.toast('error','Price cannot be negative.');
        $('#f-price').focus();
        return false;
    }

    var compareRaw=$.trim($('#f-compare-price').val());
    if(compareRaw!==''){
        var compare=parseFloat(compareRaw);
        if(isNaN(compare) || compare < 0){
            AdminApp.toast('error','Compare price cannot be negative.');
            $('#f-compare-price').focus();
            return false;
        }
    }

    if($('#f-installment').val()==='1'){
        if($('#plans-body .plan-row').length < 1){
            AdminApp.toast('error','Add at least one installment plan, or set Installment to Not Available.');
            return false;
        }
        var invalidPlan=false;
        $('#plans-body .plan-row').each(function(){
            var $row=$(this);
            var planName=$.trim($row.find('input[name*="[name]"]').val()||'');
            var down=$row.find('input[name*="[down_payment]"]').val();
            var monthly=$row.find('input[name*="[monthly_installment]"]').val();
            var months=$row.find('input[name*="[months]"]').val();
            if(planName==='' && down==='' && monthly==='' && months===''){
                invalidPlan='Please fill the installment plan details, or remove empty plan rows.';
                return false;
            }
            if(down==='' || isNaN(parseFloat(down)) || parseFloat(down) < 0){
                invalidPlan='Enter a valid down payment (0 or more).';
                return false;
            }
            if(monthly==='' || isNaN(parseFloat(monthly)) || parseFloat(monthly) < 0){
                invalidPlan='Enter a valid monthly installment (0 or more).';
                return false;
            }
            if(months==='' || isNaN(parseInt(months,10)) || parseInt(months,10) < 1){
                invalidPlan='Months must be at least 1.';
                return false;
            }
        });
        if(invalidPlan){
            AdminApp.toast('error', invalidPlan);
            return false;
        }
    }

    return true;
}

function fillProductForm(r){
    $('#record-id').val(r.id);
    $('#f-name').val(r.name);
    $('#f-sku').val(r.sku);
    $('#f-price').val(r.price);
    $('#f-compare-price').val(r.compare_price||'');
    $('#f-category').val(r.category_id||'');
    $('#f-vendor').val(r.vendor_id||'');
    $('#f-stock').val(r.stock_status);
    $('#f-status').val(r.status);
    initDescriptionEditor(r.description || '');
    $('#f-cash').val(r.cash_available!=null?r.cash_available:1);
    $('#f-installment').val(r.installment_available);
    $('#f-meta-title').val(r.meta_title||'');
    $('#f-meta-description').val(r.meta_description||'');
    resetPlans(r.plans||[]);
    togglePlansSection();

    var imgs = r.images_list || [];
    if ((!imgs || !imgs.length) && r.images) {
        try {
            imgs = typeof r.images === 'string' ? JSON.parse(r.images || '[]') : (r.images || []);
        } catch (e) {
            imgs = [];
        }
    }
    renderExistingImages(imgs);
}

productDropzone = new Dropzone('#product-images-dropzone', {
    url: '#',
    autoProcessQueue: false,
    uploadMultiple: true,
    parallelUploads: 20,
    maxFilesize: 8,
    acceptedFiles: 'image/*',
    addRemoveLinks: true,
    dictRemoveFile: 'Remove',
    dictDefaultMessage: 'Drop images here or click to upload',
    clickable: true,
    createImageThumbnails: true,
    thumbnailWidth: 120,
    thumbnailHeight: 120,
    init: function () {
        this.on('error', function (file, message) {
            AdminApp.toast('error', typeof message === 'string' ? message : 'Invalid image file.');
            this.removeFile(file);
        });
    }
});

$(document).on('click', '.btn-remove-existing', function () {
    var path = $(this).closest('.existing-image-card').data('path');
    existingImages = existingImages.filter(function (p) { return p !== path; });
    renderExistingImages(existingImages);
});

$('#f-installment').on('change', togglePlansSection);
$('#btn-add-plan').on('click',function(){ $('#plans-body').append(planRowHtml({})); });
$(document).on('click','.btn-remove-plan',function(){ $(this).closest('tr').remove(); });

$('#main-form').on('submit',function(e){
    e.preventDefault();
    syncDescriptionEditor();
    if(!validateProductForm()) return;

    var id=$('#record-id').val(),
        url=id?ADMIN_BASE+'/api/products/'+id:ADMIN_BASE+'/api/products',
        $btn=$('#save-btn');

    var fd = new FormData(this);
    fd.set('name', $('#f-name').val() || '');
    fd.set('description', $('#f-description').val() || '');
    fd.set('images_managed', '1');

    // Drop any accidental file inputs from Dropzone internals.
    if (fd.delete) {
        fd.delete('file');
        fd.delete('file[]');
        fd.delete('images');
        fd.delete('images[]');
        fd.delete('keep_images');
        fd.delete('keep_images[]');
    }

    existingImages.forEach(function (path) {
        fd.append('keep_images[]', path);
    });

    if (productDropzone && productDropzone.files && productDropzone.files.length) {
        productDropzone.files.forEach(function (file) {
            if (file && file.accepted !== false && file.status !== Dropzone.ERROR && file.status !== Dropzone.CANCELED) {
                fd.append('images[]', file, file.name);
            }
        });
    }

    AdminApp.setButtonLoading($btn,true);
    AdminApp.request(url,'POST',fd).done(function(res){
        AdminApp.toast('success',res.message);
        window.location = ADMIN_BASE + '/products';
    }).always(function(){AdminApp.setButtonLoading($btn,false);});
});

if(recordId){
    AdminApp.request(ADMIN_BASE+'/api/products/'+recordId,'GET').done(function(res){
        fillProductForm(res.data);
    });
} else {
    resetPlans([]);
    $('#f-cash').val('1');
    $('#f-installment').val('1');
    togglePlansSection();
    renderExistingImages([]);
    initDescriptionEditor('');
}
</script>
<?= $this->endSection() ?>
