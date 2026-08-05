<?php
$siteName = $settings['site_name'] ?? 'Rafi & Sons';
$phone = $settings['contact_phone'] ?? '0300-0000000';
$whatsapp = $settings['whatsapp_number'] ?? $phone;
$menu = $activeMenu ?? '';
$categoryTree = $categoryTree ?? [];
$searchCategory = $searchCategory ?? '';
?>
<header class="qb-header">
    <div class="qb-header-main">
        <div class="container">
            <div class="qb-header-row">
                <a href="<?= site_url('home') ?>" class="qb-logo">
                    <img src="<?= base_url('assets/store/rafi-and-sons-logo.png') ?>" alt="<?= esc($siteName) ?>" height="56">
                </a>

                <form action="<?= site_url('search') ?>" method="get" class="qb-search-form" id="qb-search-form" role="search">
                    <div class="qb-search-inner">
                        <select name="category" class="qb-search-cat" id="qb-search-category" aria-label="Category">
                            <option value="">All</option>
                            <?php foreach ($categoryTree as $cat): ?>
                                <option value="<?= esc($cat['slug']) ?>" <?= $searchCategory === $cat['slug'] ? 'selected' : '' ?>>
                                    <?= esc($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="qb-search-field">
                            <input
                                type="search"
                                name="q"
                                id="qb-search-input"
                                value="<?= esc($search ?? '') ?>"
                                placeholder="Search products..."
                                class="qb-search-input"
                                autocomplete="off"
                                aria-label="Search products"
                                aria-expanded="false"
                                aria-controls="qb-search-dropdown"
                            >
                            <div class="qb-search-dropdown" id="qb-search-dropdown" hidden>
                                <div class="qb-search-dropdown-inner">
                                    <ul class="qb-search-results" id="qb-search-results" role="listbox"></ul>
                                    <a href="#" class="qb-search-view-all" id="qb-search-view-all" hidden>
                                        View all results
                                    </a>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="qb-search-btn">
                            <i class="d-icon-search d-none d-md-inline"></i>
                            <span>Search</span>
                        </button>
                    </div>
                </form>

                <div class="qb-header-actions">
                    <a href="tel:<?= esc(preg_replace('/\s+/', '', $phone)) ?>" class="qb-call-box">
                        <i class="fas fa-phone-alt"></i>
                        <div>
                            <small>Call now</small>
                            <strong><?= esc($phone) ?></strong>
                        </div>
                    </a>
                    <a href="<?= site_url('cart') ?>" class="qb-cart-box">
                        <i class="d-icon-bag"></i>
                        <span class="qb-cart-count" id="header-cart-count"><?= (int) ($cartCount ?? 0) ?></span>
                        <span class="d-none d-lg-inline">Cart</span>
                    </a>
                    <a href="<?= site_url('contact') ?>" class="qb-account-box d-none d-md-flex">
                        <i class="far fa-user"></i>
                        <span>Contact Us</span>
                    </a>
                    <button type="button" class="qb-mobile-toggle mobile-menu-toggle" aria-label="Menu">
                        <i class="d-icon-bars2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="qb-nav-bar">
        <div class="container">
            <nav class="qb-nav">
                <ul class="qb-nav-list">
                    <li class="<?= $menu === 'home' ? 'active' : '' ?>"><a href="<?= site_url('home') ?>">Home</a></li>
                    <li class="<?= $menu === 'shop' ? 'active' : '' ?>"><a href="<?= site_url('shop') ?>">Shop</a></li>
                    <li class="has-submenu">
                        <a href="#">Pages <i class="fas fa-chevron-down"></i></a>
                        <ul class="qb-submenu">
                            <li><a href="<?= site_url('about') ?>">About Us</a></li>
                            <li><a href="<?= site_url('faq') ?>">FAQs</a></li>
                            <li><a href="<?= site_url('privacy') ?>">Privacy Policy</a></li>
                            <li><a href="<?= site_url('terms') ?>">Terms &amp; Conditions</a></li>
                        </ul>
                    </li>
                    <li><a href="<?= site_url('installment-terms') ?>">Payment Method</a></li>
                    <li class="<?= $menu === 'contact' ? 'active' : '' ?>"><a href="<?= site_url('contact') ?>">Contact</a></li>
                </ul>
                <div class="qb-nav-right">
                    <a href="<?= site_url('contact') ?>">Track Your Order</a>
                </div>
            </nav>
        </div>
    </div>
</header>

<div class="mobile-menu-wrapper">
    <div class="mobile-menu-overlay"></div>
    <a class="mobile-menu-close" href="#"><i class="d-icon-times"></i></a>
    <div class="mobile-menu-container scrollable">
        <form action="<?= site_url('search') ?>" method="get" class="input-wrapper">
            <input type="search" class="form-control" name="q" placeholder="Search products..." required autocomplete="off">
            <button class="btn btn-search" type="submit"><i class="d-icon-search"></i></button>
        </form>
        <ul class="mobile-menu mmenu-anim">
            <li><a href="<?= site_url('home') ?>">Home</a></li>
            <li>
                <a href="<?= site_url('shop') ?>">Shop</a>
                <ul>
                    <?php foreach ($categoryTree as $cat): ?>
                        <li>
                            <a href="<?= site_url('shop?category=' . urlencode($cat['slug'])) ?>"><?= esc($cat['name']) ?></a>
                            <?php if (! empty($cat['children'])): ?>
                            <ul>
                                <?php foreach ($cat['children'] as $child): ?>
                                    <li><a href="<?= site_url('shop?category=' . urlencode($child['slug'])) ?>"><?= esc($child['name']) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <li><a href="<?= site_url('installment-terms') ?>">Payment Method</a></li>
            <li><a href="<?= site_url('about') ?>">About</a></li>
            <li><a href="<?= site_url('faq') ?>">FAQ</a></li>
            <li><a href="<?= site_url('contact') ?>">Contact</a></li>
            <li><a href="<?= site_url('cart') ?>">Cart</a></li>
        </ul>
    </div>
</div>

<?php if ($whatsapp): ?>
<a href="https://wa.me/<?= esc(preg_replace('/\D+/', '', $whatsapp)) ?>" class="qb-whatsapp-float" target="_blank" rel="noopener" title="Chat on WhatsApp">
    <i class="fab fa-whatsapp"></i>
    <span>Chat With Us</span>
</a>
<?php endif; ?>
