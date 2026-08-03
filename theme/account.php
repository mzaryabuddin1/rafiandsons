<?php
$pageTitle = 'My Account';
$activePage = 'account';
require __DIR__ . '/includes/header.php';
?>

<main>
  <div class="page-header">
    <div class="container">
      <h1>My Account</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Account</li>
        </ol>
      </nav>
    </div>
  </div>

  <section class="container pb-5">
    <div class="row g-4">
      <div class="col-lg-6">
        <div class="border p-4 h-100">
          <h4 class="mb-3">Login</h4>
          <form>
            <div class="mb-3">
              <label class="form-label">Email address *</label>
              <input type="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password *</label>
              <input type="password" class="form-control" required>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember">
                <label class="form-check-label" for="remember">Remember me</label>
              </div>
              <a href="#" class="small">Lost your password?</a>
            </div>
            <button type="submit" class="btn btn-rs">Log In</button>
          </form>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="border p-4 h-100">
          <h4 class="mb-3">Register</h4>
          <form>
            <div class="mb-3">
              <label class="form-label">Full Name *</label>
              <input type="text" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Email address *</label>
              <input type="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password *</label>
              <input type="password" class="form-control" required>
            </div>
            <p class="small text-muted">Your personal data will be used to support your experience throughout this website.</p>
            <button type="submit" class="btn btn-rs-dark">Register</button>
          </form>
        </div>
      </div>
    </div>

    <div class="row g-4 mt-2">
      <div class="col-lg-3">
        <nav class="nav flex-column account-nav border">
          <a class="nav-link active" href="#">Dashboard</a>
          <a class="nav-link" href="#">Orders</a>
          <a class="nav-link" href="#">Downloads</a>
          <a class="nav-link" href="#">Addresses</a>
          <a class="nav-link" href="#">Account Details</a>
          <a class="nav-link" href="wishlist.php">Wishlist</a>
          <a class="nav-link" href="#">Logout</a>
        </nav>
      </div>
      <div class="col-lg-9">
        <div class="border p-4">
          <h5>Hello, Customer</h5>
          <p class="mb-0">From your account dashboard you can view your recent orders, manage shipping addresses, and edit your password and account details.</p>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
