<?php
/**
 * Shared product helpers for demo pages
 */
function productCard($title, $price, $old, $img, $sale = false) {
  $badge = $sale ? '<span class="product-badge">Sale</span>' : '';
  $oldHtml = $old ? '<del>$' . number_format($old, 2) . '</del>' : '';
  echo <<<HTML
  <div class="product-card">
    <div class="media">
      {$badge}
      <a href="product.php"><img src="{$img}" alt="{$title}" loading="lazy"></a>
      <a href="product.php" class="btn btn-rs-dark btn-sm quick-view">Quick View</a>
    </div>
    <div class="rating"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star"></i> <span class="text-muted">(6)</span></div>
    <a href="product.php" class="name">{$title}</a>
    <div class="price">\${$price} {$oldHtml}</div>
  </div>
HTML;
}

$productsFashion = [
  ['Fashion Sports Cap', '199.00', 210, 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=400&h=400&fit=crop', true],
  ["Men's Fashion Hood", '199.00', 210, 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=400&h=400&fit=crop', false],
  ['Black Jeans Trousers', '199.00', 210, 'https://images.unsplash.com/photo-1542272604-787c3835535d?w=400&h=400&fit=crop', true],
  ["Women's Fashion HandBag", '199.00', 210, 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=400&h=400&fit=crop', false],
  ['Dark Blue Suede Shoes', '199.00', 210, 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=400&h=400&fit=crop', false],
  ['Brown Leather Shoes', '199.00', 210, 'https://images.unsplash.com/photo-1614252231356-3ad5b6f3e1d0?w=400&h=400&fit=crop', true],
];

$productsBeauty = [
  ['Floral Perfume', '199.00', 210, 'https://images.unsplash.com/photo-1541643600914-78b084683601?w=400&h=400&fit=crop', true],
  ['Toilet Powder', '199.00', 210, 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&h=400&fit=crop', false],
  ['Purple Lipstick', '199.00', 210, 'https://images.unsplash.com/photo-1586495777744-4413f21067fa?w=400&h=400&fit=crop', false],
  ["Women's Hand Glass", '199.00', 210, 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=400&h=400&fit=crop', true],
  ["Men's Fashion Perfume", '199.00', 210, 'https://images.unsplash.com/photo-1594035910387-fea47794261f?w=400&h=400&fit=crop', false],
  ['Fashion Face Powder', '199.00', 210, 'https://images.unsplash.com/photo-1571781926291-c77df360cbdc?w=400&h=400&fit=crop', false],
];

$productsTech = [
  ['R7 Bluetooth Receiver', '199.00', 210, 'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=400&h=400&fit=crop', false],
  ['Wireless Headphone', '199.00', 210, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop', true],
  ['S5 Bluetooth Receiver', '199.00', 210, 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=400&h=400&fit=crop', false],
  ['Samsung Bluetooth Headphone', '199.00', 210, 'https://images.unsplash.com/photo-1487215078519-e21cc028cb29?w=400&h=400&fit=crop', false],
  ['Bluetooth Mini Receiver', '199.00', 210, 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=400&h=400&fit=crop', true],
  ['Studio Headphone Pro', '199.00', 210, 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=400&h=400&fit=crop', false],
];

$productsHome = [
  ['FTPS Coffee Maker', '199.00', 210, 'https://images.unsplash.com/photo-1517668808822-9ebb02f2a0e6?w=400&h=400&fit=crop', true],
  ['Electronic Pulverizer', '199.00', 210, 'https://images.unsplash.com/photo-1585515320310-259814833e7f?w=400&h=400&fit=crop', false],
  ['Kitchen Blender Set', '199.00', 210, 'https://images.unsplash.com/photo-1570222094114-d054a817e56b?w=400&h=400&fit=crop', false],
  ['Water Cooler Reservoir', '199.00', 210, 'https://images.unsplash.com/photo-1564422147064-2b2c8a8a7a5d?w=400&h=400&fit=crop', true],
  ['Electronic Roaster', '199.00', 210, 'https://images.unsplash.com/photo-1556910103-1c02745aae4d?w=400&h=400&fit=crop', false],
  ['Electronic Cooker', '199.00', 210, 'https://images.unsplash.com/photo-1584990347449-a2d4c2f2fc6c?w=400&h=400&fit=crop', false],
];
