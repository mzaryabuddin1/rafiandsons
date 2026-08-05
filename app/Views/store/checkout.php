<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<main class="main">
    <div class="page-content pt-8 pb-10">
        <div class="container">
            <h2 class="title title-center mb-2">Installment Checkout</h2>
            <p class="text-center mb-6 text-muted">Submit a booking request. This is not automatic financing approval.</p>

            <div class="row">
                <div class="col-lg-7">
                    <form id="checkout-form" class="form">
                        <h3 class="title title-simple text-left mb-3">Your Details</h3>
                        <div class="row">
                            <div class="col-xs-6">
                                <label>Full Name *</label>
                                <input type="text" class="form-control" name="customer_name" required>
                            </div>
                            <div class="col-xs-6">
                                <label>Phone *</label>
                                <input type="text" class="form-control" name="customer_phone" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6">
                                <label>Email</label>
                                <input type="email" class="form-control" name="customer_email">
                            </div>
                            <div class="col-xs-6">
                                <label>CNIC</label>
                                <input type="text" class="form-control" name="customer_cnic" placeholder="xxxxx-xxxxxxx-x">
                            </div>
                        </div>
                        <label>City</label>
                        <input type="text" class="form-control" name="customer_city">
                        <label>Address</label>
                        <input type="text" class="form-control" name="customer_address">
                        <label>Notes</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>

                        <h3 class="title title-simple text-left mt-5 mb-3">Select Installment Plan *</h3>
                        <?php foreach ($plans as $i => $plan): ?>
                            <label class="plan-card <?= ((int) ($selectedPlanId ?? 0) === (int) $plan['id'] || ($selectedPlanId === null && $i === 0)) ? 'active' : '' ?>">
                                <input type="radio" name="installment_plan_id" value="<?= (int) $plan['id'] ?>"
                                    <?= ((int) ($selectedPlanId ?? 0) === (int) $plan['id'] || ($selectedPlanId === null && $i === 0)) ? 'checked' : '' ?>>
                                <strong><?= esc($plan['name']) ?></strong>
                                <ul class="mt-2">
                                    <li>Down payment: PKR <?= number_format((float) $plan['down_payment'], 0) ?></li>
                                    <li>Monthly: PKR <?= number_format((float) $plan['monthly_installment'], 0) ?> × <?= (int) $plan['months'] ?></li>
                                    <li>Processing: PKR <?= number_format((float) $plan['processing_charges'], 0) ?></li>
                                    <li><strong>Total payable: PKR <?= number_format((float) $plan['total_payable'], 0) ?></strong></li>
                                </ul>
                            </label>
                        <?php endforeach; ?>

                        <button type="submit" class="btn btn-primary btn-rounded btn-order" id="checkout-btn">Submit Booking Request</button>
                    </form>
                </div>
                <aside class="col-lg-5">
                    <div class="summary mb-4">
                        <h3 class="summary-title">Your Order</h3>
                        <table class="order-table">
                            <thead>
                            <tr><th>Product</th><th>Total</th></tr>
                            </thead>
                            <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td class="product-name"><?= esc($item['name']) ?> <span>× <?= (int) $item['qty'] ?></span></td>
                                    <td>PKR <?= number_format($item['price'] * $item['qty'], 0) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td><strong>Cart Subtotal</strong></td>
                                <td><strong>PKR <?= number_format($cartSubtotal, 0) ?></strong></td>
                            </tr>
                            </tbody>
                        </table>
                        <p class="mt-3" style="font-size:1.3rem;color:#666;">
                            Final installment totals follow the selected plan. Our team will confirm after review.
                        </p>
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
    $('.plan-card').removeClass('active');
    $(this).closest('.plan-card').addClass('active');
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
