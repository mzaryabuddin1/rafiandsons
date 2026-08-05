<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<main class="main qb-main">
    <div class="page-content qb-cart-page">
        <div class="container">
            <h1 class="qb-page-title">Shopping Cart</h1>
            <?php if (empty($items)): ?>
                <div class="qb-form-card text-center">
                    <p>Your cart is empty.</p>
                    <a href="<?= site_url('shop') ?>" class="qb-btn qb-btn-primary">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="qb-form-card">
                            <table class="table qb-cart-table">
                                <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Plan</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr data-product-id="<?= (int) $item['product_id'] ?>">
                                        <td>
                                            <div class="qb-cart-product">
                                                <img src="<?= base_url($item['image']) ?>" alt="<?= esc($item['name']) ?>" width="64" height="64">
                                                <a href="<?= site_url('product/' . $item['slug']) ?>"><?= esc($item['name']) ?></a>
                                            </div>
                                        </td>
                                        <td>PKR <?= number_format($item['price'], 0) ?></td>
                                        <td>
                                            <input type="number" class="form-control cart-qty qb-qty" min="1" value="<?= (int) $item['qty'] ?>">
                                        </td>
                                        <td>
                                            <select class="form-control cart-plan">
                                                <option value="">Select plan</option>
                                                <?php foreach (($plansByProduct[$item['product_id']] ?? []) as $plan): ?>
                                                    <option value="<?= (int) $plan['id'] ?>" <?= ((int) ($item['plan_id'] ?? 0) === (int) $plan['id']) ? 'selected' : '' ?>>
                                                        <?= esc($plan['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td class="line-total">PKR <?= number_format($item['price'] * $item['qty'], 0) ?></td>
                                        <td>
                                            <a href="#" class="cart-remove" title="Remove">&times;</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <aside class="col-lg-4">
                        <div class="qb-order-summary">
                            <h3>Cart Totals</h3>
                            <div class="qb-order-total">
                                <span>Subtotal</span>
                                <strong id="cart-subtotal-label">PKR <?= number_format($cartSubtotal, 0) ?></strong>
                            </div>
                            <a href="<?= site_url('checkout') ?>" class="qb-btn qb-btn-primary qb-btn-block">Proceed to Checkout</a>
                        </div>
                    </aside>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).on('change', '.cart-qty', function () {
    var $row = $(this).closest('tr');
    StoreApp.request(STORE_BASE + '/cart/update', 'POST', {
        product_id: $row.data('product-id'),
        qty: $(this).val()
    }).done(function (res) {
        StoreApp.toast(res.message);
        location.reload();
    });
});
$(document).on('change', '.cart-plan', function () {
    var $row = $(this).closest('tr');
    StoreApp.request(STORE_BASE + '/cart/set-plan', 'POST', {
        product_id: $row.data('product-id'),
        plan_id: $(this).val()
    }).done(function (res) { StoreApp.toast(res.message); });
});
$(document).on('click', '.cart-remove', function (e) {
    e.preventDefault();
    var $row = $(this).closest('tr');
    StoreApp.request(STORE_BASE + '/cart/remove', 'POST', {
        product_id: $row.data('product-id')
    }).done(function () { location.reload(); });
});
</script>
<?= $this->endSection() ?>
