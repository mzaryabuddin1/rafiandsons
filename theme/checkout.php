<?php
$pageTitle = 'Checkout';
$activePage = 'cart';
require __DIR__ . '/includes/header.php';
?>

<main>
  <div class="page-header">
    <div class="container">
      <h1>Checkout</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item"><a href="cart.php">Cart</a></li>
          <li class="breadcrumb-item active">Checkout</li>
        </ol>
      </nav>
    </div>
  </div>

  <section class="container pb-5">
    <form action="#" method="post">
      <div class="row g-4">
        <div class="col-lg-7">
          <h4 class="mb-3">Billing Details</h4>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">First Name *</label>
              <input type="text" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Last Name *</label>
              <input type="text" class="form-control" required>
            </div>
            <div class="col-12">
              <label class="form-label">Company Name (optional)</label>
              <input type="text" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label">Country / Region *</label>
              <select class="form-select" required>
                <option>United States</option>
                <option>United Kingdom</option>
                <option>Pakistan</option>
                <option>United Arab Emirates</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Street Address *</label>
              <input type="text" class="form-control mb-2" placeholder="House number and street name" required>
              <input type="text" class="form-control" placeholder="Apartment, suite, unit (optional)">
            </div>
            <div class="col-md-6">
              <label class="form-label">Town / City *</label>
              <input type="text" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">ZIP Code *</label>
              <input type="text" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone *</label>
              <input type="tel" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email *</label>
              <input type="email" class="form-control" required>
            </div>
            <div class="col-12">
              <label class="form-label">Order Notes (optional)</label>
              <textarea class="form-control" rows="3" placeholder="Notes about your order"></textarea>
            </div>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="cart-totals">
            <h4>Your Order</h4>
            <div class="d-flex justify-content-between py-2 border-bottom">
              <span>Rafi White Trends Tee × 1</span>
              <strong>$21.00</strong>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom">
              <span>Dark Blue Women’s Hat × 1</span>
              <strong>$118.00</strong>
            </div>
            <div class="d-flex justify-content-between py-2"><span>Subtotal</span><strong>$139.00</strong></div>
            <div class="d-flex justify-content-between py-2"><span>Shipping</span><span>Free Shipping</span></div>
            <div class="d-flex justify-content-between py-3 fs-5 border-top mt-2">
              <span>Total</span><strong class="text-dark">$139.00</strong>
            </div>

            <div class="mt-3 mb-3">
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="payment" id="cod" checked>
                <label class="form-check-label" for="cod">Cash on Delivery</label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="payment" id="card">
                <label class="form-check-label" for="card">Credit / Debit Card</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="payment" id="paypal">
                <label class="form-check-label" for="paypal">PayPal</label>
              </div>
            </div>
            <button type="submit" class="btn btn-rs w-100">Place Order</button>
          </div>
        </div>
      </div>
    </form>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
