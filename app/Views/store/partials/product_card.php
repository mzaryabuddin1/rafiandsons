<?php
/** @var array $product */
$style = $style ?? 'default';
$images = $product['images'] ? json_decode($product['images'], true) : [];
$img = $images[0] ?? 'theme/images/demos/demo22/products/1.jpg';
$url = site_url('product/' . $product['slug']);
$advance = $product['min_advance'] ?? null;
?>
<?php if ($style === 'qist'): ?>
<div class="qb-product-card">
    <a href="<?= $url ?>" class="qb-product-media">
        <img src="<?= base_url($img) ?>" alt="<?= esc($product['name']) ?>">
    </a>
    <div class="qb-product-body">
        <?php if ($advance !== null && $advance > 0): ?>
            <div class="qb-advance-badge">Rs. <?= number_format($advance, 0) ?> Advance</div>
        <?php elseif (! empty($product['installment_available'])): ?>
            <div class="qb-advance-badge">Installments Available</div>
        <?php endif; ?>
        <h3 class="qb-product-name"><a href="<?= $url ?>"><?= esc($product['name']) ?></a></h3>
        <div class="qb-product-price">PKR <?= number_format((float) $product['price'], 0) ?></div>
        <button type="button" class="qb-add-cart js-add-cart" data-product-id="<?= (int) $product['id'] ?>">
            <i class="d-icon-bag"></i> Add to Cart
        </button>
    </div>
</div>
<?php else: ?>
<div class="product text-center">
    <figure class="product-media">
        <a href="<?= $url ?>">
            <img src="<?= base_url($img) ?>" alt="<?= esc($product['name']) ?>" width="280" height="315" style="object-fit:cover;">
        </a>
        <div class="product-action-vertical">
            <a href="#" class="btn-product-icon btn-cart js-add-cart" data-product-id="<?= (int) $product['id'] ?>" title="Add to cart">
                <i class="d-icon-bag"></i>
            </a>
        </div>
        <div class="product-action">
            <a href="<?= $url ?>" class="btn-product btn-quickview" title="View">Quick View</a>
        </div>
    </figure>
    <div class="product-details">
        <div class="product-cat">
            <a href="<?= site_url('shop?category=' . urlencode($product['category_slug'] ?? '')) ?>"><?= esc($product['category_name'] ?? 'Product') ?></a>
        </div>
        <h3 class="product-name"><a href="<?= $url ?>"><?= esc($product['name']) ?></a></h3>
        <div class="product-price">
            <?php if ($advance !== null && $advance > 0): ?>
                <ins class="new-price">Rs. <?= number_format($advance, 0) ?> Advance</ins>
            <?php else: ?>
                <ins class="new-price">PKR <?= number_format((float) $product['price'], 0) ?></ins>
            <?php endif; ?>
        </div>
        <?php if (! empty($product['installment_available'])): ?>
            <div class="product-installment-badge">Installments Available</div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
