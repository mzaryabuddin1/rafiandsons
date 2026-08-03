<footer class="footer">
      <div class="container">
        <div class="row g-4">
          <div class="col-lg-3 col-md-6">
            <a href="index.php" class="brand-logo text-white d-inline-block mb-3">Rafi<span>&amp;</span>Sons</a>
            <p class="mb-3">Get all the latest information, sales and offers.</p>
            <form class="newsletter-form d-flex">
              <input type="email" class="form-control" placeholder="Email address" required>
              <button class="btn" type="submit">Go</button>
            </form>
          </div>
          <div class="col-lg-3 col-md-6">
            <h5>Contact Info</h5>
            <ul>
              <li><i class="bi bi-telephone me-2"></i> Toll Free (123) 456-7890</li>
              <li><i class="bi bi-envelope me-2"></i> hello@rafiandsons.com</li>
              <li><i class="bi bi-geo-alt me-2"></i> 123 Street, City, Country</li>
              <li class="mt-2"><strong class="text-white">WORKING DAYS</strong><br>Mon – Sun / 9:00 AM – 8:00 PM</li>
            </ul>
          </div>
          <div class="col-lg-2 col-md-4">
            <h5>About Us</h5>
            <ul>
              <li><a href="about.php">About Us</a></li>
              <li><a href="account.php">Order History</a></li>
              <li><a href="contact.php">Returns</a></li>
              <li><a href="contact.php">Customer Service</a></li>
              <li><a href="about.php">Terms &amp; Conditions</a></li>
            </ul>
          </div>
          <div class="col-lg-2 col-md-4">
            <h5>My Account</h5>
            <ul>
              <li><a href="account.php">Sign In</a></li>
              <li><a href="cart.php">View Cart</a></li>
              <li><a href="wishlist.php">My Wishlist</a></li>
              <li><a href="account.php">Track My Order</a></li>
              <li><a href="contact.php">Help</a></li>
            </ul>
          </div>
          <div class="col-lg-2 col-md-4">
            <h5>Follow Us</h5>
            <div class="d-flex gap-2 fs-5">
              <a href="#"><i class="bi bi-facebook"></i></a>
              <a href="#"><i class="bi bi-twitter-x"></i></a>
              <a href="#"><i class="bi bi-instagram"></i></a>
              <a href="#"><i class="bi bi-youtube"></i></a>
            </div>
          </div>
        </div>
        <div class="footer-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
          <p class="mb-0">Rafi &amp; Sons eCommerce © <?= date('Y') ?>. All Rights Reserved</p>
          <div class="payment-icons text-white-50 small">
            <i class="bi bi-credit-card-2-front fs-4 me-1"></i>
            <i class="bi bi-paypal fs-4 me-1"></i>
            <i class="bi bi-wallet2 fs-4"></i>
          </div>
        </div>
      </div>
    </footer>

    <nav class="mobile-bar">
      <a href="index.php"><i class="bi bi-house"></i>Home</a>
      <a href="shop.php"><i class="bi bi-grid"></i>Shop</a>
      <a href="wishlist.php"><i class="bi bi-heart"></i>Wishlist</a>
      <a href="account.php"><i class="bi bi-person"></i>Account</a>
    </nav>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>
