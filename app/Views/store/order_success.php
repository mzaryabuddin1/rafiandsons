<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<main class="main">
    <div class="page-content pt-10 pb-10">
        <div class="container text-center" style="max-width:720px;">
            <i class="fas fa-check-circle brand-icon-success" style="font-size:48px;"></i>
            <h2 class="title title-center mt-3 mb-3">Booking Request Submitted</h2>
            <p class="mb-2">Thank you, <?= esc($order['customer_name']) ?>.</p>
            <p class="mb-4">Your order number is <strong><?= esc($order['order_number']) ?></strong>.</p>
            <div class="store-alert text-left">
                <p class="mb-1"><strong>Plan:</strong> <?= esc($order['plan_name']) ?></p>
                <p class="mb-1"><strong>Down payment:</strong> PKR <?= number_format((float) $order['down_payment'], 0) ?></p>
                <p class="mb-1"><strong>Monthly:</strong> PKR <?= number_format((float) $order['monthly_installment'], 0) ?> × <?= (int) $order['months'] ?></p>
                <p class="mb-0"><strong>Total payable:</strong> PKR <?= number_format((float) $order['total_payable'], 0) ?></p>
            </div>
            <p class="text-muted mb-5">Our team will contact you shortly. This request is not automatic financing approval.</p>
            <a href="<?= site_url('shop') ?>" class="btn btn-primary btn-rounded">Continue Shopping</a>
        </div>
    </div>
</main>
<?= $this->endSection() ?>
