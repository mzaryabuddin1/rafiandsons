<?= $this->extend('store/layout') ?>
<?php
$customer = $checkoutCustomer ?? [];
?>
<?= $this->section('content') ?>
<main class="main qb-main">
    <div class="page-content qb-checkout-page">
        <div class="container">
            <h1 class="qb-page-title">Checkout</h1>
            <p class="qb-page-sub">
                <?php if ($hasInstallment ?? false): ?>
                    Review your details and installment breakdown. Our team will verify your booking.
                <?php else: ?>
                    Complete your order. Our team will contact you to confirm.
                <?php endif; ?>
            </p>
            <?php if (! empty($isLoggedIn)): ?>
                <p class="qb-checkout-signed-in"><i class="fas fa-check-circle"></i> Signed in as <strong><?= esc($customer['name'] ?? '') ?></strong>. Your saved details are pre-filled below.</p>
            <?php else: ?>
                <p class="qb-checkout-guest-tip">Have an account? <a href="<?= site_url('account/login?redirect=' . urlencode(current_url())) ?>">Sign in</a> to auto-fill your details.</p>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-7">
                    <form id="checkout-form" class="qb-form-card">
                        <h3>Your Details</h3>
                        <div class="row">
                            <div class="col-sm-6">
                                <label>Full Name *</label>
                                <input type="text" class="form-control" name="customer_name" required value="<?= esc($customer['name'] ?? '') ?>">
                            </div>
                            <div class="col-sm-6">
                                <label>Phone *</label>
                                <input type="text" class="form-control" name="customer_phone" required value="<?= esc($customer['phone'] ?? '') ?>">
                            </div>
                            <div class="col-sm-6">
                                <label>Email</label>
                                <input type="email" class="form-control" name="customer_email" placeholder="For order confirmation" value="<?= esc($customer['email'] ?? '') ?>">
                            </div>
                            <div class="col-sm-6">
                                <label>CNIC</label>
                                <input type="text" class="form-control" name="customer_cnic" placeholder="xxxxx-xxxxxxx-x" value="<?= esc($customer['cnic'] ?? '') ?>">
                            </div>
                            <div class="col-sm-6">
                                <label>City</label>
                                <input type="text" class="form-control" name="customer_city" value="<?= esc($customer['city'] ?? '') ?>">
                            </div>
                            <div class="col-sm-6">
                                <label>Address</label>
                                <input type="text" class="form-control" name="customer_address" value="<?= esc($customer['address'] ?? '') ?>">
                            </div>
                            <div class="col-12">
                                <label>Notes</label>
                                <textarea class="form-control" name="notes" rows="3" placeholder="Any special instructions"></textarea>
                            </div>
                        </div>

                        <button type="submit" class="qb-btn qb-btn-primary qb-btn-block mt-4" id="checkout-btn">
                            <?= ($hasInstallment ?? false) ? 'Submit Booking Request' : 'Place Order' ?>
                        </button>
                    </form>
                </div>
                <aside class="col-lg-5">
                    <div class="qb-order-summary qb-checkout-summary">
                        <h3>Order Summary</h3>
                        <ul class="qb-order-list">
                            <?php foreach ($items as $item): ?>
                                <?php $isInst = ($item['payment_type'] ?? '') === 'installment'; ?>
                                <li class="qb-checkout-item">
                                    <div class="qb-checkout-item-head">
                                        <span><?= esc($item['name']) ?><?= $isInst && ! empty($item['plan_name']) ? ' · ' . esc($item['plan_name']) : '' ?> × <?= (int) $item['qty'] ?></span>
                                        <strong>PKR <?= number_format($item['price'] * $item['qty'], 0) ?></strong>
                                    </div>
                                    <?php if ($isInst): ?>
                                    <div class="qb-checkout-item-detail">
                                        <span class="qb-checkout-badge">Installment</span>
                                        <p>Advance (due now): <strong>PKR <?= number_format((float) $item['down_payment'], 0) ?></strong></p>
                                        <p>Monthly: <strong>PKR <?= number_format((float) $item['monthly_installment'], 0) ?></strong> × <?= (int) $item['months'] ?> months</p>
                                        <p>Total payable: <strong>PKR <?= number_format((float) $item['total_payable'], 0) ?></strong></p>
                                    </div>
                                    <?php else: ?>
                                    <div class="qb-checkout-item-detail">
                                        <span class="qb-checkout-badge qb-checkout-badge--cash">Cash</span>
                                        <p>Full cash price</p>
                                    </div>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="qb-checkout-totals">
                            <div class="qb-checkout-total-row qb-checkout-total-row--highlight">
                                <span>Due now</span>
                                <strong>PKR <?= number_format($cartSubtotal, 0) ?></strong>
                            </div>
                            <div class="qb-checkout-total-row">
                                <span>Total order value</span>
                                <strong>PKR <?= number_format($cartGrandTotal ?? $cartSubtotal, 0) ?></strong>
                            </div>
                        </div>

                        <?php if ($hasInstallment ?? false): ?>
                        <p class="qb-disclaimer">Monthly installments start after verification. This is not automatic financing approval.</p>
                        <?php endif; ?>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$('#checkout-form').on('submit', function (e) {
    e.preventDefault();
    var $btn = $('#checkout-btn');
    var btnText = $btn.text();
    $btn.prop('disabled', true).text('Submitting...');
    StoreApp.request(STORE_BASE + '/checkout/place-order', 'POST', $(this).serialize())
        .done(function (res) {
            StoreApp.toast(res.message);
            window.location.href = res.data.redirect;
        })
        .always(function () {
            $btn.prop('disabled', false).text(btnText);
        });
});
</script>
<?= $this->endSection() ?>
