<?php
$pageTitle = 'About Us';
$activePage = 'about';
require __DIR__ . '/includes/header.php';
?>

<main>
  <div class="page-header">
    <div class="container">
      <h1>About Us</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">About Us</li>
        </ol>
      </nav>
    </div>
  </div>

  <section class="container pb-5">
    <div class="row align-items-center g-5 mb-5">
      <div class="col-lg-6">
        <img src="https://images.unsplash.com/photo-1441984904996-e0b692f4c4f1?w=900&h=700&fit=crop" alt="Our store" class="w-100">
      </div>
      <div class="col-lg-6">
        <p class="text-uppercase small fw-semibold" style="color:var(--rs-primary)">Our Story</p>
        <h2 class="mb-3">Welcome to Rafi &amp; Sons</h2>
        <p>Inspired by modern marketplace shopping experiences, Rafi &amp; Sons brings fashion, electronics, beauty, and home essentials together in one trusted destination.</p>
        <p>We partner with reliable suppliers and brands so you can discover quality products at fair prices — with secure checkout, fast shipping, and dedicated support.</p>
        <a href="shop.php" class="btn btn-rs mt-2">Start Shopping</a>
      </div>
    </div>

    <div class="row g-4 text-center mb-5">
      <div class="col-md-3 col-6">
        <div class="service-box">
          <h3 class="mb-1" style="color:var(--rs-primary)">15+</h3>
          <p class="mb-0">Years Experience</p>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="service-box">
          <h3 class="mb-1" style="color:var(--rs-primary)">8K+</h3>
          <p class="mb-0">Products</p>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="service-box">
          <h3 class="mb-1" style="color:var(--rs-primary)">50K+</h3>
          <p class="mb-0">Happy Customers</p>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="service-box">
          <h3 class="mb-1" style="color:var(--rs-primary)">120+</h3>
          <p class="mb-0">Brand Partners</p>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-md-4">
        <div class="service-box text-start">
          <i class="bi bi-bullseye"></i>
          <h5>Our Mission</h5>
          <p>Make multi-category shopping simple, enjoyable, and accessible for everyone.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="service-box text-start">
          <i class="bi bi-eye"></i>
          <h5>Our Vision</h5>
          <p>Become the most trusted online marketplace for everyday lifestyle needs.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="service-box text-start">
          <i class="bi bi-heart"></i>
          <h5>Our Values</h5>
          <p>Quality, transparency, and customer-first service in every order we fulfill.</p>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
