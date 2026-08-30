<?= $this->extend('store/layout') ?>
<?php $vendorUser = $vendorUser ?? []; ?>
<?= $this->section('content') ?>
<main class="main qb-main">
    <div class="page-content">
        <div class="container">
            <div class="qb-vendor-portal-head">
                <div>
                    <h1 class="qb-page-title">Vendor Orders</h1>
                    <p class="qb-page-sub mb-0">
                        Signed in as <strong><?= esc($vendorUser['business_name'] ?? $vendorUser['contact_name'] ?? 'Vendor') ?></strong>
                    </p>
                </div>
                <button type="button" class="qb-btn qb-btn-outline" id="vendor-logout-btn">Sign Out</button>
            </div>

            <?php if (empty($orders)): ?>
                <div class="qb-form-card">
                    <p class="mb-0 text-muted">No orders for your products yet.</p>
                </div>
            <?php else: ?>
                <div class="qb-orders-wrap">
                    <div class="qb-orders-head">
                        <div>Order #</div>
                        <div>Customer</div>
                        <div>Your Items</div>
                        <div>Your Total</div>
                        <div>Status</div>
                    </div>
                    <?php foreach ($orders as $order): ?>
                        <div class="qb-orders-row qb-vendor-order-card">
                            <div class="qb-orders-cell" data-label="Order #">
                                <strong><?= esc($order['order_number']) ?></strong>
                                <div class="text-muted small"><?= esc($order['created_at'] ?? '') ?></div>
                            </div>
                            <div class="qb-orders-cell" data-label="Customer">
                                <?= esc($order['customer_name']) ?><br>
                                <span class="text-muted"><?= esc($order['customer_phone']) ?></span>
                            </div>
                            <div class="qb-orders-cell" data-label="Your Items">
                                <?php foreach ($order['vendor_items'] as $item): ?>
                                    <div><?= esc($item['product_name']) ?> × <?= (int) $item['quantity'] ?></div>
                                <?php endforeach; ?>
                            </div>
                            <div class="qb-orders-cell" data-label="Your Total">
                                <strong>PKR <?= number_format((float) $order['vendor_total'], 0) ?></strong>
                            </div>
                            <div class="qb-orders-cell" data-label="Status">
                                <span class="badge badge-primary"><?= esc($order['status_label'] ?? $order['status']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$('#vendor-logout-btn').on('click', function () {
    StoreApp.request(STORE_BASE + '/vendor/logout', 'POST', {}).done(function (res) {
        window.location.href = res.data.redirect;
    });
});
</script>
<?= $this->endSection() ?>
