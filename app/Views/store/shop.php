<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<?php
$activeId       = ! empty($activeCategory) ? (int) $activeCategory['id'] : 0;
$activeParentId = ! empty($activeCategory['parent_id']) ? (int) $activeCategory['parent_id'] : 0;
$catalogMin     = (int) ($catalogMin ?? 0);
$catalogMax     = (int) ($catalogMax ?? 100000);
$rangeMin       = $minPrice !== null ? (int) $minPrice : $catalogMin;
$rangeMax       = $maxPrice !== null ? (int) $maxPrice : $catalogMax;
$hasPriceFilter = $minPrice !== null || $maxPrice !== null;
?>
<main class="main qb-main qb-shop-page">
    <div class="qb-shop-breadcrumb">
        <div class="container">
            <nav class="qb-breadcrumb" aria-label="Breadcrumb">
                <a href="<?= site_url('home') ?>">Home</a>
                <span>/</span>
                <a href="<?= shop_query_url([], ['category', 'min_price', 'max_price']) ?>">Shop</a>
                <?php if (! empty($search)): ?>
                    <span>/</span>
                    <span>Search: <?= esc($search) ?></span>
                <?php elseif (! empty($activeCategory)): ?>
                    <span>/</span>
                    <span><?= esc($activeCategory['name']) ?></span>
                <?php endif; ?>
            </nav>
        </div>
    </div>

    <div class="container qb-shop-wrap">
        <div class="qb-shop-layout" id="qb-shop-layout">
            <aside class="qb-shop-sidebar" id="qb-shop-sidebar">
                <div class="qb-shop-filter-panel">
                    <div class="qb-shop-filter-head">
                        <h2>Filters</h2>
                        <button type="button" class="qb-shop-filter-close" id="qb-shop-filter-close" aria-label="Close filters">
                            <i class="d-icon-times"></i>
                        </button>
                    </div>

                    <?php if (! empty($search) || $hasPriceFilter): ?>
                        <div class="qb-shop-active-filters">
                            <?php if (! empty($search)): ?>
                                <span class="qb-filter-chip">
                                    Search: <?= esc($search) ?>
                                    <a href="<?= shop_query_url([], ['q']) ?>" aria-label="Clear search">&times;</a>
                                </span>
                            <?php endif; ?>
                            <?php if ($hasPriceFilter): ?>
                                <span class="qb-filter-chip">
                                    Price:
                                    <?= $minPrice !== null ? 'Rs. ' . number_format((float) $minPrice, 0) : 'Any' ?>
                                    –
                                    <?= $maxPrice !== null ? 'Rs. ' . number_format((float) $maxPrice, 0) : 'Any' ?>
                                    <a href="<?= shop_query_url([], ['min_price', 'max_price']) ?>" aria-label="Clear price filter">&times;</a>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="qb-filter-block is-collapsible is-open">
                        <button type="button" class="qb-filter-block-toggle" aria-expanded="true">
                            <span>Search Products</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="qb-filter-block-body">
                            <form action="<?= site_url('search') ?>" method="get" class="qb-sidebar-search">
                                <?php if (! empty($activeCategory)): ?>
                                    <input type="hidden" name="category" value="<?= esc($activeCategory['slug']) ?>">
                                <?php endif; ?>
                                <input
                                    type="search"
                                    name="q"
                                    value="<?= esc($search ?? '') ?>"
                                    placeholder="Search by name, SKU..."
                                    class="qb-sidebar-search-input"
                                    aria-label="Search products"
                                >
                                <button type="submit" class="qb-btn qb-btn-primary qb-btn-sm qb-btn-block">Search</button>
                            </form>
                        </div>
                    </div>

                    <div class="qb-filter-block is-collapsible is-open">
                        <button type="button" class="qb-filter-block-toggle" aria-expanded="true">
                            <span>Categories</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="qb-filter-block-body">
                            <ul class="qb-filter-list">
                                <li class="<?= $activeId === 0 ? 'is-active' : '' ?>">
                                    <a href="<?= shop_query_url([], ['category']) ?>">All Products</a>
                                </li>
                                <?php foreach (($categoryTree ?? []) as $cat): ?>
                                    <?php
                                    $catId          = (int) $cat['id'];
                                    $hasChildren    = ! empty($cat['children']);
                                    $isParentActive = $activeId === $catId || $activeParentId === $catId;
                                    ?>
                                    <li class="qb-filter-parent <?= $hasChildren ? 'has-children' : '' ?> <?= $isParentActive ? 'is-active is-open' : '' ?>">
                                        <div class="qb-filter-parent-row">
                                            <a href="<?= shop_query_url(['category' => $cat['slug']]) ?>">
                                                <i class="fas <?= esc(category_fa_icon($cat['slug'], $cat['description'] ?? null)) ?>"></i>
                                                <?= esc($cat['name']) ?>
                                            </a>
                                            <?php if ($hasChildren): ?>
                                                <button type="button" class="qb-filter-expand" aria-expanded="<?= $isParentActive ? 'true' : 'false' ?>" aria-label="Toggle <?= esc($cat['name']) ?> subcategories">
                                                    <i class="fas fa-chevron-down"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($hasChildren): ?>
                                            <ul class="qb-filter-sublist">
                                                <?php foreach ($cat['children'] as $child): ?>
                                                    <li class="<?= $activeId === (int) $child['id'] ? 'is-active' : '' ?>">
                                                        <a href="<?= shop_query_url(['category' => $child['slug']]) ?>">
                                                            <?= esc($child['name']) ?>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="qb-filter-block is-collapsible is-open">
                        <button type="button" class="qb-filter-block-toggle" aria-expanded="true">
                            <span>Price Range</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="qb-filter-block-body">
                            <form method="get" action="<?= site_url('shop') ?>" class="qb-price-filter" id="qb-price-filter">
                                <?php if (! empty($activeCategory)): ?>
                                    <input type="hidden" name="category" value="<?= esc($activeCategory['slug']) ?>">
                                <?php endif; ?>
                                <?php if (! empty($search)): ?>
                                    <input type="hidden" name="q" value="<?= esc($search) ?>">
                                <?php endif; ?>
                                <?php if (! empty($sort)): ?>
                                    <input type="hidden" name="sort" value="<?= esc($sort) ?>">
                                <?php endif; ?>

                                <div class="qb-price-inputs">
                                    <label>
                                        <span>Min</span>
                                        <input type="number" name="min_price" id="qb-price-min" min="<?= $catalogMin ?>" max="<?= $catalogMax ?>" step="100" value="<?= $minPrice !== null ? (int) $minPrice : '' ?>" placeholder="<?= number_format($catalogMin) ?>">
                                    </label>
                                    <label>
                                        <span>Max</span>
                                        <input type="number" name="max_price" id="qb-price-max" min="<?= $catalogMin ?>" max="<?= $catalogMax ?>" step="100" value="<?= $maxPrice !== null ? (int) $maxPrice : '' ?>" placeholder="<?= number_format($catalogMax) ?>">
                                    </label>
                                </div>

                                <div class="qb-range-slider" data-min="<?= $catalogMin ?>" data-max="<?= $catalogMax ?>">
                                    <div class="qb-range-track"></div>
                                    <div class="qb-range-fill"></div>
                                    <input type="range" class="qb-range-min" min="<?= $catalogMin ?>" max="<?= $catalogMax ?>" step="100" value="<?= $rangeMin ?>" aria-label="Minimum price">
                                    <input type="range" class="qb-range-max" min="<?= $catalogMin ?>" max="<?= $catalogMax ?>" step="100" value="<?= $rangeMax ?>" aria-label="Maximum price">
                                </div>

                                <div class="qb-price-labels">
                                    <span>Rs. <strong id="qb-price-min-label"><?= number_format($rangeMin) ?></strong></span>
                                    <span>Rs. <strong id="qb-price-max-label"><?= number_format($rangeMax) ?></strong></span>
                                </div>

                                <div class="qb-price-actions">
                                    <button type="submit" class="qb-btn qb-btn-primary qb-btn-sm">Apply</button>
                                    <?php if ($hasPriceFilter): ?>
                                        <a href="<?= shop_query_url([], ['min_price', 'max_price']) ?>" class="qb-btn qb-btn-outline qb-btn-sm">Reset</a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="qb-shop-content">
                <div class="qb-shop-toolbar">
                    <div class="qb-shop-toolbar-left">
                        <button type="button" class="qb-shop-filter-toggle" id="qb-shop-filter-toggle">
                            <i class="fas fa-filter"></i> Filters
                        </button>
                        <button type="button" class="qb-shop-sidebar-collapse" id="qb-shop-sidebar-collapse" aria-expanded="true" title="Toggle filters sidebar">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <h1 class="qb-shop-title">
                            <?= esc($pageTitle) ?>
                            <small id="qb-shop-total-count">(<?= (int) ($totalProducts ?? count($products)) ?>)</small>
                        </h1>
                        <?php if (! empty($products) && ($totalProducts ?? 0) > 0): ?>
                            <p class="qb-shop-progress">
                                Showing <span id="qb-shop-loaded-count"><?= (int) ($loadedCount ?? count($products)) ?></span>
                                of <span id="qb-shop-total-label"><?= (int) ($totalProducts ?? count($products)) ?></span> products
                            </p>
                        <?php endif; ?>
                    </div>
                    <form method="get" action="<?= site_url('shop') ?>" class="qb-shop-sort">
                        <?php if (! empty($activeCategory)): ?>
                            <input type="hidden" name="category" value="<?= esc($activeCategory['slug']) ?>">
                        <?php endif; ?>
                        <?php if (! empty($search)): ?>
                            <input type="hidden" name="q" value="<?= esc($search) ?>">
                        <?php endif; ?>
                        <?php if ($minPrice !== null): ?>
                            <input type="hidden" name="min_price" value="<?= (int) $minPrice ?>">
                        <?php endif; ?>
                        <?php if ($maxPrice !== null): ?>
                            <input type="hidden" name="max_price" value="<?= (int) $maxPrice ?>">
                        <?php endif; ?>
                        <label for="shop-sort" class="sr-only">Sort products</label>
                        <select id="shop-sort" name="sort" onchange="this.form.submit()">
                            <option value="">Latest</option>
                            <option value="price_asc" <?= ($sort ?? '') === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="price_desc" <?= ($sort ?? '') === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                            <option value="name" <?= ($sort ?? '') === 'name' ? 'selected' : '' ?>>Name</option>
                        </select>
                    </form>
                </div>

                <?php if (! empty($categoryNotFound)): ?>
                    <div class="qb-shop-empty">
                        <p>Category not found.</p>
                        <a href="<?= shop_query_url([], ['category']) ?>" class="qb-btn qb-btn-primary">Browse all products</a>
                    </div>
                <?php elseif (empty($products)): ?>
                    <div class="qb-shop-empty">
                        <p>No products found<?= ! empty($search) ? ' for "' . esc($search) . '"' : '' ?>.</p>
                        <a href="<?= shop_query_url([], ['category', 'q', 'min_price', 'max_price']) ?>" class="qb-btn qb-btn-primary">Clear filters</a>
                    </div>
                <?php else: ?>
                    <div
                        class="qb-shop-grid"
                        id="qb-shop-grid"
                        data-page="1"
                        data-per-page="<?= (int) ($perPage ?? 12) ?>"
                        data-total="<?= (int) ($totalProducts ?? count($products)) ?>"
                        data-has-more="<?= ! empty($hasMoreProducts) ? '1' : '0' ?>"
                        data-load-url="<?= site_url('shop/load-more') ?>"
                    >
                        <?= view('store/partials/shop_products', ['products' => $products]) ?>
                    </div>

                    <div class="qb-shop-infinite" id="qb-shop-infinite" <?= empty($hasMoreProducts) ? 'hidden' : '' ?>>
                        <div class="qb-shop-loader" id="qb-shop-loader" hidden>
                            <div class="qb-shop-loader-spinner" aria-hidden="true"></div>
                            <span>Loading more products...</span>
                        </div>
                        <div class="qb-shop-sentinel" id="qb-shop-sentinel" aria-hidden="true"></div>
                    </div>

                    <div class="qb-shop-end" id="qb-shop-end" <?= ! empty($hasMoreProducts) ? 'hidden' : '' ?>>
                        <span>You've seen all products</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="qb-shop-filter-overlay" id="qb-shop-filter-overlay" hidden></div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function ($) {
    var $layout = $('#qb-shop-layout');
    var $sidebar = $('#qb-shop-sidebar');
    var $overlay = $('#qb-shop-filter-overlay');

    function openFilters() {
        $sidebar.addClass('is-open');
        $overlay.removeAttr('hidden');
        $('body').addClass('qb-shop-filters-open');
    }

    function closeFilters() {
        $sidebar.removeClass('is-open');
        $overlay.attr('hidden', 'hidden');
        $('body').removeClass('qb-shop-filters-open');
    }

    $('#qb-shop-filter-toggle').on('click', openFilters);
    $('#qb-shop-filter-close, #qb-shop-filter-overlay').on('click', closeFilters);
    $(window).on('resize', function () {
        if (window.innerWidth >= 992) {
            closeFilters();
        }
    });

    $('#qb-shop-sidebar-collapse').on('click', function () {
        var collapsed = $layout.toggleClass('is-sidebar-collapsed').hasClass('is-sidebar-collapsed');
        $(this).attr('aria-expanded', collapsed ? 'false' : 'true');
    });

    $('.qb-filter-block-toggle').on('click', function () {
        var $block = $(this).closest('.qb-filter-block');
        var open = $block.toggleClass('is-open').hasClass('is-open');
        $(this).attr('aria-expanded', open ? 'true' : 'false');
    });

    $('.qb-filter-expand').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $parent = $(this).closest('.qb-filter-parent');
        var open = $parent.toggleClass('is-open').hasClass('is-open');
        $(this).attr('aria-expanded', open ? 'true' : 'false');
    });

    var $slider = $('.qb-range-slider');
    if ($slider.length) {
        var catalogMin = parseInt($slider.data('min'), 10) || 0;
        var catalogMax = parseInt($slider.data('max'), 10) || 100000;
        var $rangeMin = $slider.find('.qb-range-min');
        var $rangeMax = $slider.find('.qb-range-max');
        var $inputMin = $('#qb-price-min');
        var $inputMax = $('#qb-price-max');
        var $fill = $slider.find('.qb-range-fill');
        var $labelMin = $('#qb-price-min-label');
        var $labelMax = $('#qb-price-max-label');

        function clamp(val, min, max) {
            return Math.min(Math.max(val, min), max);
        }

        function formatPrice(val) {
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }

        function updateFill() {
            var minVal = parseInt($rangeMin.val(), 10);
            var maxVal = parseInt($rangeMax.val(), 10);
            if (minVal > maxVal) {
                if ($(document.activeElement).hasClass('qb-range-min')) {
                    maxVal = minVal;
                    $rangeMax.val(maxVal);
                } else {
                    minVal = maxVal;
                    $rangeMin.val(minVal);
                }
            }
            var span = catalogMax - catalogMin || 1;
            var left = ((minVal - catalogMin) / span) * 100;
            var right = ((catalogMax - maxVal) / span) * 100;
            $fill.css({ left: left + '%', right: right + '%' });
            $inputMin.val(minVal);
            $inputMax.val(maxVal);
            $labelMin.text(formatPrice(minVal));
            $labelMax.text(formatPrice(maxVal));
        }

        $rangeMin.on('input', updateFill);
        $rangeMax.on('input', updateFill);
        $inputMin.on('change', function () {
            var val = clamp(parseInt($(this).val(), 10) || catalogMin, catalogMin, catalogMax);
            $rangeMin.val(val);
            updateFill();
        });
        $inputMax.on('change', function () {
            var val = clamp(parseInt($(this).val(), 10) || catalogMax, catalogMin, catalogMax);
            $rangeMax.val(val);
            updateFill();
        });
        updateFill();
    }

    var $grid = $('#qb-shop-grid');
    if ($grid.length && $grid.data('has-more') === 1) {
        var loadUrl = $grid.data('load-url');
        var loading = false;
        var currentPage = parseInt($grid.data('page'), 10) || 1;
        var hasMore = true;
        var $loader = $('#qb-shop-loader');
        var $sentinel = $('#qb-shop-sentinel');
        var $infinite = $('#qb-shop-infinite');
        var $end = $('#qb-shop-end');
        var $loadedCount = $('#qb-shop-loaded-count');
        var $totalCount = $('#qb-shop-total-count');
        var $totalLabel = $('#qb-shop-total-label');

        function buildLoadUrl(page) {
            var params = new URLSearchParams(window.location.search);
            params.set('page', page);
            return loadUrl + '?' + params.toString();
        }

        function setLoader(active) {
            if (active) {
                $loader.removeAttr('hidden');
            } else {
                $loader.attr('hidden', 'hidden');
            }
        }

        function finishLoading() {
            hasMore = false;
            $grid.attr('data-has-more', '0');
            $infinite.attr('hidden', 'hidden');
            $end.removeAttr('hidden');
        }

        function loadNextPage() {
            if (loading || !hasMore) {
                return;
            }

            loading = true;
            setLoader(true);

            var nextPage = currentPage + 1;

            $.ajax({
                url: buildLoadUrl(nextPage),
                method: 'GET',
                dataType: 'json'
            }).done(function (res) {
                var data = (res && res.data) || {};
                var html = data.html || '';

                if (html) {
                    var $items = $(html).addClass('qb-shop-grid-item is-entering');
                    $grid.append($items);
                    requestAnimationFrame(function () {
                        $items.removeClass('is-entering');
                    });
                }

                currentPage = data.page || nextPage;
                $grid.attr('data-page', currentPage);

                if ($loadedCount.length && typeof data.loaded !== 'undefined') {
                    $loadedCount.text(data.loaded);
                }
                if ($totalCount.length && typeof data.total !== 'undefined') {
                    $totalCount.text('(' + data.total + ')');
                }
                if ($totalLabel.length && typeof data.total !== 'undefined') {
                    $totalLabel.text(data.total);
                }

                hasMore = !!data.hasMore;
                if (!hasMore) {
                    finishLoading();
                }
            }).fail(function () {
                hasMore = false;
                $infinite.attr('hidden', 'hidden');
            }).always(function () {
                loading = false;
                setLoader(false);
            });
        }

        if ('IntersectionObserver' in window && $sentinel.length) {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        loadNextPage();
                    }
                });
            }, {
                root: null,
                rootMargin: '240px 0px',
                threshold: 0
            });

            observer.observe($sentinel[0]);
        } else {
            $(window).on('scroll.shopInfinite', function () {
                if (!hasMore || loading) {
                    return;
                }
                var scrollBottom = $(window).scrollTop() + $(window).height();
                var triggerPoint = $sentinel.offset().top;
                if (scrollBottom >= triggerPoint - 200) {
                    loadNextPage();
                }
            });
        }
    } else if ($grid.length && $grid.data('has-more') !== 1) {
        $('#qb-shop-infinite').attr('hidden', 'hidden');
        $('#qb-shop-end').removeAttr('hidden');
    }
})(jQuery);
</script>
<?= $this->endSection() ?>
