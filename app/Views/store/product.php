<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<main class="main qb-main">
    <nav class="qb-breadcrumb">
        <div class="container">
            <a href="<?= site_url('home') ?>">Home</a>
            <span>/</span>
            <a href="<?= site_url('shop') ?>">Shop</a>
            <span>/</span>
            <span><?= esc($product['name']) ?></span>
        </div>
    </nav>

    <div class="page-content qb-product-page">
        <div class="container">
            <div class="row qb-product-row">
                <div class="col-md-6">
                    <div class="qb-gallery">
                        <div class="qb-gallery-main">
                            <img id="qb-main-image" src="<?= base_url($images[0]) ?>" alt="<?= esc($product['name']) ?>">
                        </div>
                        <?php if (count($images) > 1): ?>
                        <div class="qb-gallery-thumbs">
                            <?php foreach ($images as $i => $img): ?>
                                <button type="button" class="qb-thumb <?= $i === 0 ? 'active' : '' ?>" data-src="<?= base_url($img) ?>">
                                    <img src="<?= base_url($img) ?>" alt="">
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="qb-product-info">
                        <h1 class="qb-product-title"><?= esc($product['name']) ?></h1>
                        <div class="qb-product-meta">
                            SKU: <?= esc($product['sku'] ?: '-') ?>
                            · <?= $product['stock_status'] === 'in_stock' ? 'In Stock' : 'Out of Stock' ?>
                        </div>
                        <div class="qb-product-price-lg">PKR <?= number_format((float) $product['price'], 0) ?></div>
                        <?php if (! empty($product['description'])): ?>
                            <p class="qb-product-desc"><?= esc($product['description']) ?></p>
                        <?php endif; ?>

                        <?php if (! empty($plans)): ?>
                        <div class="qb-plans">
                            <h3>Available Installment Plans</h3>
                            <?php foreach ($plans as $i => $plan): ?>
                            <label class="qb-plan <?= $i === 0 ? 'is-active' : '' ?>">
                                <input type="radio" name="installment_plan_id" value="<?= (int) $plan['id'] ?>" <?= $i === 0 ? 'checked' : '' ?>>
                                <span class="qb-plan-body">
                                    <strong><?= esc($plan['name']) ?></strong>
                                    <span>Down payment: PKR <?= number_format((float) $plan['down_payment'], 0) ?></span>
                                    <span>Monthly: PKR <?= number_format((float) $plan['monthly_installment'], 0) ?> × <?= (int) $plan['months'] ?> months</span>
                                    <span>Total payable: PKR <?= number_format((float) $plan['total_payable'], 0) ?></span>
                                </span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <div class="qb-buy-row">
                            <input class="qb-qty" type="number" min="1" max="10" value="1" id="product-qty">
                            <button type="button" class="qb-btn qb-btn-primary js-add-cart" data-product-id="<?= (int) $product['id'] ?>">
                                <i class="d-icon-bag"></i> Add to Cart
                            </button>
                            <button type="button" class="qb-btn qb-btn-dark js-buy-now" data-product-id="<?= (int) $product['id'] ?>">
                                Book Installment
                            </button>
                        </div>
                        <p class="qb-disclaimer">Submitting a booking is not automatic financing approval.</p>
                    </div>
                </div>
            </div>

            <?php if (! empty($related)): ?>
            <section class="qb-related">
                <h2 class="qb-section-title">Related Products</h2>
                <div class="row">
                    <?php foreach ($related as $item): ?>
                        <div class="col-6 col-sm-4 col-md-3 mb-4">
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
    $('.qb-plan').removeClass('is-active');
    $(this).closest('.qb-plan').addClass('is-active');
});
$(document).on('click', '.qb-thumb', function () {
    $('.qb-thumb').removeClass('active');
    $(this).addClass('active');
    $('#qb-main-image').attr('src', $(this).data('src'));
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
