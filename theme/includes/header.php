<?php
if (!isset($pageTitle)) $pageTitle = 'Rafi & Sons';
if (!isset($activePage)) $activePage = '';
function navActive($key, $activePage) {
  return $activePage === $key ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?> | Rafi &amp; Sons</title>
  <meta name="description" content="Rafi & Sons — multi-category ecommerce store">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
  <div class="page-wrapper">
    <header class="site-header">
      <div class="header-top d-none d-md-block">
        <div class="container d-flex justify-content-between align-items-center">
          <p class="mb-0 welcome-msg">Welcome to Rafi &amp; Sons store!</p>
          <div>
            <a href="about.php">About</a>
            <a href="blog.php">Blog</a>
            <a href="contact.php">Contact</a>
            <a href="account.php">Login</a>
          </div>
        </div>
      </div>

      <div class="header-middle">
        <div class="container">
          <div class="row align-items-center g-3">
            <div class="col-auto d-lg-none">
              <button class="btn btn-link text-dark p-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                <i class="bi bi-list fs-3"></i>
              </button>
            </div>
            <div class="col-auto">
              <a href="index.php" class="brand-logo">Rafi<span>&amp;</span>Sons</a>
            </div>
            <div class="col d-none d-md-block">
              <form class="search-wrap position-relative" action="shop.php" method="get">
                <input type="text" class="form-control" name="q" placeholder="Search products...">
                <button class="btn" type="submit" aria-label="Search"><i class="bi bi-search"></i></button>
              </form>
            </div>
            <div class="col-auto ms-auto">
              <div class="d-flex align-items-center gap-3 gap-lg-4">
                <a href="tel:+1800123456" class="call-box d-none d-xl-flex align-items-center gap-2 text-decoration-none">
                  <i class="bi bi-telephone fs-4 text-dark"></i>
                  <div>
                    <strong>Call Us Now:</strong>
                    <span>0(800) 123-456</span>
                  </div>
                </a>
                <a href="wishlist.php" class="header-icon" title="Wishlist">
                  <i class="bi bi-heart"></i>
                  <span class="badge-count">3</span>
                </a>
                <a href="cart.php" class="header-icon" title="Cart" data-bs-toggle="offcanvas" data-bs-target="#cartDrawer">
                  <i class="bi bi-bag"></i>
                  <span class="badge-count">2</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="header-bottom d-none d-lg-block">
        <div class="container d-flex align-items-center">
          <div class="dropdown">
            <a class="category-toggle dropdown-toggle" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
              <i class="bi bi-list"></i> Shop By Categories
            </a>
            <ul class="dropdown-menu category-menu">
              <li><a class="dropdown-item fw-semibold" href="shop.php">Browse Our Categories</a></li>
              <li><a class="dropdown-item" href="shop.php?cat=electronics"><i class="bi bi-camera"></i> Electronics</a></li>
              <li><a class="dropdown-item" href="shop.php?cat=bags"><i class="bi bi-briefcase"></i> Backpacks &amp; Fashion Bags</a></li>
              <li><a class="dropdown-item" href="shop.php?cat=fashion"><i class="bi bi-handbag"></i> Travel &amp; Clothings</a></li>
              <li><a class="dropdown-item" href="shop.php?cat=computers"><i class="bi bi-laptop"></i> Computers</a></li>
              <li><a class="dropdown-item" href="shop.php?cat=beauty"><i class="bi bi-heart"></i> Beauty &amp; Fragrance</a></li>
              <li><a class="dropdown-item" href="shop.php?cat=home"><i class="bi bi-house"></i> Home &amp; Kitchen</a></li>
              <li><a class="dropdown-item" href="shop.php?cat=jewelry"><i class="bi bi-watch"></i> Jewelry &amp; Watches</a></li>
              <li><a class="dropdown-item" href="shop.php?cat=shoes"><i class="bi bi-bag-check"></i> Shoes</a></li>
            </ul>
          </div>
          <nav class="navbar navbar-expand p-0 ms-2">
            <ul class="navbar-nav">
              <li class="nav-item"><a class="nav-link <?= navActive('home', $activePage) ?>" href="index.php">Home</a></li>
              <li class="nav-item"><a class="nav-link <?= navActive('shop', $activePage) ?>" href="shop.php">Shop</a></li>
              <li class="nav-item"><a class="nav-link <?= navActive('about', $activePage) ?>" href="about.php">About</a></li>
              <li class="nav-item"><a class="nav-link <?= navActive('blog', $activePage) ?>" href="blog.php">Blog</a></li>
              <li class="nav-item"><a class="nav-link <?= navActive('contact', $activePage) ?>" href="contact.php">Contact</a></li>
              <li class="nav-item"><a class="nav-link <?= navActive('account', $activePage) ?>" href="account.php">Account</a></li>
            </ul>
          </nav>
          <div class="ms-auto currency-lang d-flex gap-3">
            <div class="dropdown">
              <a class="dropdown-toggle" href="#" data-bs-toggle="dropdown">USD</a>
              <ul class="dropdown-menu dropdown-menu-end"><li><a class="dropdown-item" href="#">USD</a></li><li><a class="dropdown-item" href="#">EUR</a></li></ul>
            </div>
            <div class="dropdown">
              <a class="dropdown-toggle" href="#" data-bs-toggle="dropdown">ENG</a>
              <ul class="dropdown-menu dropdown-menu-end"><li><a class="dropdown-item" href="#">ENG</a></li><li><a class="dropdown-item" href="#">FRH</a></li></ul>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Mobile menu -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title brand-logo">Rafi<span>&amp;</span>Sons</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
      </div>
      <div class="offcanvas-body">
        <form class="search-wrap position-relative mb-3" action="shop.php">
          <input type="text" class="form-control" name="q" placeholder="Search...">
          <button class="btn" type="submit"><i class="bi bi-search"></i></button>
        </form>
        <div class="list-group list-group-flush">
          <a href="index.php" class="list-group-item list-group-item-action">Home</a>
          <a href="shop.php" class="list-group-item list-group-item-action">Shop</a>
          <a href="about.php" class="list-group-item list-group-item-action">About</a>
          <a href="blog.php" class="list-group-item list-group-item-action">Blog</a>
          <a href="wishlist.php" class="list-group-item list-group-item-action">Wishlist</a>
          <a href="cart.php" class="list-group-item list-group-item-action">Cart</a>
          <a href="account.php" class="list-group-item list-group-item-action">My Account</a>
          <a href="contact.php" class="list-group-item list-group-item-action">Contact</a>
        </div>
      </div>
    </div>

    <!-- Cart drawer -->
    <div class="offcanvas offcanvas-end offcanvas-cart" tabindex="-1" id="cartDrawer">
      <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Shopping Cart</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
      </div>
      <div class="offcanvas-body">
        <div class="product-row">
          <img src="https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=160&h=160&fit=crop" alt="">
          <div class="flex-grow-1">
            <a href="product.php" class="fw-semibold text-dark">Rafi White Trends Tee</a>
            <div class="small mt-1">1 × $21.00</div>
          </div>
          <button class="btn btn-sm btn-link text-muted"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="product-row">
          <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=160&h=160&fit=crop" alt="">
          <div class="flex-grow-1">
            <a href="product.php" class="fw-semibold text-dark">Dark Blue Women’s Hat</a>
            <div class="small mt-1">1 × $118.00</div>
          </div>
          <button class="btn btn-sm btn-link text-muted"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="d-flex justify-content-between fw-bold text-dark mt-3 mb-3">
          <span>Subtotal:</span>
          <span>$139.00</span>
        </div>
        <a href="cart.php" class="btn btn-rs-outline w-100 mb-2">View Cart</a>
        <a href="checkout.php" class="btn btn-rs-dark w-100">Go To Checkout</a>
      </div>
    </div>
