<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<?php
$homeSliders = $homeSliders ?? [];
$homeSide    = $homeSide ?? [];
$homeMid     = $homeMid ?? [];
$sideBanner  = $homeSide[0] ?? null;
?>
<main class="main mt-lg-4">
    <div class="page-content">
        <section class="intro-section container">
            <div class="row">
                <div class="col-xl-9 col-lg-9 col-md-8 mb-4 mb-lg-0">
                    <div class="intro-slider animation-slider owl-carousel owl-theme owl-dot-inner row cols-1 gutter-no"
                         data-owl-options="{
                            'items': 1,
                            'dots': true,
                            'loop': true
                         }">
                        <?php if ($homeSliders): ?>
                            <?php foreach ($homeSliders as $i => $slide): ?>
                                <?php
                                $isDark   = ($slide['style'] ?? 'light') === 'dark';
                                $bg       = $slide['bg_color'] ?: ($isDark ? '#7a7675' : '#e8e8ea');
                                $btnClass = $isDark ? 'btn btn-outline btn-white btn-rounded' : 'btn btn-outline btn-dark btn-rounded';
                                $href     = \App\Models\BannerModel::resolveLink($slide['link'] ?? null);
                                $btnText  = $slide['button_text'] ?: 'Shop now';
                                $img      = $slide['image'] ?: 'theme/images/demos/demo22/slides/' . (($i % 2) + 1) . '.jpg';
                                ?>
                                <div class="intro-slide<?= $i === 0 ? '1' : '2' ?> banner banner-fixed" style="background-color: <?= esc($bg) ?>">
                                    <figure>
                                        <img src="<?= base_url($img) ?>" alt="<?= esc($slide['title']) ?>" width="580" height="460">
                                    </figure>
                                    <div class="banner-content x-50 y-50 text-center <?= $isDark ? '' : 'd-flex flex-column align-items-center' ?>">
                                        <?php if ($isDark): ?><div><?php endif; ?>
                                            <?php if (! empty($slide['subtitle'])): ?>
                                                <h4 class="banner-subtitle <?= $isDark ? 'mb-1 ls-l text-white text-uppercase font-weight-normal' : 'text-body font-weight-normal' ?>">
                                                    <?= esc($slide['subtitle']) ?>
                                                </h4>
                                            <?php endif; ?>
                                            <h3 class="banner-title <?= $isDark ? 'ls-l text-white text-uppercase font-weight-bold' : '' ?>">
                                                <?= esc($slide['title']) ?>
                                            </h3>
                                            <?php if (! empty($slide['description'])): ?>
                                                <p class="<?= $isDark ? 'ls-l mb-5 text-white font-primary' : 'font-weight-semi-bold text-grey' ?>">
                                                    <?= esc($slide['description']) ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if (! empty($slide['badge_text'])): ?>
                                                <div class="banner-price-info ls-s text-uppercase text-primary font-weight-bold flex-1">
                                                    <?= esc($slide['badge_text']) ?>
                                                </div>
                                            <?php endif; ?>
                                            <a href="<?= esc($href) ?>" class="<?= $btnClass ?>"><?= esc($btnText) ?></a>
                                        <?php if ($isDark): ?></div><?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="intro-slide1 banner banner-fixed" style="background-color: #e8e8ea">
                                <figure>
                                    <img src="<?= base_url('theme/images/demos/demo22/slides/1.jpg') ?>" alt="banner" width="580" height="460">
                                </figure>
                                <div class="banner-content x-50 y-50 text-center d-flex flex-column align-items-center">
                                    <h4 class="banner-subtitle text-body font-weight-normal">Financing Offer</h4>
                                    <h3 class="banner-title">Shop with Installments</h3>
                                    <a href="<?= site_url('shop') ?>" class="btn btn-outline btn-dark btn-rounded">Shop now</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-4 mb-4">
                    <?php if ($sideBanner): ?>
                        <?php
                        $sideHref = \App\Models\BannerModel::resolveLink($sideBanner['link'] ?? null);
                        $sideBtn  = $sideBanner['button_text'] ?: 'Buy Now';
                        $sideImg  = $sideBanner['image'] ?: 'theme/images/demos/demo22/banner/drone.png';
                        ?>
                        <div class="intro-banner banner banner-fixed overlay-dark">
                            <figure>
                                <img class="x-50" src="<?= base_url($sideImg) ?>" alt="<?= esc($sideBanner['title']) ?>" width="346" height="193">
                            </figure>
                            <div class="banner-content x-50 y-50 text-center d-flex flex-column align-items-center">
                                <?php if (! empty($sideBanner['subtitle'])): ?>
                                    <p class="text-white font-primary text-uppercase flex-1 lh-1"><?= nl2br(esc($sideBanner['subtitle'])) ?></p>
                                <?php endif; ?>
                                <?php if (! empty($sideBanner['badge_text'])): ?>
                                    <h4 class="banner-subtitle mb-1 text-uppercase ls-normal font-weight-normal"><?= esc($sideBanner['badge_text']) ?></h4>
                                <?php endif; ?>
                                <h3 class="banner-title ls-md font-weight-bold"><?= esc($sideBanner['title']) ?></h3>
                                <?php if (! empty($sideBanner['description'])): ?>
                                    <p class="text-body mb-2"><?= esc($sideBanner['description']) ?></p>
                                <?php endif; ?>
                                <a href="<?= esc($sideHref) ?>" class="btn btn-dark btn-md btn-rounded"><?= esc($sideBtn) ?></a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="intro-banner banner banner-fixed overlay-dark">
                            <figure>
                                <img class="x-50" src="<?= base_url('theme/images/demos/demo22/banner/drone.png') ?>" alt="product" width="346" height="193">
                            </figure>
                            <div class="banner-content x-50 y-50 text-center d-flex flex-column align-items-center">
                                <p class="text-white font-primary text-uppercase flex-1 lh-1">Through <br><span class="d-inline-block mt-1 ls-normal">Rafi &amp; Sons</span></p>
                                <h4 class="banner-subtitle mb-1 text-uppercase ls-normal font-weight-normal">Up to 70% Off</h4>
                                <h3 class="banner-title ls-md font-weight-bold">Featured Offer</h3>
                                <a href="<?= site_url('shop') ?>" class="btn btn-dark btn-md btn-rounded">Buy Now</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="categories container mt-10">
            <h2 class="title title-line title-underline border-1 mb-4">Top Categories of the Month</h2>
            <div class="row">
                <?php foreach ($topCategories as $i => $cat): ?>
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="category category-group-image">
                        <a href="<?= site_url('shop?category=' . urlencode($cat['slug'])) ?>">
                            <figure class="category-media">
                                <img src="<?= base_url($cat['image_file']) ?>" alt="<?= esc($cat['name']) ?>" width="190" height="169">
                            </figure>
                        </a>
                        <div class="category-content">
                            <h4 class="category-name">
                                <a href="<?= site_url('shop?category=' . urlencode($cat['slug'])) ?>"><?= esc($cat['name']) ?></a>
                            </h4>
                            <ul class="category-list">
                                <?php if (! empty($cat['products'])): ?>
                                    <?php foreach ($cat['products'] as $p): ?>
                                        <li><a href="<?= site_url('product/' . $p['slug']) ?>"><?= esc($p['name']) ?></a></li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li><a href="<?= site_url('shop?category=' . urlencode($cat['slug'])) ?>">View all</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="banner-group container mt-10 pb-4 pt-2 mb-10">
            <div class="owl-carousel owl-theme row cols-md-2 cols-1" data-owl-options="{
                'items': 2,
                'margin': 20,
                'dots': true,
                'responsive': {
                    '0': { 'items': 1 },
                    '768': { 'items': 2, 'loop': false },
                    '992': { 'dots': false }
                }
            }">
                <?php if ($homeMid): ?>
                    <?php foreach ($homeMid as $i => $mid): ?>
                        <?php
                        $midHref = \App\Models\BannerModel::resolveLink($mid['link'] ?? null);
                        $midBtn  = $mid['button_text'] ?: 'Shop Now';
                        $midBg   = $mid['bg_color'] ?: ($i % 2 === 0 ? '#d2070d' : '#444443');
                        $midImg  = $mid['image'] ?: 'theme/images/demos/demo22/banner/' . (($i % 2) + 1) . '.jpg';
                        ?>
                        <div class="banner<?= ($i % 2) + 1 ?> banner banner-fixed overlay-zoom" style="background-color: <?= esc($midBg) ?>">
                            <figure>
                                <img src="<?= base_url($midImg) ?>" alt="<?= esc($mid['title']) ?>" width="580" height="219">
                            </figure>
                            <div class="banner-content y-50">
                                <h3 class="banner-title text-white"><?= esc($mid['title']) ?></h3>
                                <?php if (! empty($mid['description'])): ?>
                                    <p class="mb-7 text-white"><?= esc($mid['description']) ?></p>
                                <?php endif; ?>
                                <a href="<?= esc($midHref) ?>" class="btn btn-link btn-white btn-underline"><?= esc($midBtn) ?><i class="fas fa-angle-right"></i></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="banner1 banner banner-fixed overlay-zoom" style="background-color: #d2070d">
                        <figure>
                            <img src="<?= base_url('theme/images/demos/demo22/banner/1.jpg') ?>" alt="banner" width="580" height="219">
                        </figure>
                        <div class="banner-content y-50">
                            <h3 class="banner-title text-white">Easy Installment Plans</h3>
                            <p class="mb-7 text-white">Choose a plan that fits your budget and submit a booking request. Our team will verify and contact you.</p>
                            <a href="<?= site_url('installment-terms') ?>" class="btn btn-link btn-white btn-underline">Installment Terms<i class="fas fa-angle-right"></i></a>
                            <a href="<?= site_url('shop') ?>" class="btn btn-link btn-white btn-underline">Shop Now<i class="fas fa-angle-right"></i></a>
                        </div>
                    </div>
                    <div class="banner2 banner banner-fixed overlay-zoom" style="background-color: #444443">
                        <figure>
                            <img src="<?= base_url('theme/images/demos/demo22/banner/2.jpg') ?>" alt="banner" width="580" height="219">
                        </figure>
                        <div class="banner-content y-50">
                            <h3 class="banner-title text-white">Ready-To-Ship Products</h3>
                            <p class="mb-7 text-white">Browse our latest electronics, appliances, and more — available with flexible installment options.</p>
                            <a href="<?= site_url('shop') ?>" class="btn btn-link btn-white btn-underline">New Arrivals<i class="fas fa-angle-right"></i></a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="container pb-10">
            <h2 class="title title-line title-underline border-1 mb-4">Featured Products</h2>
            <div class="row cols-2 cols-sm-3 cols-md-4 product-wrapper">
                <?php foreach ($featured as $product): ?>
                    <div class="product-wrap mb-4">
                        <?= view('store/partials/product_card', ['product' => $product]) ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-2">
                <a href="<?= site_url('shop') ?>" class="btn btn-dark btn-rounded">View All Products<i class="d-icon-arrow-right"></i></a>
            </div>
        </section>
    </div>
</main>
<?= $this->endSection() ?>
