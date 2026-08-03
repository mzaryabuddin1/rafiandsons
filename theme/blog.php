<?php
$pageTitle = 'Blog';
$activePage = 'blog';
require __DIR__ . '/includes/header.php';

$posts = [
  ['Complete Set Of Ski Tools.', 'Discover the essential gear checklist for your next winter adventure.', 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=800&h=500&fit=crop', 'September 6, 2020'],
  ['Utaliquam sollicitudin leo.', 'Style notes and product picks from our fashion editors this week.', 'https://images.unsplash.com/photo-1483985988104-5bcadb2c2f4f?w=800&h=500&fit=crop', 'September 6, 2020'],
  ['Fusce pellentesque suscipit.', 'How to refresh your home with seasonal kitchen and decor finds.', 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=800&h=500&fit=crop', 'September 6, 2020'],
  ['Style tips for the season', 'Layering ideas and accessories that elevate everyday outfits.', 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=800&h=500&fit=crop', 'August 21, 2020'],
  ['Beauty routines that work', 'Fragrance and skincare essentials our customers love most.', 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=800&h=500&fit=crop', 'August 12, 2020'],
  ['Gadgets worth buying', 'Smart tech picks for work, travel, and entertainment.', 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&h=500&fit=crop', 'July 30, 2020'],
];
?>

<main>
  <div class="page-header">
    <div class="container">
      <h1>From Our Blog</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Blog</li>
        </ol>
      </nav>
    </div>
  </div>

  <section class="container pb-5">
    <div class="row g-4">
      <?php foreach ($posts as $post): ?>
      <div class="col-md-6 col-lg-4">
        <article class="blog-card h-100">
          <div class="media"><img src="<?= $post[2] ?>" alt="" loading="lazy"></div>
          <div class="meta"><?= $post[3] ?> | 1 Comments</div>
          <h4 class="mb-2"><?= $post[0] ?></h4>
          <p><?= $post[1] ?></p>
          <a href="#" class="fw-semibold small">Read More →</a>
        </article>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
