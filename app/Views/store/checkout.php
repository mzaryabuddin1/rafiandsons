<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<main class="main qb-main">
    <div class="page-content qb-checkout-page">
        <div class="container">
            <h1 class="qb-page-title">Installment Checkout</h1>
            <p class="qb-page-sub">Submit a booking request. This is not automatic financing approval.</p>

            <div class="row">
                <div class="col-lg-7">
                    <form id="checkout-form" class="qb-form-card">
                        <h3>Your Details</h3>
                        <div class="row">
                            <div class="col-sm-6">
                                <label>Full Name *</label>
                                <input type="text" class="form-control" name="customer_name" required>
                            </div>
                            <div class="col-sm-6">
                                <label>Phone *</label>
                                <input type="text" class="form-control" name="customer_phone" required>
                            </div>
                            <div class="col-sm-6">
                                <label>Email</label>
                                <input type="email" class="form-control" name="customer_email">
                            </div>
                            <div class="col-sm-6">
                                <label>CNIC</label>
                                <input type="text" class="form-control" name="customer_cnic" placeholder="xxxxx-xxxxxxx-x">
                            </div>
                            <div class="col-sm-6">
                                <label>City</label>
                                <input type="text" class="form-control" name="customer_city">
                            </div>
                            <div class="col-sm-6">
                                <label>Address</label>
                                <input type="text" class="form-control" name="customer_address">
                            </div>
                            <div class="col-12">
                                <label>Notes</label>
                                <textarea class="form-control" name="notes" rows="3"></textarea>
                            </div>
                        </div>

                        <h3 class="mt-4">Select Installment Plan *</h3>
                        <div class="qb-plans">
                            <?php foreach ($plans as $i => $plan): ?>
                                <?php $active = ((int) ($selectedPlanId ?? 0) === (int) $plan['id'] || ($selectedPlanId === null && $i === 0)); ?>
                                <label class="qb-plan <?= $active ? 'is-active' : '' ?>">
                                    <input type="radio" name="installment_plan_id" value="<?= (int) $plan['id'] ?>" <?= $active ? 'checked' : '' ?>>
                                    <span class="qb-plan-body">
                                        <strong><?= esc($plan['name']) ?></strong>
                                        <span>Down payment: PKR <?= number_format((float) $plan['down_payment'], 0) ?></span>
                                        <span>Monthly: PKR <?= number_format((float) $plan['monthly_installment'], 0) ?> × <?= (int) $plan['months'] ?></span>
                                        <span>Processing: PKR <?= number_format((float) ($plan['processing_charges'] ?? 0), 0) ?></span>
                                        <span><b>Total payable: PKR <?= number_format((float) $plan['total_payable'], 0) ?></b></span>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <button type="submit" class="qb-btn qb-btn-primary qb-btn-block" id="checkout-btn">Submit Booking Request</button>
                    </form>
                </div>
                <aside class="col-lg-5">
                    <div class="qb-order-summary">
                        <h3>Your Order</h3>
                        <ul class="qb-order-list">
                            <?php foreach ($items as $item): ?>
                                <li>
                                    <span><?= esc($item['name']) ?> × <?= (int) $item['qty'] ?></span>
                                    <strong>PKR <?= number_format($item['price'] * $item['qty'], 0) ?></strong>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="qb-order-total">
                            <span>Cart Subtotal</span>
                            <strong>PKR <?= number_format($cartSubtotal, 0) ?></strong>
                        </div>
                        <p class="qb-disclaimer">Final installment totals follow the selected plan. Our team will confirm after review.</p>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).on('change', 'input[name="installment_plan_id"]', function () {
    $('.qb-plan').removeClass('is-active');
    $(this).closest('.qb-plan').addClass('is-active');
});
$('#checkout-form').on('submit', function (e) {
    e.preventDefault();
    var $btn = $('#checkout-btn');
    $btn.prop('disabled', true).text('Submitting...');
    StoreApp.request(STORE_BASE + '/checkout/place-order', 'POST', $(this).serialize())
        .done(function (res) {
            StoreApp.toast(res.message);
            window.location.href = res.data.redirect;
        })
        .always(function () {
            $btn.prop('disabled', false).text('Submit Booking Request');
        });
});
</script>
<?= $this->endSection() ?>
