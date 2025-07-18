<?php
$id = $id ?? 'product-grid-' . uniqid();
$heading = $settings['heading'] ?? 'Shop Products';
$collectionId = $settings['collection_id'] ?? null;
if (!$collectionId && !empty($context['collection']['id'])) {
  $collectionId = $context['collection']['id'];
}
$setId = $settings['product_set_id'] ?? null;
$maxProducts = intval($settings['max_products'] ?? 12);
$showPrices = $settings['show_prices'] ?? true;
$columns = intval($settings['columns'] ?? 4);

// Replace with real fetch
if ($setId) {
  $products = getProductsBySet($setId, $maxProducts);
} else {
  $products = getProductsByCollection($collectionId, $maxProducts); // Returns products with keys: title, price, image, handle, compare_price, variants
}

if (!function_exists('escape_html')) {
  function escape_html($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
  }
}
?>

<style>
#<?= $id ?> {
  padding: 60px 20px;
  font-family: Arial, sans-serif;
  text-align: center;
}

#<?= $id ?> .product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 30px;
  margin-top: 30px;
  max-width: 1200px;
  margin-left: auto;
  margin-right: auto;
}

#<?= $id ?> .product-card {
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.05);
  overflow: hidden;
  transition: transform 0.2s ease;
  text-align: left;
  text-decoration: none;
  color: inherit;
}

#<?= $id ?> .product-card:hover {
  transform: translateY(-5px);
}

#<?= $id ?> .product-card img {
  width: 100%;
  height: 240px;
  object-fit: cover;
}

#<?= $id ?> .product-info {
  padding: 1rem;
}

#<?= $id ?> .product-title {
  font-size: 1rem;
  font-weight: bold;
  margin-bottom: 8px;
}

#<?= $id ?> .price-block {
  font-size: 0.95rem;
  color: #333;
}

#<?= $id ?> .price-block del {
  color: #999;
  margin-left: 6px;
  font-size: 0.85rem;
}

#<?= $id ?> .badge-sale {
  background: #ff3b3b;
  color: white;
  font-size: 0.75rem;
  padding: 4px 8px;
  border-radius: 4px;
  position: absolute;
  top: 10px;
  left: 10px;
}
</style>

<section id="<?= $id ?>">
  <h2><?= escape_html($heading) ?></h2>

  <div class="product-grid">
    <?php foreach ($products as $product): ?>
<a class="product-card" href="/products/<?= escape_html($product['handle']) ?>">
        <div style="position: relative;">
          <img src="<?= escape_html($product['image']) ?>" alt="<?= escape_html($product['title']) ?>">
          <?php if (!empty($product['compare_price']) && $product['compare_price'] > $product['price']): ?>
            <div class="badge-sale">Sale</div>
          <?php endif; ?>
        </div>
        <div class="product-info">
          <div class="product-title"><?= escape_html($product['title']) ?></div>
          <?php if ($showPrices): ?>
            <div class="price-block">
              ₹<?= number_format($product['price'], 2) ?>
              <?php if (!empty($product['compare_price']) && $product['compare_price'] > $product['price']): ?>
                <del>₹<?= number_format($product['compare_price'], 2) ?></del>
              <?php endif; ?>
            </div>
          <?php endif; ?>
          <button class="add-to-cart-button btn btn-primary mt-2" data-product-id="<?= (int)$product['id'] ?>">Add to Cart</button>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>
