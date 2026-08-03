<?php
$pageTitle = 'Shopping Cart';
$activePage = 'cart';
require __DIR__ . '/includes/header.php';

$cartItems = [
  ['Rafi White Trends Tee', 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=160&h=160&fit=crop', 21.00, 1],
  ["Dark Blue Women's Hat", 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=160&h=160&fit=crop', 118.00, 1],
];
$subtotal = array_sum(array_map(fn($i) => $i[2] * $i[3], $cartItems));
?>

<main>
  <div class="page-header">
    <div class="container">
      <h1>Shopping Cart</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Cart</li>
        </ol>
      </nav>
    </div>
  </div>

  <section class="container pb-5">
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="table-responsive">
          <table class="table cart-table align-middle">
            <thead>
              <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($cartItems as $item): ?>
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-3">
                    <img src="<?= $item[1] ?>" alt="">
                    <a href="product.php" class="fw-semibold text-dark"><?= $item[0] ?></a>
                  </div>
                </td>
                <td>$<?= number_format($item[2], 2) ?></td>
                <td>
                  <div class="input-group input-group-sm" style="width:120px">
                    <button class="btn btn-outline-secondary" type="button" data-qty="minus">−</button>
                    <input type="text" class="form-control qty-input" value="<?= $item[3] ?>">
                    <button class="btn btn-outline-secondary" type="button" data-qty="plus">+</button>
                  </div>
                </td>
                <td class="fw-semibold text-dark">$<?= number_format($item[2] * $item[3], 2) ?></td>
                <td><button class="btn btn-link text-muted"><i class="bi bi-x-lg"></i></button></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="d-flex flex-wrap gap-2 justify-content-between">
          <a href="shop.php" class="btn btn-rs-outline">Continue Shopping</a>
          <button class="btn btn-rs-dark">Update Cart</button>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="cart-totals">
          <h4>Cart Totals</h4>
          <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><strong>$<?= number_format($subtotal, 2) ?></strong></div>
          <div class="d-flex justify-content-between mb-2"><span>Shipping</span><span>Free</span></div>
          <hr>
          <div class="d-flex justify-content-between mb-3 fs-5"><span>Total</span><strong class="text-dark">$<?= number_format($subtotal, 2) ?></strong></div>
          <a href="checkout.php" class="btn btn-rs w-100">Proceed to Checkout</a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
