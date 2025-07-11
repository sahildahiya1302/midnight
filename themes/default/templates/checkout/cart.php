<?php
// Simple cart review page
setSecurityHeaders();
?>
<h1>Cart Review</h1>
<form action="/checkout/address" method="post">
    <table class="cart-table">
        <tr><th>Product</th><th>Qty</th><th>Price</th></tr>
        <?php foreach ($_SESSION['cart'] ?? [] as $item): ?>
        <tr>
            <td><?php echo e($item['title'] ?? ''); ?></td>
            <td><?php echo intval($item['quantity'] ?? 1); ?></td>
            <td><?php echo number_format((float)($item['price'] ?? 0), 2); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <label>Coupon Code
        <input type="text" name="coupon">
    </label>
    <button type="submit">Continue to Address</button>
</form>
