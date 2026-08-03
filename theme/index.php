<?php
$pageTitle = 'Home';
$activePage = 'home';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/products.php';
?>

<main>
  <!-- Intro / Hero (Demo22 layout) -->
  <section class="container section-pad pt-4">
    <div class="row g-3">
      <div class="col-lg-8">
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
          </div>
          <div class="carousel-inner">
            <div class="carousel-item active">
              <div class="hero-slide" style="background-image:url('https://images.unsplash.com/photo-1519183071298-a2962be96f83?w=1200&h=800&fit=crop')">
                <div class="content animate-in">
                  <p class="subtitle">Financing Offer</p>
                  <h2 class="title">Camera, Lens and Tablet</h2>
                  <p class="mb-1">Discount</p>
                  <div class="offer">40% OFF</div>
                  <a href="shop.php" class="btn btn-rs-white-outline">Shop now</a>
                </div>
              </div>
            </div>
            <div class="carousel-item">
              <div class="hero-slide" style="background-image:url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200&h=800&fit=crop')">
                <div class="content animate-in">
                  <p class="subtitle">Flash Sales</p>
                  <h2 class="title">Up to 70% Discount</h2>
                  <p class="mb-4">Extra Off Everything Online</p>
                  <a href="shop.php" class="btn btn-rs-white-outline">Shop now</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="side-banner h-100" style="background-image:url('https://images.unsplash.com/photo-1473968512647-3e447244af8f?w=800&h=900&fit=crop'); min-height:420px;">
          <div class="content animate-in delay-1">
            <p class="mb-1 text-uppercase small opacity-75">Through Rafi Birthday</p>
            <h3 class="mb-1">Up to 70% Off</h3>
            <p class="mb-3 fs-5 fw-semibold">Portable Drone SD9</p>
            <a href="shop.php" class="btn btn-rs">Buy drone</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Top Categories -->
  <section class="container pb-5">
    <h2 class="section-title text-center">Top Categories of the Month</h2>
    <div class="row g-3">
      <?php
      $cats = [
        ['Electronics', ['Air Conditioners','Machines','Musical Instrument','Office Electronics','Televisions']],
        ['Fashion & Clothings', ['Bikinies','Casual Dresses','Hair Accessories & Hats','Jackets','Jumpsuits & T-shirts']],
        ['Computers', ['Desktop PCs','Laptops','New Arrivals','PC Components','PC Gaming']],
        ['Home & Kitchen', ['Cookwares','Decor','Furniture','Garden Tools','New Arrivals']],
        ['Beauty & Fragrance', ['Hair Care','Makeup','New Arrivals','Perfumes','Skin Care']],
        ['Jewelry & Watches', ['Accessories','Bracelets','Necklace','Pendant','Watch']],
      ];
      foreach ($cats as $cat): ?>
      <div class="col-6 col-md-4 col-lg-2">
        <div class="cat-card">
          <h4><a href="shop.php"><?= $cat[0] ?></a></h4>
          <ul>
            <?php foreach ($cat[1] as $item): ?>
            <li><a href="shop.php"><?= $item ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Promo banners -->
  <section class="container pb-5">
    <div class="row g-3">
      <div class="col-md-6">
        <div class="promo-banner" style="background-image:url('https://images.unsplash.com/photo-1556740738-b6a63e27c4df?w=900&h=500&fit=crop')">
          <div class="content">
            <h3>Customized Products</h3>
            <p class="mb-3">Partner with experienced manufacturers with design &amp; production capabilities</p>
            <a href="shop.php" class="btn btn-rs me-2">OEM Factories</a>
            <a href="shop.php" class="btn btn-rs-white-outline">Top Suppliers</a>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="promo-banner" style="background-image:url('https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=900&h=500&fit=crop')">
          <div class="content">
            <h3>Ready-To-Ship Products</h3>
            <p class="mb-3">Source from millions of products ready to ship within 15 days</p>
            <a href="shop.php" class="btn btn-rs me-2">New Arrivals</a>
            <a href="shop.php" class="btn btn-rs-white-outline">Best Sellers</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Fashion block -->
  <section class="container pb-5">
    <div class="product-block-header">
      <h3>Fashion &amp; Clothing</h3>
      <div class="sub-links d-none d-md-block">
        <a href="shop.php">Bikinies</a>
        <a href="shop.php">Casual Dresses</a>
        <a href="shop.php">Jackets</a>
        <a href="shop.php">New Arrivals</a>
        <a href="shop.php">Sunglasses</a>
        <a href="shop.php" class="fw-semibold text-dark">View all products</a>
      </div>
    </div>
    <div class="row g-3">
      <div class="col-lg-3">
        <div class="featured-panel">
          <span class="label">Featured</span>
          <h4>Fashion Design<br>Collection</h4>
          <a href="shop.php" class="btn btn-rs align-self-start">Shop now</a>
        </div>
      </div>
      <div class="col-lg-9">
        <div class="row">
          <?php foreach (array_slice($productsFashion, 0, 6) as $p): ?>
          <div class="col-6 col-md-4"><?php productCard($p[0], $p[1], $p[2], $p[3], $p[4]); ?></div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- Beauty block -->
  <section class="container pb-5">
    <div class="product-block-header">
      <h3>Beauty &amp; Fragrance</h3>
      <div class="sub-links d-none d-md-block">
        <a href="shop.php">Hair Care</a>
        <a href="shop.php">Makeup</a>
        <a href="shop.php">Perfumes</a>
        <a href="shop.php">Skin Care</a>
        <a href="shop.php" class="fw-semibold text-dark">View all products</a>
      </div>
    </div>
    <div class="row g-3">
      <div class="col-lg-3">
        <div class="featured-panel">
          <span class="label">Recommended for you</span>
          <h4>Cosmetics Trends<br>Collection</h4>
          <a href="shop.php" class="btn btn-rs align-self-start">Shop now</a>
        </div>
      </div>
      <div class="col-lg-9">
        <div class="row">
          <?php foreach (array_slice($productsBeauty, 0, 6) as $p): ?>
          <div class="col-6 col-md-4"><?php productCard($p[0], $p[1], $p[2], $p[3], $p[4]); ?></div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- Computers block -->
  <section class="container pb-5">
    <div class="product-block-header">
      <h3>Computers</h3>
      <div class="sub-links d-none d-md-block">
        <a href="shop.php">Desktop PCs</a>
        <a href="shop.php">Laptops</a>
        <a href="shop.php">PC Gaming</a>
        <a href="shop.php" class="fw-semibold text-dark">View all products</a>
      </div>
    </div>
    <div class="row g-3">
      <div class="col-lg-3">
        <div class="featured-panel">
          <span class="label">Featured</span>
          <h4>Top Electronics<br>Collection</h4>
          <a href="shop.php" class="btn btn-rs align-self-start">Shop now</a>
        </div>
      </div>
      <div class="col-lg-9">
        <div class="row">
          <?php foreach (array_slice($productsTech, 0, 6) as $p): ?>
          <div class="col-6 col-md-4"><?php productCard($p[0], $p[1], $p[2], $p[3], $p[4]); ?></div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- Home & Kitchen -->
  <section class="container pb-5">
    <div class="product-block-header">
      <h3>Home &amp; Kitchen</h3>
      <div class="sub-links d-none d-md-block">
        <a href="shop.php">Cookwares</a>
        <a href="shop.php">Decor</a>
        <a href="shop.php">Furniture</a>
        <a href="shop.php" class="fw-semibold text-dark">View all products</a>
      </div>
    </div>
    <div class="row g-3">
      <div class="col-lg-3">
        <div class="featured-panel">
          <span class="label">Recommended for you</span>
          <h4>Kitchen Tools<br>Collection</h4>
          <a href="shop.php" class="btn btn-rs align-self-start">Shop now</a>
        </div>
      </div>
      <div class="col-lg-9">
        <div class="row">
          <?php foreach (array_slice($productsHome, 0, 6) as $p): ?>
          <div class="col-6 col-md-4"><?php productCard($p[0], $p[1], $p[2], $p[3], $p[4]); ?></div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- Services -->
  <section class="container pb-5">
    <div class="row g-3">
      <div class="col-6 col-lg-3">
        <div class="service-box">
          <i class="bi bi-truck"></i>
          <h5>Free Shipping &amp; Return</h5>
          <p>Free shipping on orders over $99</p>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="service-box">
          <i class="bi bi-headset"></i>
          <h5>Customer Support 24/7</h5>
          <p>Instant access to perfect support</p>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="service-box">
          <i class="bi bi-shield-lock"></i>
          <h5>100% Secure Payment</h5>
          <p>We ensure secure payment!</p>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="service-box">
          <i class="bi bi-arrow-counterclockwise"></i>
          <h5>Money Back Guarantee</h5>
          <p>Any back within 30 days</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Blog -->
  <section class="container pb-5">
    <h2 class="section-title text-center">From Our Blog</h2>
    <div class="row g-4">
      <?php
      $posts = [
        ['Complete Set Of Ski Tools.', 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=600&h=400&fit=crop'],
        ['Utaliquam sollicitudin leo.', 'https://images.unsplash.com/photo-1483985988104-5bcadb2c2f4f?w=600&h=400&fit=crop'],
        ['Fusce pellentesque suscipit.', 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=600&h=400&fit=crop'],
        ['Style tips for the season', 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=600&h=400&fit=crop'],
      ];
      foreach ($posts as $post): ?>
      <div class="col-md-6 col-lg-3">
        <article class="blog-card">
          <div class="media"><a href="blog.php"><img src="<?= $post[1] ?>" alt="" loading="lazy"></a></div>
          <div class="meta">September 6, 2020 | 1 Comments</div>
          <h4><a href="blog.php"><?= $post[0] ?></a></h4>
          <a href="blog.php" class="small fw-semibold">Read More →</a>
        </article>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
