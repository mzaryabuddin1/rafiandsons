<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<main class="main cart">
    <div class="page-content pt-8 pb-10">
        <div class="container">
            <h2 class="title title-center mb-6">Shopping Cart</h2>
            <?php if (empty($items)): ?>
                <div class="text-center">
                    <p>Your cart is empty.</p>
                    <a href="<?= site_url('shop') ?>" class="btn btn-primary btn-rounded">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-lg-8">
                        <table class="shop-table cart-table">
                            <thead>
                            <tr>
                                <th><span>Product</span></th>
                                <th><span>Price</span></th>
                                <th><span>Quantity</span></th>
                                <th><span>Plan</span></th>
                                <th><span>Subtotal</span></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr data-product-id="<?= (int) $item['product_id'] ?>">
                                    <td class="product-thumbnail">
                                        <div class="p-relative">
                                            <a href="<?= site_url('product/' . $item['slug']) ?>">
                                                <figure>
                                                    <img src="<?= base_url($item['image']) ?>" width="100" height="100" alt="<?= esc($item['name']) ?>">
                                                </figure>
                                            </a>
                                        </div>
                                        <a href="<?= site_url('product/' . $item['slug']) ?>" class="product-name"><?= esc($item['name']) ?></a>
                                    </td>
                                    <td>PKR <?= number_format($item['price'], 0) ?></td>
                                    <td>
                                        <input type="number" class="form-control cart-qty" min="1" value="<?= (int) $item['qty'] ?>" style="width:70px;">
                                    </td>
                                    <td>
                                        <select class="form-control cart-plan" style="min-width:160px;">
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
                                        <a href="#" class="btn btn-link btn-close cart-remove"><i class="fas fa-times"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <aside class="col-lg-4 sticky-sidebar-wrapper">
                        <div class="summary mb-4">
                            <h3 class="summary-title">Cart Totals</h3>
                            <table class="shipping">
                                <tr>
                                    <td>Subtotal</td>
                                    <td id="cart-subtotal-label"><strong>PKR <?= number_format($cartSubtotal, 0) ?></strong></td>
                                </tr>
                            </table>
                            <a href="<?= site_url('checkout') ?>" class="btn btn-dark btn-rounded btn-checkout">Proceed to Checkout</a>
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
