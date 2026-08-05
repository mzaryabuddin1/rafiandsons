<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<?php
$itemCount = 0;
foreach ($items ?? [] as $item) {
    $itemCount += (int) ($item['qty'] ?? 0);
}
?>
<main class="main qb-main qb-cart-page">
    <div class="qb-shop-breadcrumb">
        <div class="container">
            <nav class="qb-breadcrumb" aria-label="Breadcrumb">
                <a href="<?= site_url('home') ?>">Home</a>
                <span>/</span>
                <span>Shopping Cart</span>
            </nav>
        </div>
    </div>

    <div class="container qb-cart-wrap">
        <div class="qb-cart-head">
            <div>
                <h1 class="qb-page-title">Shopping Cart</h1>
                <?php if (! empty($items)): ?>
                    <p class="qb-page-sub"><?= $itemCount ?> item<?= $itemCount === 1 ? '' : 's' ?> in your cart</p>
                <?php endif; ?>
            </div>
            <?php if (! empty($items)): ?>
                <a href="<?= site_url('shop') ?>" class="qb-btn qb-btn-outline qb-cart-continue">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
            <?php endif; ?>
        </div>

        <?php if (empty($items)): ?>
            <div class="qb-cart-empty">
                <div class="qb-cart-empty-icon">
                    <i class="d-icon-bag"></i>
                </div>
                <h2>Your cart is empty</h2>
                <p>Browse our products and add items to start your installment booking.</p>
                <a href="<?= site_url('shop') ?>" class="qb-btn qb-btn-primary">Start Shopping</a>
            </div>
        <?php else: ?>
            <div class="qb-cart-layout">
                <div class="qb-cart-main">
                    <div class="qb-cart-panel">
                        <div class="qb-cart-columns" aria-hidden="true">
                            <span>Product</span>
                            <span>Price</span>
                            <span>Qty</span>
                            <span>Installment Plan</span>
                            <span>Subtotal</span>
                            <span></span>
                        </div>

                        <div class="qb-cart-items" id="qb-cart-items">
                            <?php foreach ($items as $item): ?>
                                <?php
                                $productPlans = $plansByProduct[$item['product_id']] ?? [];
                                $selectedPlan = null;
                                foreach ($productPlans as $plan) {
                                    if ((int) ($item['plan_id'] ?? 0) === (int) $plan['id']) {
                                        $selectedPlan = $plan;
                                        break;
                                    }
                                }
                                $lineTotal = (float) $item['price'] * (int) $item['qty'];
                                ?>
                                <article
                                    class="qb-cart-item"
                                    data-product-id="<?= (int) $item['product_id'] ?>"
                                    data-unit-price="<?= (float) $item['price'] ?>"
                                >
                                    <div class="qb-cart-item-product">
                                        <a href="<?= site_url('product/' . $item['slug']) ?>" class="qb-cart-item-thumb">
                                            <img src="<?= base_url($item['image']) ?>" alt="<?= esc($item['name']) ?>" loading="lazy">
                                        </a>
                                        <div class="qb-cart-item-info">
                                            <a href="<?= site_url('product/' . $item['slug']) ?>" class="qb-cart-item-name">
                                                <?= esc($item['name']) ?>
                                            </a>
                                            <?php if (! empty($item['sku'])): ?>
                                                <span class="qb-cart-item-sku">SKU: <?= esc($item['sku']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="qb-cart-item-price" data-label="Price">
                                        <strong>PKR <?= number_format((float) $item['price'], 0) ?></strong>
                                    </div>

                                    <div class="qb-cart-item-qty" data-label="Qty">
                                        <div class="qb-qty-stepper">
                                            <button type="button" class="qb-qty-btn qb-qty-minus" aria-label="Decrease quantity">&minus;</button>
                                            <input
                                                type="number"
                                                class="cart-qty qb-qty-input"
                                                min="1"
                                                max="99"
                                                value="<?= (int) $item['qty'] ?>"
                                                aria-label="Quantity"
                                            >
                                            <button type="button" class="qb-qty-btn qb-qty-plus" aria-label="Increase quantity">+</button>
                                        </div>
                                    </div>

                                    <div class="qb-cart-item-plan" data-label="Plan">
                                        <select class="cart-plan qb-cart-plan-select" aria-label="Installment plan">
                                            <option value="">Select plan</option>
                                            <?php foreach ($productPlans as $plan): ?>
                                                <option
                                                    value="<?= (int) $plan['id'] ?>"
                                                    <?= ((int) ($item['plan_id'] ?? 0) === (int) $plan['id']) ? 'selected' : '' ?>
                                                    data-down="<?= (float) ($plan['down_payment'] ?? 0) ?>"
                                                    data-monthly="<?= (float) ($plan['monthly_installment'] ?? 0) ?>"
                                                    data-months="<?= (int) ($plan['months'] ?? 0) ?>"
                                                >
                                                    <?= esc($plan['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <p class="qb-cart-plan-hint">
                                            <?php if ($selectedPlan): ?>
                                                Rs. <?= number_format((float) $selectedPlan['down_payment'], 0) ?> advance ·
                                                Rs. <?= number_format((float) $selectedPlan['monthly_installment'], 0) ?> × <?= (int) $selectedPlan['months'] ?> months
                                            <?php else: ?>
                                                Choose an installment plan for this product
                                            <?php endif; ?>
                                        </p>
                                    </div>

                                    <div class="qb-cart-item-total" data-label="Subtotal">
                                        <strong class="line-total">PKR <?= number_format($lineTotal, 0) ?></strong>
                                    </div>

                                    <button type="button" class="qb-cart-item-remove cart-remove" aria-label="Remove item">
                                        <i class="d-icon-times"></i>
                                    </button>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <aside class="qb-cart-summary">
                    <div class="qb-order-summary qb-cart-summary-card">
                        <h3>Order Summary</h3>

                        <ul class="qb-cart-summary-list">
                            <?php foreach ($items as $item): ?>
                                <li>
                                    <span><?= esc($item['name']) ?> × <?= (int) $item['qty'] ?></span>
                                    <strong>PKR <?= number_format((float) $item['price'] * (int) $item['qty'], 0) ?></strong>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="qb-order-total qb-cart-summary-total">
                            <span>Subtotal</span>
                            <strong id="cart-subtotal-label">PKR <?= number_format($cartSubtotal, 0) ?></strong>
                        </div>

                        <p class="qb-cart-summary-note">
                            Installment terms are confirmed on checkout. Down payment and monthly amounts depend on your selected plan.
                        </p>

                        <a href="<?= site_url('checkout') ?>" class="qb-btn qb-btn-primary qb-btn-block">Proceed to Checkout</a>
                        <a href="<?= site_url('shop') ?>" class="qb-btn qb-btn-outline qb-btn-block">Continue Shopping</a>
                    </div>

                    <div class="qb-cart-trust">
                        <div><i class="fas fa-shield-alt"></i> Secure booking request</div>
                        <div><i class="fas fa-phone-alt"></i> Team will contact you to verify</div>
                    </div>
                </aside>
            </div>
        <?php endif; ?>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function ($) {
    function formatMoney(amount) {
        return 'PKR ' + Number(amount || 0).toLocaleString();
    }

    function updateLineTotal($item) {
        var qty = parseInt($item.find('.cart-qty').val(), 10) || 1;
        var price = parseFloat($item.data('unit-price')) || 0;
        $item.find('.line-total').text(formatMoney(price * qty));
    }

    function updateSubtotal() {
        var total = 0;
        $('.qb-cart-item').each(function () {
            var qty = parseInt($(this).find('.cart-qty').val(), 10) || 0;
            var price = parseFloat($(this).data('unit-price')) || 0;
            total += qty * price;
        });
        $('#cart-subtotal-label').text(formatMoney(total));
    }

    function updatePlanHint($select) {
        var $option = $select.find('option:selected');
        var $hint = $select.siblings('.qb-cart-plan-hint');
        if (!$option.val()) {
            $hint.text('Choose an installment plan for this product');
            return;
        }
        var down = Number($option.data('down') || 0);
        var monthly = Number($option.data('monthly') || 0);
        var months = Number($option.data('months') || 0);
        $hint.text(
            'Rs. ' + down.toLocaleString() + ' advance · Rs. ' +
            monthly.toLocaleString() + ' × ' + months + ' months'
        );
    }

    function syncQty($input, qty) {
        qty = Math.max(1, Math.min(99, qty));
        $input.val(qty);
        return qty;
    }

    $(document).on('click', '.qb-qty-minus', function () {
        var $input = $(this).siblings('.cart-qty');
        syncQty($input, (parseInt($input.val(), 10) || 1) - 1).trigger('change');
    });

    $(document).on('click', '.qb-qty-plus', function () {
        var $input = $(this).siblings('.cart-qty');
        syncQty($input, (parseInt($input.val(), 10) || 1) + 1).trigger('change');
    });

    $(document).on('change', '.cart-qty', function () {
        var $item = $(this).closest('.qb-cart-item');
        var qty = syncQty($(this), parseInt($(this).val(), 10) || 1);

        StoreApp.request(STORE_BASE + '/cart/update', 'POST', {
            product_id: $item.data('product-id'),
            qty: qty
        }).done(function (res) {
            StoreApp.toast(res.message);
            updateLineTotal($item);
            updateSubtotal();
            if (res.data && typeof res.data.count !== 'undefined') {
                StoreApp.updateCartBadge(res.data.count, res.data.subtotal);
            }
        });
    });

    $(document).on('change', '.cart-plan', function () {
        var $item = $(this).closest('.qb-cart-item');
        updatePlanHint($(this));

        StoreApp.request(STORE_BASE + '/cart/set-plan', 'POST', {
            product_id: $item.data('product-id'),
            plan_id: $(this).val()
        }).done(function (res) {
            StoreApp.toast(res.message);
        });
    });

    $(document).on('click', '.cart-remove', function (e) {
        e.preventDefault();
        var $item = $(this).closest('.qb-cart-item');

        StoreApp.request(STORE_BASE + '/cart/remove', 'POST', {
            product_id: $item.data('product-id')
        }).done(function (res) {
            $item.addClass('is-removing');
            setTimeout(function () {
                $item.remove();
                updateSubtotal();
                if (!$('.qb-cart-item').length) {
                    location.reload();
                }
            }, 220);
            if (res.data) {
                StoreApp.updateCartBadge(res.data.count, res.data.subtotal);
            }
        });
    });
})(jQuery);
</script>
<?= $this->endSection() ?>
