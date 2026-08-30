<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<main class="main qb-main">
    <div class="page-content">
        <!-- Hero: category sidebar + slider -->
        <section class="qb-hero">
            <div class="qb-hero-inner">
                <aside class="qb-cat-sidebar d-none d-lg-block" id="qb-cat-sidebar">
                    <div class="qb-cat-sidebar-scroll" id="qb-cat-sidebar-scroll">
                        <ul>
                            <?php foreach ($categoryTree as $cat): ?>
                                <?php $icon = category_fa_icon($cat['slug'] ?? '', $cat['icon'] ?? $cat['description'] ?? null); ?>
                                <li class="<?= ! empty($cat['children']) ? 'has-children' : '' ?>">
                                    <a href="<?= site_url('shop?category=' . urlencode($cat['slug'])) ?>">
                                        <i class="fas <?= esc($icon) ?>" aria-hidden="true"></i>
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
                    <button type="button" class="qb-cat-scroll-hint" id="qb-cat-scroll-hint" aria-label="Scroll categories" hidden>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </aside>

                <div class="qb-hero-slider-wrap">
                    <div class="qb-hero-slider owl-carousel owl-theme" data-owl-options="{'items':1,'dots':true,'nav':false,'loop':true,'autoplay':true,'autoplayTimeout':4500,'smartSpeed':600,'autoHeight':false}">
                        <?php if ($homeSliders): ?>
                            <?php foreach ($homeSliders as $i => $slide): ?>
                                <?php
                                $href = \App\Models\BannerModel::resolveLink($slide['link'] ?? null);
                                $img  = $slide['image'] ?: 'theme/images/demos/demo22/slides/' . (($i % 2) + 1) . '.jpg';
                                ?>
                                <a href="<?= esc($href) ?>" class="qb-slide">
                                    <img src="<?= base_url($img) ?>" alt="<?= esc($slide['title'] ?: 'Promo') ?>" loading="<?= $i === 0 ? 'eager' : 'lazy' ?>">
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <a href="<?= site_url('shop') ?>" class="qb-slide">
                                <img src="<?= base_url('theme/images/demos/demo22/slides/1.jpg') ?>" alt="Shop on installments">
                            </a>
                        <?php endif; ?>
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
                            <span>No Bank Account / Card Required</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="qb-feature-item">
                            <i class="far fa-file-alt"></i>
                            <span>No Documentation Charges</span>
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
                                <?php if (! empty($cat['image_file']) && is_file(FCPATH . $cat['image_file'])): ?>
                                    <img src="<?= base_url($cat['image_file']) ?>" alt="<?= esc($cat['name']) ?>">
                                <?php else: ?>
                                    <i class="fas <?= esc($cat['icon'] ?? 'fa-box') ?>"></i>
                                <?php endif; ?>
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
                <?php if (! empty($section['banner']['image'])): ?>
                    <?php
                    $b = $section['banner'];
                    $bHref = \App\Models\BannerModel::resolveLink($b['link'] ?? ('shop?category=' . $section['category']['slug']));
                    ?>
                    <a href="<?= esc($bHref) ?>" class="qb-section-banner">
                        <img src="<?= base_url($b['image']) ?>" alt="<?= esc($b['title'] ?: $section['category']['name']) ?>" loading="lazy">
                    </a>
                <?php endif; ?>
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

<?= $this->section('scripts') ?>
<script>
(function ($) {
    var $scroll = $('#qb-cat-sidebar-scroll');
    var $hint = $('#qb-cat-scroll-hint');
    if (!$scroll.length) return;

    function updateHint() {
        var el = $scroll[0];
        var canScroll = el.scrollHeight > el.clientHeight + 4;
        var atBottom = el.scrollTop + el.clientHeight >= el.scrollHeight - 4;
        $hint.prop('hidden', !(canScroll && !atBottom));
    }

    $scroll.on('scroll', updateHint);
    $(window).on('resize', updateHint);
    $hint.on('click', function () {
        $scroll.animate({ scrollTop: $scroll.scrollTop() + 120 }, 200);
    });

    // Keep flyout menus visible while the sidebar scrolls (fixed positioning).
    var $openItem = null;
    var hideTimer = null;

    function hideSub() {
        if ($openItem) {
            $openItem.removeClass('is-open');
            $openItem.children('.qb-cat-sub').removeClass('is-fixed').removeAttr('style');
            $openItem = null;
        }
    }

    function showSub($item) {
        hideSub();
        var $sub = $item.children('.qb-cat-sub');
        if (!$sub.length) return;
        var rect = $item[0].getBoundingClientRect();
        $item.addClass('is-open');
        $sub.addClass('is-fixed').css({
            top: Math.max(8, Math.min(rect.top, window.innerHeight - 200)) + 'px',
            left: rect.right + 'px',
            display: 'block'
        });
        $openItem = $item;
    }

    $('#qb-cat-sidebar')
        .on('mouseenter', 'li.has-children', function () {
            clearTimeout(hideTimer);
            showSub($(this));
        })
        .on('mouseleave', 'li.has-children', function () {
            hideTimer = setTimeout(hideSub, 120);
        });

    $(document).on('mouseenter', '.qb-cat-sub.is-fixed', function () {
        clearTimeout(hideTimer);
    }).on('mouseleave', '.qb-cat-sub.is-fixed', function () {
        hideTimer = setTimeout(hideSub, 120);
    });

    $scroll.on('scroll', hideSub);

    if (window.ResizeObserver) {
        new ResizeObserver(updateHint).observe($scroll[0]);
    }
    // Slider aspect-ratio / owl init can change height after first paint
    [50, 300, 800].forEach(function (ms) { setTimeout(updateHint, ms); });
})(jQuery);
</script>
<?= $this->endSection() ?>
