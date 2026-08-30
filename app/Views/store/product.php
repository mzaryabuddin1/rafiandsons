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
                        <?php
                        $comparePrice = ! empty($product['compare_price']) ? (float) $product['compare_price'] : null;
                        $cashPrice = (float) $product['price'];
                        $showCompare = $comparePrice !== null && $comparePrice > $cashPrice;
                        $cashAvailable = (int) ($product['cash_available'] ?? 1) === 1;
                        $installmentAvailable = (int) ($product['installment_available'] ?? 0) === 1 && ! empty($plans);
                        $bothModes = $cashAvailable && $installmentAvailable;
                        $defaultMode = $installmentAvailable && ! $cashAvailable ? 'installment' : 'cash';
                        $minAdvance = null;
                        if ($installmentAvailable) {
                            foreach ($plans as $p) {
                                $adv = (float) ($p['down_payment'] ?? 0);
                                $minAdvance = $minAdvance === null ? $adv : min($minAdvance, $adv);
                            }
                        }
                        ?>
                        <h1 class="qb-product-title"><?= esc($product['name']) ?></h1>
                        <?php if (! empty($product['vendor_name'])): ?>
                            <div class="qb-vendor-tag">Vendor: <?= esc($product['vendor_name']) ?></div>
                        <?php endif; ?>
                        <div class="qb-product-meta">
                            SKU: <?= esc($product['sku'] ?: '-') ?>
                            · <?= $product['stock_status'] === 'in_stock' ? 'In Stock' : 'Out of Stock' ?>
                        </div>

                        <?php if ($bothModes): ?>
                        <div class="qb-payment-choice">
                            <h3 class="qb-payment-choice-title">Choose payment method</h3>
                            <div class="qb-payment-tabs" role="tablist">
                                <label class="qb-payment-tab <?= $defaultMode === 'cash' ? 'is-active' : '' ?>" data-mode="cash">
                                    <input type="radio" name="payment_type" value="cash" <?= $defaultMode === 'cash' ? 'checked' : '' ?>>
                                    <span class="qb-payment-tab-label">Cash</span>
                                    <span class="qb-payment-tab-price">PKR <?= number_format($cashPrice, 0) ?></span>
                                    <span class="qb-payment-tab-note">Pay full amount</span>
                                </label>
                                <label class="qb-payment-tab <?= $defaultMode === 'installment' ? 'is-active' : '' ?>" data-mode="installment">
                                    <input type="radio" name="payment_type" value="installment" <?= $defaultMode === 'installment' ? 'checked' : '' ?>>
                                    <span class="qb-payment-tab-label">Installment</span>
                                    <span class="qb-payment-tab-price">From PKR <?= number_format($minAdvance ?? 0, 0) ?></span>
                                    <span class="qb-payment-tab-note">Pay advance first</span>
                                </label>
                            </div>
                        </div>
                        <?php elseif ($cashAvailable): ?>
                            <input type="hidden" name="payment_type" value="cash">
                        <?php elseif ($installmentAvailable): ?>
                            <input type="hidden" name="payment_type" value="installment">
                        <?php endif; ?>

                        <div class="qb-price-block qb-price-block--cash" <?= ($installmentAvailable && ! $cashAvailable) ? 'hidden' : '' ?>>
                            <div class="qb-product-price-lg qb-product-price-wrap">
                                <?php if ($showCompare): ?>
                                    <span class="qb-price-compare">PKR <?= number_format($comparePrice, 0) ?></span>
                                <?php endif; ?>
                                <span class="qb-cash-price-label">Cash Price</span>
                                <span>PKR <?= number_format($cashPrice, 0) ?></span>
                            </div>
                        </div>

                        <?php if (! empty($product['description'])): ?>
                            <p class="qb-product-desc"><?= esc($product['description']) ?></p>
                        <?php endif; ?>

                        <?php if ($installmentAvailable): ?>
                        <div class="qb-installment-block" <?= $bothModes && $defaultMode === 'cash' ? 'hidden' : '' ?>>
                            <h3 class="qb-installment-title">Select your installment plan</h3>
                            <p class="qb-installment-sub">Only the advance amount is added to cart. Monthly payments start after verification.</p>
                            <div class="qb-plan-cards">
                                <?php foreach ($plans as $i => $plan): ?>
                                <label class="qb-plan-card <?= $i === 0 ? 'is-active' : '' ?>">
                                    <input type="radio" name="installment_plan_id" value="<?= (int) $plan['id'] ?>" <?= $i === 0 ? 'checked' : '' ?>>
                                    <div class="qb-plan-card-inner">
                                        <div class="qb-plan-card-top">
                                            <strong><?= esc($plan['name']) ?></strong>
                                            <span class="qb-plan-card-check"></span>
                                        </div>
                                        <div class="qb-plan-card-monthly">
                                            Rs. <?= number_format((float) $plan['monthly_installment'], 0) ?>
                                            <span>× <?= (int) $plan['months'] ?> months</span>
                                        </div>
                                        <div class="qb-plan-card-advance">
                                            Rs. <?= number_format((float) $plan['down_payment'], 0) ?>
                                            <em>Advance</em>
                                        </div>
                                    </div>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php elseif ($cashAvailable): ?>
                            <p class="qb-cash-only-note">Available for cash purchase only.</p>
                        <?php endif; ?>

                        <div class="qb-buy-row">
                            <input class="qb-qty" type="number" min="1" max="10" value="1" id="product-qty">
                            <button type="button" class="qb-btn qb-btn-primary js-add-cart" data-product-id="<?= (int) $product['id'] ?>">
                                <i class="d-icon-bag"></i> Add to Cart
                            </button>
                            <button type="button" class="qb-btn qb-btn-dark js-buy-now" data-product-id="<?= (int) $product['id'] ?>">
                                <?= $installmentAvailable && ! $cashAvailable ? 'Book Installment' : 'Buy Now' ?>
                            </button>
                        </div>
                        <?php if ($installmentAvailable): ?>
                        <p class="qb-disclaimer">Installment booking is subject to verification by our team.</p>
                        <?php endif; ?>
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
function productPaymentType() {
    var $checked = $('input[name="payment_type"]:checked');
    if ($checked.length) return $checked.val();
    var $hidden = $('input[name="payment_type"][type="hidden"]');
    return $hidden.length ? $hidden.val() : 'cash';
}

