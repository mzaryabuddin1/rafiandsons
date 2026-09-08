<?php
/** @var array $product */
$images = $product['images'] ? json_decode($product['images'], true) : [];
$img = $images[0] ?? 'theme/images/demos/demo22/products/1.jpg';
$url = site_url('product/' . $product['slug']);
$advance = $product['min_advance'] ?? null;
$comparePrice = ! empty($product['compare_price']) ? (float) $product['compare_price'] : null;
$cashPrice = (float) $product['price'];
$showCompare = $comparePrice !== null && $comparePrice > $cashPrice;
$cashAvailable = (int) ($product['cash_available'] ?? 1) === 1;
$installmentAvailable = (int) ($product['installment_available'] ?? 0) === 1;
$bothModes = $cashAvailable && $installmentAvailable && $advance !== null;
$openQuickForCart = $bothModes;
?>
<div class="qb-product-card" data-slug="<?= esc($product['slug']) ?>">
    <div class="qb-product-media-wrap">
        <a href="<?= $url ?>" class="qb-product-media">
            <img src="<?= base_url($img) ?>" alt="<?= esc($product['name']) ?>" loading="lazy">
        </a>
        <div class="qb-product-actions">
            <button type="button" class="qb-quick-view js-quick-view" data-slug="<?= esc($product['slug']) ?>" title="Quick View">
                Quick View
            </button>
            <button
                type="button"
                class="qb-icon-cart js-add-cart"
                data-product-id="<?= (int) $product['id'] ?>"
                data-slug="<?= esc($product['slug']) ?>"
                data-cash-available="<?= $cashAvailable ? '1' : '0' ?>"
                data-installment-available="<?= $installmentAvailable ? '1' : '0' ?>"
                data-open-qv="<?= $openQuickForCart ? '1' : '0' ?>"
                title="<?= $openQuickForCart ? 'Choose payment option' : 'Add to Cart' ?>"
            >
                <i class="d-icon-bag"></i>
            </button>
        </div>
    </div>
    <div class="qb-product-body">
        <div class="qb-card-pay-tags">
            <?php if ($cashAvailable): ?>
                <span class="qb-card-pay-tag qb-card-pay-tag--cash">Cash</span>
            <?php endif; ?>
            <?php if ($installmentAvailable): ?>
                <span class="qb-card-pay-tag qb-card-pay-tag--inst">Installment</span>
            <?php endif; ?>
            <?php if (! $cashAvailable && ! $installmentAvailable): ?>
                <span class="qb-card-pay-tag qb-card-pay-tag--cash">Available</span>
            <?php endif; ?>
        </div>

        <h3 class="qb-product-name"><a href="<?= $url ?>"><?= esc($product['name']) ?></a></h3>

        <div class="qb-vendor-tag<?= empty($product['vendor_name']) ? ' is-empty' : '' ?>">
            <?php if (! empty($product['vendor_name'])): ?>
                Vendor: <?= esc($product['vendor_name']) ?>
            <?php else: ?>
                &nbsp;
            <?php endif; ?>
        </div>

        <div class="qb-card-price-box">
            <?php if ($cashAvailable): ?>
                <div class="qb-card-price-row">
                    <span class="qb-card-price-label">Cash</span>
                    <div class="qb-card-price-values">
                        <?php if ($showCompare): ?>
                            <span class="qb-price-compare">PKR <?= number_format($comparePrice, 0) ?></span>
                        <?php endif; ?>
                        <span class="qb-product-price">PKR <?= number_format($cashPrice, 0) ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($installmentAvailable && $advance !== null): ?>
                <div class="qb-card-price-row qb-card-price-row--inst">
                    <span class="qb-card-price-label">Advance</span>
                    <div class="qb-card-price-values">
                        <span class="qb-product-price qb-product-price--advance">From Rs. <?= number_format((float) $advance, 0) ?></span>
                    </div>
                </div>
            <?php elseif ($installmentAvailable && ! $cashAvailable): ?>
                <div class="qb-card-price-row qb-card-price-row--inst">
                    <span class="qb-card-price-label">Plan</span>
                    <div class="qb-card-price-values">
                        <span class="qb-product-price qb-product-price--advance">Installments</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
