<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<main class="main">
    <nav class="breadcrumb-nav">
        <div class="container">
            <ul class="breadcrumb">
                <li><a href="<?= site_url('/') ?>"><i class="d-icon-home"></i></a></li>
                <li>Shop<?= ! empty($activeCategory) ? ' / ' . esc($activeCategory['name']) : '' ?></li>
            </ul>
        </div>
    </nav>

    <div class="page-content mb-10">
        <div class="container">
            <div class="row gutter-lg">
                <aside class="col-lg-3 sidebar sidebar-fixed sticky-sidebar-wrapper">
                    <div class="sidebar-overlay"></div>
                    <a class="sidebar-close" href="#"><i class="d-icon-times"></i></a>
                    <a href="#" class="sidebar-toggle"><i class="fas fa-chevron-right"></i></a>
                    <div class="sidebar-content">
                        <div class="sticky-sidebar">
                            <div class="widget widget-collapsible">
                                <h3 class="widget-title">Categories</h3>
                                <ul class="widget-body filter-items">
                                    <li><a href="<?= site_url('shop') ?>">All Products</a></li>
                                    <?php foreach (($categoryTree ?? []) as $cat): ?>
                                        <li class="<?= (! empty($activeCategory) && (int) $activeCategory['id'] === (int) $cat['id']) ? 'active' : '' ?>">
                                            <a href="<?= site_url('shop?category=' . urlencode($cat['slug'])) ?>"><strong><?= esc($cat['name']) ?></strong></a>
                                        </li>
                                        <?php foreach ($cat['children'] ?? [] as $child): ?>
                                        <li class="<?= (! empty($activeCategory) && (int) $activeCategory['id'] === (int) $child['id']) ? 'active' : '' ?>" style="padding-left:1.2rem;">
                                            <a href="<?= site_url('shop?category=' . urlencode($child['slug'])) ?>"><?= esc($child['name']) ?></a>
                                        </li>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </aside>

                <div class="col-lg-9">
                    <div class="toolbox sticky-toolbox sticky-content fix-top">
                        <div class="toolbox-left">
                            <h4 class="title title-simple mb-0"><?= esc($pageTitle) ?> <small class="text-muted">(<?= count($products) ?>)</small></h4>
                        </div>
                        <div class="toolbox-right">
                            <form method="get" action="<?= site_url('shop') ?>" class="d-flex align-items-center">
                                <?php if (! empty($activeCategory)): ?>
                                    <input type="hidden" name="category" value="<?= esc($activeCategory['slug']) ?>">
                                <?php endif; ?>
                                <?php if (! empty($search)): ?>
                                    <input type="hidden" name="q" value="<?= esc($search) ?>">
                                <?php endif; ?>
                                <select class="form-control" name="sort" onchange="this.form.submit()">
                                    <option value="">Latest</option>
                                    <option value="price_asc" <?= ($sort ?? '') === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                                    <option value="price_desc" <?= ($sort ?? '') === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                                    <option value="name" <?= ($sort ?? '') === 'name' ? 'selected' : '' ?>>Name</option>
                                </select>
                            </form>
                        </div>
                    </div>

                    <div class="row cols-2 cols-sm-3 product-wrapper">
                        <?php if (empty($products)): ?>
                            <div class="col-12"><p>No products found.</p></div>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <div class="product-wrap mb-4">
                                    <?= view('store/partials/product_card', ['product' => $product, 'style' => 'qist']) ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection() ?>