function syncProductPaymentUI() {
    var mode = productPaymentType();
    $('.qb-payment-tab').removeClass('is-active');
    $('.qb-payment-tab input[value="' + mode + '"]').closest('.qb-payment-tab').addClass('is-active');

    if (mode === 'cash') {
        $('.qb-price-block--cash').removeAttr('hidden');
        $('.qb-installment-block').attr('hidden', true);
    } else {
        $('.qb-price-block--cash').attr('hidden', true);
        $('.qb-installment-block').removeAttr('hidden');
    }
}

$(document).on('change', 'input[name="payment_type"]', syncProductPaymentUI);
$(document).on('change', 'input[name="installment_plan_id"]', function () {
    $('.qb-plan-card').removeClass('is-active');
    $(this).closest('.qb-plan-card').addClass('is-active');
});
$(document).on('click', '.qb-thumb', function () {
    $('.qb-thumb').removeClass('active');
    $(this).addClass('active');
    $('#qb-main-image').attr('src', $(this).data('src'));
});

function addProductToCart(redirectCheckout) {
    var productId = <?= (int) $product['id'] ?>;
    var qty = $('#product-qty').val() || 1;
    var paymentType = productPaymentType();
    var planId = paymentType === 'installment'
        ? ($('input[name="installment_plan_id"]:checked').val() || '')
        : '';

    if (paymentType === 'installment' && !planId) {
        StoreApp.toast('Please select an installment plan.');
        return;
    }

    StoreApp.request(STORE_BASE + '/cart/add', 'POST', {
        product_id: productId,
        qty: qty,
        payment_type: paymentType,
        plan_id: planId
    }).done(function () {
        if (redirectCheckout) {
            window.location.href = STORE_BASE + '/checkout';
        }
    });
}

$('.js-buy-now').on('click', function (e) {
    e.preventDefault();
    addProductToCart(true);
});

syncProductPaymentUI();
</script>
<?= $this->endSection() ?>
