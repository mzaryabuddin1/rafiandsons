<?php
/** @var \App\Libraries\VendorAuth $auth */
$user = $authUser ?? null;
$menu = $activeMenu ?? '';
?>
<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <img src="<?= base_url('assets/admin/rafi-and-sons-logo.png') ?>" alt="Rafi &amp; Sons" class="admin-logo">
                    <div class="sidebar-brand">
                        Vendor Panel
                        <small><?= esc($user['business_name'] ?? 'Vendor') ?></small>
                    </div>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="block m-t-xs font-bold"><?= esc($user['contact_name'] ?? 'Vendor') ?></span>
                        <span class="text-muted text-xs block"><?= esc($user['email'] ?? '') ?> <b class="caret"></b></span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a class="dropdown-item vendor-logout-btn" href="#">Logout</a></li>
                    </ul>
                </div>
                <div class="logo-element">
                    <img src="<?= base_url('assets/admin/rafi-and-sons-logo.png') ?>" alt="Rafi &amp; Sons" class="admin-logo-mini">
                </div>
            </li>

            <li class="<?= $menu === 'dashboard' ? 'active' : '' ?>">
                <a href="<?= site_url('vendor/dashboard') ?>"><i class="fa fa-th-large"></i> <span class="nav-label">Dashboard</span></a>
            </li>
            <li class="<?= $menu === 'orders' ? 'active' : '' ?>">
                <a href="<?= site_url('vendor/orders') ?>"><i class="fa fa-shopping-cart"></i> <span class="nav-label">Orders</span></a>
            </li>
        </ul>
    </div>
</nav>
