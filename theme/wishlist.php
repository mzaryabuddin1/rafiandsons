<?php
$pageTitle = 'Wishlist';
$activePage = 'wishlist';
require __DIR__ . '/includes/header.php';

$items = [
  ["Girl's Dark Bag", 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=200&h=200&fit=crop', 84.00, true],
  ["Women's Fashion Comforter", 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=200&h=200&fit=crop', 84.00, true],
  ['Wide Knickerbockers', 'https://images.unsplash.com/photo-1542272604-787c3835535d?w=200&h=200&fit=crop', 84.00, false],
];
?>

<main>
  <div class="page-header">
    <div class="container">
      <h1>Wishlist</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Wishlist</li>
        </ol>
      </nav>
    </div>
  </div>

  <section class="container pb-5">
    <div class="table-responsive">
      <table class="table cart-table align-middle">
        <thead>
          <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Stock Status</th>
            <th></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): ?>
          <tr>
            <td>
              <div class="d-flex align-items-center gap-3">
                <img src="<?= $item[1] ?>" alt="">
                <a href="product.php" class="fw-semibold text-dark"><?= $item[0] ?></a>
              </div>
            </td>
            <td class="fw-semibold text-dark">$<?= number_format($item[2], 2) ?></td>
            <td>
              <?php if ($item[3]): ?>
              <span class="text-success">In Stock</span>
              <?php else: ?>
              <span class="text-danger">Out of Stock</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($item[3]): ?>
              <a href="cart.php" class="btn btn-rs btn-sm">Add to Cart</a>
              <?php else: ?>
              <button class="btn btn-secondary btn-sm" disabled>Unavailable</button>
              <?php endif; ?>
            </td>
            <td><button class="btn btn-link text-muted"><i class="bi bi-x-lg"></i></button></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <a href="shop.php" class="btn btn-rs-outline">Continue Shopping</a>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
