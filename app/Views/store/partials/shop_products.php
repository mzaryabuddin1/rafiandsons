<?php foreach ($products as $product): ?>
    <?= view('store/partials/product_card', ['product' => $product, 'style' => 'qist']) ?>
<?php endforeach; ?>
