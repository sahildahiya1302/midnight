<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once dirname(__DIR__, 3) . '/functions.php';

$items = $_SESSION['cart'] ?? [];
ob_start();
?>
<h1>Your Shopping Cart</h1>
<?php if ($items): ?>
<table class="cart-table" aria-label="Shopping cart items">
    <thead>
        <tr>
            <th scope="col">Product</th>
            <th scope="col">Quantity</th>
            <th scope="col">Price</th>
            <th scope="col">Total</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $id => $qty): ?>
        <?php
        $product = getProduct((int)$id);
        if (!$product) continue;
        $price = $product['price'] ?? 0;
        $total = $price * $qty;
        ?>
        <tr>
            <td>
                <img src="<?php echo e($product['image'] ?? asset('images/placeholder.png')); ?>" alt="<?php echo e($product['title']); ?>" class="cart-product-image" />
                <?php echo e($product['title']); ?>
            </td>
            <td><?php echo e($qty); ?></td>
            <td><?php echo e(number_format($price, 2)); ?></td>
            <td><?php echo e(number_format($total, 2)); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p>Your cart is empty.</p>
<?php endif; ?>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/theme.php';
