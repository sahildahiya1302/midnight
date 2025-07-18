<?php
// Safe HTML escape
if (!function_exists('escape_html')) {
  function escape_html($str) {
    return htmlspecialchars((string) $str, ENT_QUOTES, 'UTF-8');
  }
}

$id = $id ?? 'featured-collection-' . uniqid();
$heading = $settings['heading'] ?? 'Featured Collection';
$collectionId = $settings['collection_id'] ?? '';
if (!$collectionId && !empty($context['collection']['id'])) {
  $collectionId = $context['collection']['id'];
}
$maxProducts = $settings['max_products'] ?? 8;
$showPrices = $settings['show_prices'] ?? true;
$showCTA = $settings['show_cta_button'] ?? false;
$ctaText = $settings['cta_text'] ?? 'View All';

// Replace with actual product loader by collection ID
$products = getProductsByCollection($collectionId, $maxProducts); // You must define this function
?>

<style>
#<?= $id ?> {
  padding: 60px 20px;
  font-family: Arial, sans-serif;
  text-align: center;
}

#<?= $id ?> .product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 20px;
  margin-top: 30px;
}

#<?= $id ?> .product-card {
  text-decoration: none;
  color: inherit;
}

#<?= $id ?> .product-card img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 6px;
}

#<?= $id ?> .cta-button {
  margin-top: 20px;
  display: inline-block;
  background: #000;
  color: white;
  padding: 10px 20px;
  border-radius: 30px;
  text-decoration: none;
}
</style>

<section id="<?= $id ?>">
  <h2><?= escape_html($heading) ?></h2>

  <div class="product-grid">
    <?php foreach ($products as $product): ?>
      <?php
  $handle = $product['handle'] ?? '';
  $title = $product['title'] ?? 'Untitled';
  $image = $product['image'] ?? '/assets/images/placeholder.png';
  $price = isset($product['price']) ? (float)$product['price'] : 0;
?>
<a href="/products/<?= escape_html($handle) ?>" class="product-card">
  <img src="<?= escape_html($image) ?>" alt="<?= escape_html($title) ?>">
  <div><?= escape_html($title) ?></div>
  <?php if ($showPrices): ?>
    <div>₹<?= number_format($price, 2) ?></div>
  <?php endif; ?>
  <button class="add-to-cart-button btn btn-primary mt-2" data-product-id="<?= (int)$product['id'] ?>">Add to Cart</button>
</a>

    <?php endforeach; ?>
  </div>

  <?php if ($showCTA): ?>
    <a href="/collections/<?= escape_html($collectionId) ?>" class="cta-button"><?= escape_html($ctaText) ?></a>
  <?php endif; ?>
</section>
