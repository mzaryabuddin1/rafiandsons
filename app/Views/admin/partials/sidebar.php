<?php
/** @var \App\Libraries\AdminAuth $auth */
$user = $authUser ?? null;
$menu = $activeMenu ?? '';
?>
<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <div class="sidebar-brand">
                        Rafi &amp; Sons
                        <small>Admin Panel</small>
                    </div>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="block m-t-xs font-bold"><?= esc($user['name'] ?? 'Admin') ?></span>
                        <span class="text-muted text-xs block"><?= esc($user['role_name'] ?? '') ?> <b class="caret"></b></span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a class="dropdown-item admin-logout-btn" href="#">Logout</a></li>
                    </ul>
                </div>
                <div class="logo-element">RS</div>
            </li>

            <?php if ($auth->can('dashboard.view')): ?>
            <li class="<?= $menu === 'dashboard' ? 'active' : '' ?>">
                <a href="<?= site_url('admin/dashboard') ?>"><i class="fa fa-th-large"></i> <span class="nav-label">Dashboard</span></a>
            </li>
            <?php endif; ?>

            <?php if ($auth->canAny(['categories.view', 'products.view', 'installment_plans.view'])): ?>
            <li class="<?= in_array($menu, ['categories', 'products', 'plans'], true) ? 'active' : '' ?>">
                <a href="#"><i class="fa fa-cube"></i> <span class="nav-label">Catalog</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse <?= in_array($menu, ['categories', 'products', 'plans'], true) ? 'in' : '' ?>">
                    <?php if ($auth->can('categories.view')): ?>
                    <li class="<?= $menu === 'categories' ? 'active' : '' ?>"><a href="<?= site_url('admin/categories') ?>">Categories</a></li>
                    <?php endif; ?>
                    <?php if ($auth->can('products.view')): ?>
                    <li class="<?= $menu === 'products' ? 'active' : '' ?>"><a href="<?= site_url('admin/products') ?>">Products</a></li>
                    <?php endif; ?>
                    <?php if ($auth->can('installment_plans.view')): ?>
                    <li class="<?= $menu === 'plans' ? 'active' : '' ?>"><a href="<?= site_url('admin/installment-plans') ?>">Installment Plans</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>

            <?php if ($auth->can('orders.view')): ?>
            <li class="<?= $menu === 'orders' ? 'active' : '' ?>">
                <a href="<?= site_url('admin/orders') ?>"><i class="fa fa-shopping-cart"></i> <span class="nav-label">Orders</span></a>
            </li>
            <?php endif; ?>

            <?php if ($auth->can('customers.view')): ?>
            <li class="<?= $menu === 'customers' ? 'active' : '' ?>">
                <a href="<?= site_url('admin/customers') ?>"><i class="fa fa-users"></i> <span class="nav-label">Customers</span></a>
            </li>
            <?php endif; ?>

            <?php if ($auth->canAny(['contents.view', 'banners.view', 'settings.view'])): ?>
            <li class="<?= in_array($menu, ['contents', 'banners', 'settings'], true) ? 'active' : '' ?>">
                <a href="#"><i class="fa fa-desktop"></i> <span class="nav-label">Website</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse <?= in_array($menu, ['contents', 'banners', 'settings'], true) ? 'in' : '' ?>">
                    <?php if ($auth->can('contents.view')): ?>
                    <li class="<?= $menu === 'contents' ? 'active' : '' ?>"><a href="<?= site_url('admin/contents') ?>">Contents</a></li>
                    <?php endif; ?>
                    <?php if ($auth->can('banners.view')): ?>
                    <li class="<?= $menu === 'banners' ? 'active' : '' ?>"><a href="<?= site_url('admin/banners') ?>">Homepage Banners</a></li>
                    <?php endif; ?>
                    <?php if ($auth->can('settings.view')): ?>
                    <li class="<?= $menu === 'settings' ? 'active' : '' ?>"><a href="<?= site_url('admin/settings') ?>">Settings</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>

            <?php if ($auth->canAny(['users.view', 'roles.view'])): ?>
            <li class="<?= in_array($menu, ['users', 'roles'], true) ? 'active' : '' ?>">
                <a href="#"><i class="fa fa-lock"></i> <span class="nav-label">Access Control</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse <?= in_array($menu, ['users', 'roles'], true) ? 'in' : '' ?>">
                    <?php if ($auth->can('users.view')): ?>
                    <li class="<?= $menu === 'users' ? 'active' : '' ?>"><a href="<?= site_url('admin/users') ?>">Users</a></li>
                    <?php endif; ?>
                    <?php if ($auth->can('roles.view')): ?>
                    <li class="<?= $menu === 'roles' ? 'active' : '' ?>"><a href="<?= site_url('admin/roles') ?>">Roles</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
