<?= $this->extend('vendor_panel/layout') ?>
<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;">
    <div class="col-lg-12">
        <h2>Dashboard</h2>
        <ol class="breadcrumb"><li>Welcome to your vendor panel</li></ol>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="ibox">
            <div class="ibox-title"><h5>Active Products</h5></div>
            <div class="ibox-content">
                <h1 class="no-margins"><?= (int) ($productCount ?? 0) ?></h1>
                <small>Tagged to your account</small>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="ibox">
            <div class="ibox-title"><h5>Orders</h5></div>
            <div class="ibox-content">
                <h1 class="no-margins"><?= (int) ($orderCount ?? 0) ?></h1>
                <small>Orders containing your products</small>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="ibox">
            <div class="ibox-title"><h5>Your Sales Total</h5></div>
            <div class="ibox-content">
                <h1 class="no-margins">PKR <?= number_format((float) ($salesTotal ?? 0), 0) ?></h1>
                <small>From your line items</small>
            </div>
        </div>
    </div>
</div>

<div class="ibox">
    <div class="ibox-title">
        <h5>Recent Orders</h5>
        <div class="ibox-tools">
            <a href="<?= site_url('vendor/orders') ?>" class="btn btn-primary btn-xs">View all</a>
        </div>
    </div>
    <div class="ibox-content">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Your Total</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($recentOrders)): ?>
                    <tr><td colspan="5" class="text-center text-muted">No orders yet</td></tr>
                <?php else: ?>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td><?= esc($order['order_number']) ?></td>
                            <td><?= esc($order['customer_name']) ?></td>
                            <td>PKR <?= number_format((float) $order['vendor_total'], 0) ?></td>
                            <td><span class="badge badge-primary"><?= esc($order['status_label']) ?></span></td>
                            <td><?= esc($order['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
