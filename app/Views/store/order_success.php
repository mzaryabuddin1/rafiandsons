<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<?php
$isInstallment = in_array($order['payment_type'] ?? '', ['installment', 'mixed'], true);
?>
<main class="main qb-main">
    <div class="page-content qb-success-page pt-10 pb-10">
        <div class="container" style="max-width:720px;">
            <div class="qb-success-card text-center">
                <div class="qb-success-icon"><i class="fas fa-check-circle"></i></div>
                <h2 class="qb-success-title">
                    <?= $isInstallment ? 'Booking Request Submitted' : 'Order Placed Successfully' ?>
                </h2>
                <p class="qb-success-lead">Thank you, <?= esc($order['customer_name']) ?>.</p>
                <p class="mb-4">Order number: <strong><?= esc($order['order_number']) ?></strong></p>

                <div class="qb-success-details text-left">
                    <h3>Payment Summary</h3>
                    <?php foreach ($orderItems ?? [] as $item): ?>
                        <?php $itemInst = ($item['payment_type'] ?? '') === 'installment'; ?>
                        <div class="qb-success-item">
                            <strong><?= esc($item['product_name']) ?> × <?= (int) $item['quantity'] ?></strong>
                            <?php if ($itemInst): ?>
                                <p>Advance paid now: PKR <?= number_format((float) $item['down_payment'], 0) ?></p>
                                <p>Monthly: PKR <?= number_format((float) $item['monthly_installment'], 0) ?> × <?= (int) $item['months'] ?> months</p>
                                <p>Total: PKR <?= number_format((float) $item['total_payable'], 0) ?></p>
                            <?php else: ?>
                                <p>Cash: PKR <?= number_format((float) ($item['cash_price'] ?? $item['unit_price']), 0) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <div class="qb-success-totals">
                        <p><strong>Due now:</strong> PKR <?= number_format((float) $order['subtotal'], 0) ?></p>
                        <p><strong>Total order value:</strong> PKR <?= number_format((float) $order['total_payable'], 0) ?></p>
                    </div>
                </div>

                <?php if (! empty($order['customer_email'])): ?>
                    <p class="text-muted">A confirmation email has been sent to <?= esc($order['customer_email']) ?>.</p>
                <?php endif; ?>
                <p class="text-muted mb-5">Our team will contact you shortly<?= $isInstallment ? ' to verify your installment booking' : '' ?>.</p>
                <a href="<?= site_url('shop') ?>" class="qb-btn qb-btn-primary">Continue Shopping</a>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection() ?>
