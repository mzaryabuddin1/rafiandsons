<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<?php
$homeSliders = $homeSliders ?? [];
$catIcons = [
    'electronics'     => 'fa-mobile-alt',
    'home-appliances' => 'fa-blender',
    'computers'       => 'fa-laptop',
    'fashion'         => 'fa-tshirt',
    'beauty'          => 'fa-spa',
    'furniture'       => 'fa-couch',
    'televisions'     => 'fa-tv',
    'mobiles'         => 'fa-mobile-alt',
    'kitchen'         => 'fa-blender',
    'laptops'         => 'fa-laptop',
];
?>
<main class="main qb-main">
    <div class="page-content">
        <!-- Hero: category sidebar + slider (full width) -->
        <section class="qb-hero">
            <div class="qb-hero-full">
                <div class="row g-0 qb-hero-row">
                    <div class="col-lg-3 d-none d-lg-block">
                        <div class="qb-cat-sidebar">
                            <ul>
                                <?php foreach ($categoryTree as $cat): ?>
                                    <?php $icon = $catIcons[$cat['slug']] ?? 'fa-box'; ?>
                                    <li class="<?= ! empty($cat['children']) ? 'has-children' : '' ?>">
                                        <a href="<?= site_url('shop?category=' . urlencode($cat['slug'])) ?>">
                                            <i class="fas <?= esc($icon) ?>"></i>
                                            <span><?= esc($cat['name']) ?></span>
                                            <?php if (! empty($cat['children'])): ?><i class="fas fa-chevron-right qb-cat-arrow"></i><?php endif; ?>
                                        </a>
                                        <?php if (! empty($cat['children'])): ?>
                                        <ul class="qb-cat-sub">
                                            <?php foreach ($cat['children'] as $child): ?>
                                                <li><a href="<?= site_url('shop?category=' . urlencode($child['slug'])) ?>"><?= esc($child['name']) ?></a></li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="qb-hero-slider owl-carousel owl-theme" data-owl-options="{'items':1,'dots':true,'loop':true,'autoplay':true,'autoplayTimeout':5000}">
                            <?php if ($homeSliders): ?>
                                <?php foreach ($homeSliders as $i => $slide): ?>
                                    <?php
                                    $href = \App\Models\BannerModel::resolveLink($slide['link'] ?? null);
                                    $img  = $slide['image'] ?: 'theme/images/demos/demo22/slides/' . (($i % 2) + 1) . '.jpg';
                                    ?>
                                    <a href="<?= esc($href) ?>" class="qb-slide">
                                        <img src="<?= base_url($img) ?>" alt="<?= esc($slide['title']) ?>">
                                        <?php if (! empty($slide['title']) || ! empty($slide['badge_text'])): ?>
                                        <div class="qb-slide-caption">
                                            <?php if (! empty($slide['subtitle'])): ?>
                                                <span class="qb-slide-tag"><?= esc($slide['subtitle']) ?></span>
                                            <?php endif; ?>
                                            <h2><?= esc($slide['title']) ?></h2>
                                            <?php if (! empty($slide['badge_text'])): ?>
                                                <div class="qb-slide-price"><?= esc($slide['badge_text']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <?php endif; ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <a href="<?= site_url('shop') ?>" class="qb-slide">
                                    <img src="<?= base_url('theme/images/demos/demo22/slides/1.jpg') ?>" alt="Shop on installments">
                                    <div class="qb-slide-caption">
                                        <h2>Shop on Easy Installments</h2>
                                        <div class="qb-slide-price">Book Today</div>
                                    </div>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="qb-features">
            <div class="qb-features-inner">
                <div class="row">
                    <div class="col-6 col-md-3">
                        <div class="qb-feature-item">
                            <i class="fas fa-shipping-fast"></i>
                            <span>Free Delivery</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="qb-feature-item">
                            <i class="fas fa-user-check"></i>
                            <span>Easy Verification Process</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="qb-feature-item">
                            <i class="far fa-credit-card"></i>
                            <span>No Bank Account Required</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="qb-feature-item">
                            <i class="far fa-file-alt"></i>
                            <span>No Hidden Charges</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Top categories icons -->
        <section class="qb-section qb-top-cats">
            <div class="container">
                <h2 class="qb-section-title">Top Categories of the Month</h2>
                <div class="qb-cat-icons owl-carousel owl-theme" data-owl-options="{
                    'margin': 16,
                    'dots': false,
                    'nav': true,
                    'responsive': {
                        '0': {'items': 3},
                        '576': {'items': 5},
                        '992': {'items': 8}
                    }
                }">
                    <?php foreach ($topCategories as $cat): ?>
                        <a href="<?= site_url('shop?category=' . urlencode($cat['slug'])) ?>" class="qb-cat-icon-item">
                            <span class="qb-cat-icon-circle">
                                <i class="fas <?= esc($cat['icon'] ?? 'fa-box') ?>"></i>
                            </span>
                            <span class="qb-cat-icon-label"><?= esc($cat['name']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Deals of the day -->
        <?php if (! empty($featured)): ?>
        <section class="qb-section qb-deals">
            <div class="container">
                <div class="qb-section-head">
                    <h2 class="qb-section-title mb-0">Deals of the Day</h2>
                    <a href="<?= site_url('shop') ?>" class="qb-view-all">View All <i class="fas fa-angle-right"></i></a>
                </div>
                <div class="qb-product-carousel owl-carousel owl-theme" data-owl-options="{
                    'margin': 16,
                    'dots': false,
                    'nav': true,
                    'responsive': {
                        '0': {'items': 2},
                        '768': {'items': 3},
                        '992': {'items': 4},
                        '1200': {'items': 5}
                    }
                }">
                    <?php foreach (array_slice($featured, 0, 10) as $product): ?>
                        <div class="qb-product-slide">
                            <?= view('store/partials/product_card', ['product' => $product, 'style' => 'qist']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Category-wise product rows -->
        <?php foreach ($categorySections ?? [] as $section): ?>
        <section class="qb-section qb-cat-products">
            <div class="container">
                <div class="qb-section-head">
                    <h2 class="qb-section-title mb-0"><?= esc($section['category']['name']) ?></h2>
                    <a href="<?= site_url('shop?category=' . urlencode($section['category']['slug'])) ?>" class="qb-view-all">View All <i class="fas fa-angle-right"></i></a>
                </div>
                <div class="qb-product-carousel owl-carousel owl-theme" data-owl-options="{
                    'margin': 16,
                    'dots': false,
                    'nav': true,
                    'responsive': {
                        '0': {'items': 2},
                        '768': {'items': 3},
                        '992': {'items': 4},
                        '1200': {'items': 5}
                    }
                }">
                    <?php foreach ($section['products'] as $product): ?>
                        <div class="qb-product-slide">
                            <?= view('store/partials/product_card', ['product' => $product, 'style' => 'qist']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endforeach; ?>

        <!-- Mid banners from admin -->
        <?php if (! empty($homeMid ?? [])): ?>
        <section class="qb-section">
            <div class="container">
                <div class="row g-3">
                    <?php foreach ($homeMid as $i => $mid): ?>
                        <?php
                        $midHref = \App\Models\BannerModel::resolveLink($mid['link'] ?? null);
                        $midImg  = $mid['image'] ?: 'theme/images/demos/demo22/banner/' . (($i % 2) + 1) . '.jpg';
                        $midBg   = $mid['bg_color'] ?: '#d2070d';
                        ?>
                        <div class="col-md-6">
                            <a href="<?= esc($midHref) ?>" class="qb-mid-banner" style="background-color:<?= esc($midBg) ?>">
                                <img src="<?= base_url($midImg) ?>" alt="<?= esc($mid['title']) ?>">
                                <div class="qb-mid-banner-text">
                                    <h3><?= esc($mid['title']) ?></h3>
                                    <?php if (! empty($mid['description'])): ?>
                                        <p><?= esc($mid['description']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>
    </div>
</main>
<?= $this->endSection() ?>
