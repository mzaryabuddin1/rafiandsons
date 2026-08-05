<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<?php
$lineCount = count($items ?? []);
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
                    <p class="qb-page-sub"><?= $lineCount ?> line<?= $lineCount === 1 ? '' : 's' ?> · <?= $itemCount ?> item<?= $itemCount === 1 ? '' : 's' ?></p>
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
                <div class="qb-cart-empty-icon"><i class="d-icon-bag"></i></div>
                <h2>Your cart is empty</h2>
                <p>Browse products and choose cash or installment payment.</p>
                <a href="<?= site_url('shop') ?>" class="qb-btn qb-btn-primary">Start Shopping</a>
            </div>
        <?php else: ?>
            <div class="qb-cart-layout">
                <div class="qb-cart-main">
                    <div class="qb-cart-panel">
                        <div class="qb-cart-columns" aria-hidden="true">
                            <span>Product</span>
                            <span>Due now</span>
                            <span>Qty</span>
                            <span>Payment</span>
                            <span>Subtotal</span>
                            <span></span>
                        </div>

                        <div class="qb-cart-items" id="qb-cart-items">
                            <?php foreach ($items as $item): ?>
                                <?php
                                $cartKey = $item['cart_key'] ?? (string) $item['product_id'];
                                $productPlans = $plansByProduct[$item['product_id']] ?? [];
                                $isInstallment = ($item['payment_type'] ?? 'cash') === 'installment';
                                $itemCash = (int) ($item['cash_available'] ?? 1) === 1;
                                $itemInst = (int) ($item['installment_available'] ?? 0) === 1;
                                $bothModes = $itemCash && $itemInst;
                                $cashPrice = (float) ($item['cash_price'] ?? $item['price']);
                                $lineTotal = (float) $item['price'] * (int) $item['qty'];
                                ?>
                                <article
                                    class="qb-cart-item"
                                    data-cart-key="<?= esc($cartKey) ?>"
                                    data-unit-price="<?= (float) $item['price'] ?>"
                                    data-payment-type="<?= esc($item['payment_type'] ?? 'cash') ?>"
                                >
                                    <div class="qb-cart-item-product">
                                        <a href="<?= site_url('product/' . $item['slug']) ?>" class="qb-cart-item-thumb">
                                            <img src="<?= base_url($item['image']) ?>" alt="<?= esc($item['name']) ?>" loading="lazy">
                                        </a>
                                        <div class="qb-cart-item-info">
                                            <a href="<?= site_url('product/' . $item['slug']) ?>" class="qb-cart-item-name"><?= esc($item['name']) ?></a>
                                            <?php if ($isInstallment && ! empty($item['plan_name'])): ?>
                                                <span class="qb-cart-plan-name"><?= esc($item['plan_name']) ?></span>
                                            <?php endif; ?>
                                            <?php if (! empty($item['sku'])): ?>
                                                <span class="qb-cart-item-sku">SKU: <?= esc($item['sku']) ?></span>
                                            <?php endif; ?>
                                            <?php if ($isInstallment): ?>
                                                <span class="qb-cart-tag qb-cart-tag--inst">Installment</span>
                                            <?php else: ?>
                                                <span class="qb-cart-tag qb-cart-tag--cash">Cash</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="qb-cart-item-price" data-label="Due now">
                                        <strong>PKR <?= number_format((float) $item['price'], 0) ?></strong>
                                        <?php if ($isInstallment): ?>
                                            <small class="qb-cart-due-note">Advance</small>
                                        <?php endif; ?>
                                    </div>

                                    <div class="qb-cart-item-qty" data-label="Qty">
                                        <div class="qb-qty-stepper">
                                            <button type="button" class="qb-qty-btn qb-qty-minus" aria-label="Decrease">&minus;</button>
                                            <input type="number" class="cart-qty qb-qty-input" min="1" max="99" value="<?= (int) $item['qty'] ?>">
                                            <button type="button" class="qb-qty-btn qb-qty-plus" aria-label="Increase">+</button>
                                        </div>
                                    </div>

                                    <div class="qb-cart-item-plan" data-label="Payment">
                                        <?php if ($bothModes): ?>
                                        <div class="qb-cart-pay-toggle">
                                            <button type="button" class="qb-cart-pay-btn <?= ! $isInstallment ? 'is-active' : '' ?>" data-payment="cash">Cash</button>
                                            <button type="button" class="qb-cart-pay-btn <?= $isInstallment ? 'is-active' : '' ?>" data-payment="installment">Installment</button>
                                        </div>
                                        <?php endif; ?>

                                        <?php if ($itemInst): ?>
                                        <div class="qb-cart-plan-wrap" <?= $bothModes && ! $isInstallment ? 'hidden' : '' ?>>
                                            <select class="cart-plan qb-cart-plan-select">
                                                <?php foreach ($productPlans as $plan): ?>
                                                    <option
                                                        value="<?= (int) $plan['id'] ?>"
                                                        <?= ((int) ($item['plan_id'] ?? 0) === (int) $plan['id']) ? 'selected' : '' ?>
                                                        data-down="<?= (float) ($plan['down_payment'] ?? 0) ?>"
                                                        data-monthly="<?= (float) ($plan['monthly_installment'] ?? 0) ?>"
                                                        data-months="<?= (int) ($plan['months'] ?? 0) ?>"
                                                        data-total="<?= (float) ($plan['total_payable'] ?? 0) ?>"
                                                    ><?= esc($plan['name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <p class="qb-cart-plan-hint">
                                                <?php if ($isInstallment && ! empty($item['monthly_installment'])): ?>
                                                    Rs. <?= number_format((float) $item['monthly_installment'], 0) ?> × <?= (int) $item['months'] ?> months
                                                    · Total Rs. <?= number_format((float) ($item['total_payable'] ?? 0), 0) ?>
                                                <?php else: ?>
                                                    Select installment plan
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <?php elseif ($itemCash): ?>
                                            <span class="qb-cart-cash-label">Full cash · PKR <?= number_format($cashPrice, 0) ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="qb-cart-item-total" data-label="Subtotal">
                                        <strong class="line-total">PKR <?= number_format($lineTotal, 0) ?></strong>
                                    </div>

                                    <button type="button" class="qb-cart-item-remove cart-remove" aria-label="Remove"><i class="d-icon-times"></i></button>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <aside class="qb-cart-summary">
                    <div class="qb-order-summary qb-cart-summary-card">
                        <h3>Order Summary</h3>
                        <div class="qb-cart-summary-row">
                            <span>Due now</span>
                            <strong id="cart-subtotal-label">PKR <?= number_format($cartSubtotal, 0) ?></strong>
                        </div>
                        <div class="qb-cart-summary-row qb-cart-summary-row--muted">
                            <span>Total order value</span>
                            <strong id="cart-grand-label">PKR <?= number_format($cartGrandTotal ?? $cartSubtotal, 0) ?></strong>
                        </div>
                        <p class="qb-cart-summary-note">Same product with different payment or plan appears as separate lines. Identical lines merge automatically.</p>
                        <a href="<?= site_url('checkout') ?>" class="qb-btn qb-btn-primary qb-btn-block">Proceed to Checkout</a>
                        <a href="<?= site_url('shop') ?>" class="qb-btn qb-btn-outline qb-btn-block">Continue Shopping</a>
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

    function cartKey($item) {
        return $item.data('cart-key');
    }

    function updateLineTotal($item) {
        var qty = parseInt($item.find('.cart-qty').val(), 10) || 1;
        var price = parseFloat($item.data('unit-price')) || 0;
        $item.find('.line-total').text(formatMoney(price * qty));
    }

    function updateSubtotal() {
        var dueNow = 0;
        $('.qb-cart-item').each(function () {
            var qty = parseInt($(this).find('.cart-qty').val(), 10) || 0;
            var price = parseFloat($(this).data('unit-price')) || 0;
            dueNow += qty * price;
        });
        $('#cart-subtotal-label').text(formatMoney(dueNow));
    }

    function updatePlanHint($select) {
        var $option = $select.find('option:selected');
        var $hint = $select.siblings('.qb-cart-plan-hint');
        if (!$option.val()) {
            $hint.text('Select installment plan');
            return;
        }
        var monthly = Number($option.data('monthly') || 0);
        var months = Number($option.data('months') || 0);
        var total = Number($option.data('total') || 0);
        $hint.text('Rs. ' + monthly.toLocaleString() + ' × ' + months + ' months · Total Rs. ' + total.toLocaleString());
    }

    $(document).on('click', '.qb-qty-minus', function () {
        var $input = $(this).siblings('.cart-qty');
        var qty = Math.max(1, (parseInt($input.val(), 10) || 1) - 1);
        $input.val(qty).trigger('change');
    });

    $(document).on('click', '.qb-qty-plus', function () {
        var $input = $(this).siblings('.cart-qty');
        var qty = Math.min(99, (parseInt($input.val(), 10) || 1) + 1);
        $input.val(qty).trigger('change');
    });

    $(document).on('change', '.cart-qty', function () {
        var $item = $(this).closest('.qb-cart-item');
        var qty = Math.max(1, Math.min(99, parseInt($(this).val(), 10) || 1));
        $(this).val(qty);

        StoreApp.request(STORE_BASE + '/cart/update', 'POST', {
            cart_key: cartKey($item),
            qty: qty
        }).done(function (res) {
            StoreApp.toast(res.message);
            updateLineTotal($item);
            updateSubtotal();
            if (res.data) StoreApp.updateCartBadge(res.data.count, res.data.subtotal);
        });
    });

    $(document).on('change', '.cart-plan', function () {
        var $item = $(this).closest('.qb-cart-item');
        updatePlanHint($(this));

        StoreApp.request(STORE_BASE + '/cart/set-plan', 'POST', {
            cart_key: cartKey($item),
            plan_id: $(this).val()
        }).done(function (res) {
            StoreApp.toast(res.message);
            if (res.data && res.data.cart_key && res.data.cart_key !== cartKey($item)) {
                location.reload();
                return;
            }
            if (res.data && res.data.item) {
                $item.data('unit-price', res.data.item.price);
                $item.find('.qb-cart-item-price strong').first().text(formatMoney(res.data.item.price));
                updateLineTotal($item);
                updateSubtotal();
                StoreApp.updateCartBadge(null, res.data.subtotal);
            }
        });
    });

    $(document).on('click', '.qb-cart-pay-btn', function () {
        var $item = $(this).closest('.qb-cart-item');
        var payment = $(this).data('payment');
        var planId = payment === 'installment' ? $item.find('.cart-plan').val() : '';

        StoreApp.request(STORE_BASE + '/cart/set-payment', 'POST', {
            cart_key: cartKey($item),
            payment_type: payment,
            plan_id: planId
        }).done(function (res) {
            if (res.success) location.reload();
        });
    });

    $(document).on('click', '.cart-remove', function () {
        var $item = $(this).closest('.qb-cart-item');
        StoreApp.request(STORE_BASE + '/cart/remove', 'POST', {
            cart_key: cartKey($item)
        }).done(function (res) {
            $item.remove();
            updateSubtotal();
            if (!$('.qb-cart-item').length) location.reload();
            if (res.data) StoreApp.updateCartBadge(res.data.count, res.data.subtotal);
        });
    });
})(jQuery);
</script>
<?= $this->endSection() ?>
