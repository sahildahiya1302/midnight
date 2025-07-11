<?php
setSecurityHeaders();
$file = THEME_PATH . '/config/checkout_success.json';
$cfg = is_file($file) ? json_decode(file_get_contents($file), true) : [];
$message = $cfg['message'] ?? 'Thank you for your order!';
$upsell = $cfg['upsell_products'] ?? [];
?>
<h1><?= htmlspecialchars($message) ?></h1>
<p>Your order has been placed successfully.</p>
<?php if ($upsell): ?>
    <h2>You may also like</h2>
    <div class="grid">
    <?php foreach ($upsell as $pid): $p = getProduct((int)$pid); if ($p): ?>
        <?php includeSection('product-card', ['product' => $p]); ?>
    <?php endif; endforeach; ?>
    </div>
<?php endif; ?>
<a href="/">Return to store</a>
