<?php
$pageTitle = 'Contact Us';
$activePage = 'contact';
require __DIR__ . '/includes/header.php';
?>

<main>
  <div class="page-header">
    <div class="container">
      <h1>Contact Us</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Contact</li>
        </ol>
      </nav>
    </div>
  </div>

  <section class="container pb-5">
    <div class="row g-4 mb-5">
      <div class="col-md-4">
        <div class="contact-info-box text-center">
          <i class="bi bi-geo-alt d-block"></i>
          <h5>Address</h5>
          <p class="mb-0">123 Street, City, Country</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="contact-info-box text-center">
          <i class="bi bi-telephone d-block"></i>
          <h5>Phone</h5>
          <p class="mb-0">Toll Free (123) 456-7890</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="contact-info-box text-center">
          <i class="bi bi-envelope d-block"></i>
          <h5>Email</h5>
          <p class="mb-0">hello@rafiandsons.com</p>
        </div>
      </div>
    </div>

    <div class="row g-5">
      <div class="col-lg-6">
        <h4 class="mb-3">Send Us a Message</h4>
        <form>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Name *</label>
              <input type="text" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email *</label>
              <input type="email" class="form-control" required>
            </div>
            <div class="col-12">
              <label class="form-label">Subject</label>
              <input type="text" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label">Message *</label>
              <textarea class="form-control" rows="5" required></textarea>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-rs">Send Message</button>
            </div>
          </div>
        </form>
      </div>
      <div class="col-lg-6">
        <div class="ratio ratio-4x3 bg-light">
          <iframe
            src="https://maps.google.com/maps?q=New%20York&t=&z=13&ie=UTF8&iwloc=&output=embed"
            style="border:0" allowfullscreen loading="lazy" title="Map"></iframe>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
