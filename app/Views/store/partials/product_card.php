<?php
/** @var array $product */
$images = $product['images'] ? json_decode($product['images'], true) : [];
$img = $images[0] ?? 'theme/images/demos/demo22/products/1.jpg';
$url = site_url('product/' . $product['slug']);
?>
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
            <ins class="new-price">PKR <?= number_format((float) $product['price'], 0) ?></ins>
        </div>
        <?php if (! empty($product['installment_available'])): ?>
            <div class="product-installment-badge">Installments Available</div>
        <?php endif; ?>
    </div>
</div>
