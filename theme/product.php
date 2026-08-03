<?php
$pageTitle = 'Product';
$activePage = 'shop';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/products.php';
?>

<main>
  <div class="page-header">
    <div class="container">
      <h1>Fashion Sports Cap</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item"><a href="shop.php">Shop</a></li>
          <li class="breadcrumb-item active">Fashion Sports Cap</li>
        </ol>
      </nav>
    </div>
  </div>

  <section class="container pb-5">
    <div class="row g-5">
      <div class="col-lg-6">
        <div class="bg-light mb-3" style="aspect-ratio:1">
          <img src="https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=800&h=800&fit=crop" class="w-100 h-100" style="object-fit:cover" alt="Fashion Sports Cap">
        </div>
        <div class="row g-2">
          <?php
          $thumbs = [
            'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1521369909029-2afed882baee?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1575428652377-a2d80e0230c4?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1534215754734-18e55d13e346?w=200&h=200&fit=crop',
          ];
          foreach ($thumbs as $t): ?>
          <div class="col-3"><img src="<?= $t ?>" class="w-100 border" style="aspect-ratio:1;object-fit:cover" alt=""></div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="rating mb-2" style="color:var(--rs-primary)">
          <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star"></i>
          <span class="text-muted ms-1">(6 reviews)</span>
        </div>
        <h2 class="mb-2">Fashion Sports Cap</h2>
        <div class="mb-3 fs-4 fw-bold text-dark">$199.00 <del class="fs-6 text-muted fw-normal">$210.00</del></div>
        <p>Premium sports cap with breathable fabric and adjustable fit. Perfect for casual wear, workouts, and outdoor style.</p>
        <ul class="mb-4">
          <li>SKU: <strong>RS-CAP-001</strong></li>
          <li>Category: <a href="shop.php">Fashion</a></li>
          <li>Availability: <span class="text-success fw-semibold">In Stock</span></li>
        </ul>

        <div class="mb-3">
          <label class="form-label">Color</label>
          <div class="d-flex gap-2">
            <button class="btn btn-sm border active" style="width:32px;height:32px;background:#222"></button>
            <button class="btn btn-sm border" style="width:32px;height:32px;background:#2980b9"></button>
            <button class="btn btn-sm border" style="width:32px;height:32px;background:#c0392b"></button>
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Size</label>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-dark btn-sm">S</button>
            <button class="btn btn-dark btn-sm">M</button>
            <button class="btn btn-outline-dark btn-sm">L</button>
            <button class="btn btn-outline-dark btn-sm">XL</button>
          </div>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
          <div class="input-group" style="width:140px">
            <button class="btn btn-outline-secondary" type="button" data-qty="minus">−</button>
            <input type="text" class="form-control qty-input" value="1">
            <button class="btn btn-outline-secondary" type="button" data-qty="plus">+</button>
          </div>
          <a href="cart.php" class="btn btn-rs">Add to Cart</a>
          <a href="wishlist.php" class="btn btn-rs-outline"><i class="bi bi-heart"></i></a>
        </div>
        <a href="checkout.php" class="btn btn-rs-dark">Buy Now</a>
      </div>
    </div>

    <ul class="nav nav-tabs mt-5" role="tablist">
      <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#desc">Description</button></li>
      <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#info">Additional Info</button></li>
      <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews">Reviews (6)</button></li>
    </ul>
    <div class="tab-content border border-top-0 p-4 mb-5">
      <div class="tab-pane fade show active" id="desc">
        <p>This fashion sports cap combines comfort and street style. Lightweight materials keep you cool while the structured brim offers sun protection.</p>
        <p class="mb-0">Machine washable. Designed for everyday wear across seasons.</p>
      </div>
      <div class="tab-pane fade" id="info">
        <table class="table table-bordered mb-0">
          <tr><th>Material</th><td>Cotton blend</td></tr>
          <tr><th>Weight</th><td>120g</td></tr>
          <tr><th>Origin</th><td>Imported</td></tr>
        </table>
      </div>
      <div class="tab-pane fade" id="reviews">
        <p class="mb-0">Customer reviews will appear here. Average rating: 4.0 / 5 based on 6 reviews.</p>
      </div>
    </div>

    <h3 class="mb-4">Related Products</h3>
    <div class="row">
      <?php foreach (array_slice($productsFashion, 1, 4) as $p): ?>
      <div class="col-6 col-md-3"><?php productCard($p[0], $p[1], $p[2], $p[3], $p[4]); ?></div>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
