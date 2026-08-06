<?php
$auth = new \App\Libraries\StoreAuth();
$avatar = $auth->profileImageUrl($storeCustomer ?? null);
?>
<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<main class="main qb-main">
    <div class="page-content qb-account-page">
        <div class="container">
            <div class="row">
                <aside class="col-lg-3">
                    <div class="qb-account-sidebar">
                        <div class="qb-account-user">
                            <img src="<?= esc($avatar) ?>" alt="Profile" class="qb-account-avatar">
                            <strong><?= esc($storeCustomer['name'] ?? '') ?></strong>
                            <small><?= esc($storeCustomer['phone'] ?? '') ?></small>
                        </div>
                        <nav class="qb-account-nav">
                            <a href="<?= site_url('account/profile') ?>">Profile Settings</a>
                            <a href="<?= site_url('account/orders') ?>" class="active">My Orders</a>
                            <a href="#" id="account-logout-link">Sign Out</a>
                        </nav>
                    </div>
                </aside>
                <div class="col-lg-9">
                    <div class="qb-form-card">
                        <h1 class="qb-page-title">My Orders</h1>
                        <p class="qb-page-sub">Track your recent orders and booking requests.</p>

                        <?php if (empty($orders)): ?>
                            <div class="qb-empty-state">
                                <p>You haven't placed any orders yet.</p>
                                <a href="<?= site_url('shop') ?>" class="qb-btn qb-btn-primary">Start Shopping</a>
                            </div>
                        <?php else: ?>
                            <div class="qb-orders-wrap">
                                <div class="qb-orders-table" role="table" aria-label="Order history">
                                    <div class="qb-orders-head" role="row">
                                        <span role="columnheader">Order #</span>
                                        <span role="columnheader">Date</span>
                                        <span role="columnheader">Total</span>
                                        <span role="columnheader">Status</span>
                                        <span role="columnheader">Payment</span>
                                    </div>
                                    <?php foreach ($orders as $order): ?>
                                        <?php
                                        $statusKey = $order['status'] ?? 'new';
                                        $statusLabel = \App\Models\OrderModel::STATUSES[$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey));
                                        ?>
                                        <div class="qb-orders-row" role="row">
                                            <span class="qb-orders-cell qb-orders-cell--id" role="cell" data-label="Order #">
                                                <strong><?= esc($order['order_number']) ?></strong>
                                            </span>
                                            <span class="qb-orders-cell" role="cell" data-label="Date">
                                                <?= esc(date('d M Y', strtotime($order['created_at'] ?? 'now'))) ?>
                                            </span>
                                            <span class="qb-orders-cell qb-orders-cell--total" role="cell" data-label="Total">
                                                PKR <?= number_format((float) ($order['total_payable'] ?? $order['subtotal'] ?? 0), 0) ?>
                                            </span>
                                            <span class="qb-orders-cell" role="cell" data-label="Status">
                                                <span class="qb-status-badge"><?= esc($statusLabel) ?></span>
                                            </span>
                                            <span class="qb-orders-cell" role="cell" data-label="Payment">
                                                <?= esc(ucfirst($order['payment_type'] ?? 'cash')) ?>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$('#account-logout-link').on('click', function (e) {
    e.preventDefault();
    StoreApp.request(STORE_BASE + '/account/logout', 'POST', {})
        .done(function (res) {
            window.location.href = res.data.redirect;
        });
});
</script>
<?= $this->endSection() ?>
