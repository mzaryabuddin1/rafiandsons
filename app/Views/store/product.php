<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<main class="main">
    <nav class="breadcrumb-nav">
        <div class="container">
            <ul class="breadcrumb">
                <li><a href="<?= site_url('/') ?>"><i class="d-icon-home"></i></a></li>
                <li><a href="<?= site_url('shop') ?>">Shop</a></li>
                <li><?= esc($product['name']) ?></li>
            </ul>
        </div>
    </nav>

    <div class="page-content mb-10">
        <div class="container">
            <div class="product product-single row mb-8">
                <div class="col-md-6">
                    <div class="product-gallery">
                        <div class="product-image-full">
                            <img src="<?= base_url($images[0]) ?>" alt="<?= esc($product['name']) ?>" width="800" height="900" style="width:100%;object-fit:cover;">
                        </div>
                        <?php if (count($images) > 1): ?>
                        <div class="product-thumbs row cols-4 mt-2">
                            <?php foreach ($images as $img): ?>
                                <div class="product-thumb">
                                    <img src="<?= base_url($img) ?>" alt="" width="150" height="150" style="object-fit:cover;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="product-details">
                        <h1 class="product-name"><?= esc($product['name']) ?></h1>
                        <div class="product-meta">
                            SKU: <span class="product-sku"><?= esc($product['sku'] ?: '-') ?></span>
                            | Stock: <span><?= $product['stock_status'] === 'in_stock' ? 'In Stock' : 'Out of Stock' ?></span>
                        </div>
                        <div class="product-price mt-2 mb-4">
                            <ins class="new-price">PKR <?= number_format((float) $product['price'], 0) ?></ins>
                        </div>
                        <p class="product-short-desc"><?= esc($product['description']) ?></p>

                        <?php if (! empty($plans)): ?>
                        <div class="mt-4 mb-4">
                            <h4 class="mb-3">Available Installment Plans</h4>
                            <?php foreach ($plans as $i => $plan): ?>
                            <label class="plan-card <?= $i === 0 ? 'active' : '' ?>">
                                <input type="radio" name="installment_plan_id" value="<?= (int) $plan['id'] ?>" <?= $i === 0 ? 'checked' : '' ?> style="margin-right:8px;">
                                <strong><?= esc($plan['name']) ?></strong>
                                <ul class="mt-2">
                                    <li>Down payment: PKR <?= number_format((float) $plan['down_payment'], 0) ?></li>
                                    <li>Monthly: PKR <?= number_format((float) $plan['monthly_installment'], 0) ?> × <?= (int) $plan['months'] ?> months</li>
                                    <li>Total payable: PKR <?= number_format((float) $plan['total_payable'], 0) ?></li>
                                </ul>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <div class="product-form product-qty mb-4">
                            <div class="product-form-group">
                                <input class="quantity form-control" type="number" min="1" max="10" value="1" id="product-qty">
                                <button class="btn-product btn-cart btn-primary js-add-cart" data-product-id="<?= (int) $product['id'] ?>">
                                    <i class="d-icon-bag"></i> Add to Cart
                                </button>
                                <a href="<?= site_url('checkout') ?>" class="btn btn-dark ml-1 js-buy-now" data-product-id="<?= (int) $product['id'] ?>">Book Installment</a>
                            </div>
                        </div>
                        <p class="text-muted" style="font-size:1.3rem;">Submitting a booking is not automatic financing approval.</p>
                    </div>
                </div>
            </div>

            <?php if (! empty($related)): ?>
            <section>
                <h2 class="title title-center mb-5">Related Products</h2>
                <div class="row cols-2 cols-sm-4">
                    <?php foreach ($related as $item): ?>
                        <div class="product-wrap mb-4">
                            <?= view('store/partials/product_card', ['product' => $item]) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).on('change', 'input[name="installment_plan_id"]', function () {
    $('.plan-card').removeClass('active');
    $(this).closest('.plan-card').addClass('active');
});
$('.js-buy-now').on('click', function (e) {
    e.preventDefault();
    var productId = $(this).data('product-id');
    var planId = $('input[name="installment_plan_id"]:checked').val() || '';
    var qty = $('#product-qty').val() || 1;
    StoreApp.request(STORE_BASE + '/cart/add', 'POST', {
        product_id: productId,
        qty: qty,
        plan_id: planId
    }).done(function () {
        window.location.href = STORE_BASE + '/checkout';
    });
});
</script>
<?= $this->endSection() ?>
