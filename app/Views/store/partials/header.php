<?php
$siteName = $settings['site_name'] ?? 'Rafi & Sons';
$phone = $settings['contact_phone'] ?? '0(800) 123-456';
$menu = $activeMenu ?? '';
$showFixedCats = ! empty($showFixedCats);
$categoryTree = $categoryTree ?? [];
$catIcons = [
    'electronics'     => 'd-icon-camera1',
    'home-appliances' => 'd-icon-cook',
    'computers'       => 'd-icon-desktop',
    'fashion'         => 'd-icon-t-shirt1',
    'beauty'          => 'd-icon-heart',
    'furniture'       => 'd-icon-cook',
];
?>
<header class="header">
    <div class="header-top">
        <div class="container">
            <div class="header-left">
                <p class="welcome-msg pb-2">Welcome to <?= esc($siteName) ?> — installment shopping made easy!</p>
            </div>
            <div class="header-right">
                <div class="dropdown dropdown-expanded">
                    <a href="#dropdown">Links</a>
                    <ul class="dropdown-box">
                        <li><a href="<?= site_url('about') ?>">About</a></li>
                        <li><a href="<?= site_url('faq') ?>">FAQ</a></li>
                        <li><a href="<?= site_url('installment-terms') ?>">Installment Terms</a></li>
                        <li><a href="<?= site_url('contact') ?>">Contact</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="header-middle sticky-header fix-top sticky-content">
        <div class="container">
            <div class="header-left">
                <a href="#" class="mobile-menu-toggle"><i class="d-icon-bars2"></i></a>
                <a href="<?= site_url('home') ?>" class="logo">
                    <img src="<?= base_url('theme/images/demos/demo22/logo.png') ?>" alt="<?= esc($siteName) ?>" width="154" height="43">
                </a>
                <div class="header-search hs-simple">
                    <form action="<?= site_url('shop') ?>" method="get" class="input-wrapper">
                        <input type="text" class="form-control" name="q" value="<?= esc($search ?? '') ?>" placeholder="Search..." required>
                        <button class="btn btn-search" type="submit" title="submit-button"><i class="d-icon-search"></i></button>
                    </form>
                </div>
            </div>
            <div class="header-right">
                <a href="tel:<?= esc(preg_replace('/\s+/', '', $phone)) ?>" class="icon-box icon-box-side">
                    <div class="icon-box-icon"><i class="d-icon-phone"></i></div>
                    <div class="icon-box-content d-lg-show">
                        <h4 class="icon-box-title">Call Us Now:</h4>
                        <p><?= esc($phone) ?></p>
                    </div>
                </a>
                <span class="divider"></span>
                <a href="<?= site_url('shop') ?>" class="wishlist" title="Shop">
                    <i class="d-icon-heart"></i>
                </a>
                <span class="divider"></span>
                <div class="dropdown cart-dropdown type2 off-canvas mr-0 mr-lg-2">
                    <a href="<?= site_url('cart') ?>" class="cart-toggle label-block link">
                        <div class="cart-label d-lg-show ls-normal">
                            <span class="cart-name ls-m">Shopping Cart:</span>
                            <span class="cart-price" id="header-cart-subtotal">PKR <?= number_format($cartSubtotal ?? 0, 0) ?></span>
                        </div>
                        <i class="d-icon-bag"><span class="cart-count" id="header-cart-count"><?= (int) ($cartCount ?? 0) ?></span></i>
                    </a>
                </div>
                <div class="header-search hs-toggle mobile-search">
                    <a href="#" class="search-toggle"><i class="d-icon-search"></i></a>
                    <form action="<?= site_url('shop') ?>" method="get" class="input-wrapper">
                        <input type="text" class="form-control" name="q" placeholder="Search your keyword..." required>
                        <button class="btn btn-search" type="submit"><i class="d-icon-search"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="header-bottom has-dropdown pb-0">
        <div class="container d-flex align-items-center">
            <div class="dropdown category-dropdown has-border<?= $showFixedCats ? ' fixed' : '' ?>">
                <a href="#" class="text-white font-weight-semi-bold category-toggle">
                    <i class="d-icon-bars2"></i><span>Shop By Categories</span>
                </a>
                <div class="dropdown-box">
                    <ul class="menu vertical-menu category-menu">
                        <li><a href="<?= site_url('shop') ?>" class="menu-title">Browse Our Categories</a></li>
                        <?php foreach ($categoryTree as $cat): ?>
                            <?php $icon = $catIcons[$cat['slug']] ?? 'd-icon-category'; ?>
                            <li class="<?= ! empty($cat['children']) ? 'submenu' : '' ?>">
                                <a href="<?= site_url('shop?category=' . urlencode($cat['slug'])) ?>">
                                    <i class="<?= esc($icon) ?>"></i><?= esc($cat['name']) ?>
                                </a>
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
                </div>
            </div>
            <nav class="main-nav ml-4">
                <ul class="menu">
                    <li class="<?= $menu === 'home' ? 'active' : '' ?>"><a href="<?= site_url('home') ?>">Home</a></li>
                    <li class="<?= $menu === 'shop' ? 'active' : '' ?>">
                        <a href="<?= site_url('shop') ?>">Categories</a>
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
                    <li><a href="<?= site_url('shop') ?>">Products</a></li>
                    <li class="<?= $menu === 'about' ? 'active' : '' ?>"><a href="<?= site_url('about') ?>">About</a></li>
                    <li class="<?= $menu === 'faq' ? 'active' : '' ?>"><a href="<?= site_url('faq') ?>">FAQ</a></li>
                    <li class="<?= $menu === 'contact' ? 'active' : '' ?>"><a href="<?= site_url('contact') ?>">Contact</a></li>
                </ul>
            </nav>
        </div>
    </div>
</header>

<div class="mobile-menu-wrapper">
    <div class="mobile-menu-overlay"></div>
    <a class="mobile-menu-close" href="#"><i class="d-icon-times"></i></a>
    <div class="mobile-menu-container scrollable">
        <form action="<?= site_url('shop') ?>" method="get" class="input-wrapper">
            <input type="text" class="form-control" name="q" placeholder="Search..." required>
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
            <li><a href="<?= site_url('about') ?>">About</a></li>
            <li><a href="<?= site_url('faq') ?>">FAQ</a></li>
            <li><a href="<?= site_url('contact') ?>">Contact</a></li>
            <li><a href="<?= site_url('cart') ?>">Cart</a></li>
        </ul>
    </div>
</div>
