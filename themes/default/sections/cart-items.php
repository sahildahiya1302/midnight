<?php
$id = $id ?? 'cart-items-' . uniqid();
$cart = $_SESSION['cart'] ?? []; // Replace with your own cart fetch logic
$currency = $settings['currency'] ?? '₹';
$textAlign = $settings['text_align'] ?? 'left';
$showImage = $settings['show_image'] ?? true;

if (!function_exists('escape_html')) {
  function escape_html($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
  }
}

$total = 0;
foreach ($cart as $item) {
  $total += ($item['price'] * $item['quantity']);
}
?>

<style>
#<?= $id ?> {
  padding: 40px 20px;
  font-family: Arial, sans-serif;
  text-align: <?= escape_html($textAlign) ?>;
}

#<?= $id ?> .cart-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 30px;
}

#<?= $id ?> th, #<?= $id ?> td {
  padding: 12px;
  border-bottom: 1px solid #ddd;
  vertical-align: middle;
}

#<?= $id ?> img.product-thumb {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 6px;
  margin-right: 10px;
}

#<?= $id ?> .product-info {
  display: flex;
  align-items: center;
}

#<?= $id ?> .quantity-controls {
  display: flex;
  align-items: center;
  gap: 8px;
}

#<?= $id ?> .cart-total {
  font-size: 1.2rem;
  font-weight: bold;
  text-align: right;
  margin-top: 20px;
}

#<?= $id ?> .checkout-button {
  display: inline-block;
  margin-top: 20px;
  background-color: #000;
  color: #fff;
  padding: 12px 24px;
  text-decoration: none;
  font-weight: bold;
  border-radius: 30px;
  transition: background 0.3s ease;
}

#<?= $id ?> .checkout-button:hover {
  background-color: #333;
}
</style>

<section id="<?= $id ?>" aria-label="Cart Items">
  <?php if (!empty($cart)): ?>
    <table class="cart-table">
      <thead>
        <tr>
          <th>Product</th>
          <th>Price</th>
          <th>Qty</th>
          <th>Subtotal</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($cart as $index => $item): ?>
          <tr>
            <td>
              <div class="product-info">
                <?php if ($showImage && !empty($item['image'])): ?>
                  <img src="<?= escape_html($item['image']) ?>" alt="<?= escape_html($item['title']) ?>" class="product-thumb">
                <?php endif; ?>
                <?= escape_html($item['title']) ?>
              </div>
            </td>
            <td><?= $currency ?><?= number_format($item['price'], 2) ?></td>
            <td>
              <form method="POST" action="/update-cart.php" class="quantity-controls">
                <input type="hidden" name="index" value="<?= $index ?>">
                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="99" style="width: 60px;">
                <button type="submit">Update</button>
              </form>
            </td>
            <td><?= $currency ?><?= number_format($item['price'] * $item['quantity'], 2) ?></td>
            <td>
              <form method="POST" action="/remove-cart-item.php">
                <input type="hidden" name="index" value="<?= $index ?>">
                <button type="submit" style="color:red;">Remove</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="cart-total">
      Total: <?= $currency ?><?= number_format($total, 2) ?>
    </div>

    <a href="/checkout.php" class="checkout-button">Proceed to Checkout</a>

  <?php else: ?>
    <p>Your cart is currently empty.</p>
  <?php endif; ?>
</section>
