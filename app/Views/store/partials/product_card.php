<?php
/** @var array $product */
$images = $product['images'] ? json_decode($product['images'], true) : [];
$img = $images[0] ?? 'theme/images/demos/demo22/products/1.jpg';
$url = site_url('product/' . $product['slug']);
$advance = $product['min_advance'] ?? null;
?>
<div class="qb-product-card">
    <div class="qb-product-media-wrap">
        <a href="<?= $url ?>" class="qb-product-media">
            <img src="<?= base_url($img) ?>" alt="<?= esc($product['name']) ?>" loading="lazy">
        </a>
        <div class="qb-product-actions">
            <button type="button" class="qb-quick-view js-quick-view" data-slug="<?= esc($product['slug']) ?>" title="Quick View">
                Quick View
            </button>
            <button type="button" class="qb-icon-cart js-add-cart" data-product-id="<?= (int) $product['id'] ?>" title="Add to Cart">
                <i class="d-icon-bag"></i>
            </button>
        </div>
    </div>
    <div class="qb-product-body">
        <?php if ($advance !== null && $advance > 0): ?>
            <div class="qb-advance-badge">Rs. <?= number_format((float) $advance, 0) ?> Advance</div>
        <?php elseif (! empty($product['installment_available'])): ?>
            <div class="qb-advance-badge">Installments Available</div>
        <?php endif; ?>
        <h3 class="qb-product-name"><a href="<?= $url ?>"><?= esc($product['name']) ?></a></h3>
        <div class="qb-product-price">PKR <?= number_format((float) $product['price'], 0) ?></div>
    </div>
</div>
