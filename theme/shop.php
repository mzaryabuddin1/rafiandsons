<?php
$pageTitle = 'Shop';
$activePage = 'shop';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/products.php';
$all = array_merge($productsFashion, $productsBeauty, $productsTech, $productsHome);
?>

<main>
  <div class="page-header">
    <div class="container">
      <h1>Shop</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Shop</li>
        </ol>
      </nav>
    </div>
  </div>

  <section class="container pb-5">
    <div class="row g-4">
      <aside class="col-lg-3 shop-sidebar">
        <div class="widget">
          <h5>Categories</h5>
          <div class="list-group">
            <a href="shop.php" class="list-group-item list-group-item-action">All Products</a>
            <a href="shop.php?cat=fashion" class="list-group-item list-group-item-action">Fashion &amp; Clothing</a>
            <a href="shop.php?cat=beauty" class="list-group-item list-group-item-action">Beauty &amp; Fragrance</a>
            <a href="shop.php?cat=computers" class="list-group-item list-group-item-action">Computers</a>
            <a href="shop.php?cat=home" class="list-group-item list-group-item-action">Home &amp; Kitchen</a>
            <a href="shop.php?cat=electronics" class="list-group-item list-group-item-action">Electronics</a>
            <a href="shop.php?cat=shoes" class="list-group-item list-group-item-action">Shoes</a>
          </div>
        </div>
        <div class="widget">
          <h5>Filter by Price</h5>
          <label class="filter-check"><input type="checkbox"> Under $50</label>
          <label class="filter-check"><input type="checkbox"> $50 – $100</label>
          <label class="filter-check"><input type="checkbox" checked> $100 – $200</label>
          <label class="filter-check"><input type="checkbox"> $200+</label>
        </div>
        <div class="widget">
          <h5>Size</h5>
          <label class="filter-check"><input type="checkbox"> XS</label>
          <label class="filter-check"><input type="checkbox"> S</label>
          <label class="filter-check"><input type="checkbox"> M</label>
          <label class="filter-check"><input type="checkbox"> L</label>
          <label class="filter-check"><input type="checkbox"> XL</label>
        </div>
        <div class="widget border-0">
          <h5>Color</h5>
          <div class="d-flex gap-2 flex-wrap">
            <span class="rounded-circle border" style="width:22px;height:22px;background:#222"></span>
            <span class="rounded-circle border" style="width:22px;height:22px;background:#c0392b"></span>
            <span class="rounded-circle border" style="width:22px;height:22px;background:#2980b9"></span>
            <span class="rounded-circle border" style="width:22px;height:22px;background:#27ae60"></span>
            <span class="rounded-circle border" style="width:22px;height:22px;background:#f1c40f"></span>
            <span class="rounded-circle border" style="width:22px;height:22px;background:#fff"></span>
          </div>
        </div>
      </aside>

      <div class="col-lg-9">
        <div class="shop-toolbar">
          <p class="mb-0">Showing <strong><?= count($all) ?></strong> products</p>
          <div class="d-flex gap-2 align-items-center">
            <label class="small mb-0">Sort by</label>
            <select class="form-select form-select-sm" style="width:auto">
              <option>Default</option>
              <option>Price: Low to High</option>
              <option>Price: High to Low</option>
              <option>Newest</option>
            </select>
          </div>
        </div>
        <div class="row">
          <?php foreach ($all as $p): ?>
          <div class="col-6 col-md-4"><?php productCard($p[0], $p[1], $p[2], $p[3], $p[4]); ?></div>
          <?php endforeach; ?>
        </div>
        <nav class="mt-3">
          <ul class="pagination justify-content-center">
            <li class="page-item disabled"><a class="page-link" href="#">Prev</a></li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">Next</a></li>
          </ul>
        </nav>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
