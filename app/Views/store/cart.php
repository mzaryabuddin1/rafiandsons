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
                                $isInstallment = ($item['payment_type'] ?? 'cash') === 'installment';
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
                                        <?php if ($isInstallment): ?>
                                            <?php if (! empty($item['plan_name'])): ?>
                                                <span class="qb-cart-pay-label"><?= esc($item['plan_name']) ?></span>
                                            <?php endif; ?>
                                            <?php if (! empty($item['monthly_installment'])): ?>
                                                <p class="qb-cart-plan-hint">
                                                    Rs. <?= number_format((float) $item['monthly_installment'], 0) ?> × <?= (int) $item['months'] ?> months
                                                    · Total Rs. <?= number_format((float) ($item['total_payable'] ?? 0), 0) ?>
                                                </p>
                                            <?php endif; ?>
                                        <?php else: ?>
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
                        <p class="qb-cart-summary-note">Payment type is chosen when adding to cart. To change it, remove the item and add again with your preferred option.</p>
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
