<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once dirname(__DIR__, 3) . '/functions.php';

$ids = getCompareList();
$products = array_map('getProduct', $ids);

ob_start();
?>
<h1>Compare Products</h1>
<?php if ($products): ?>
<table class="compare-table" aria-label="Product comparison">
    <thead>
        <tr>
            <th>Product</th>
            <th>Price</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($products as $p): if(!$p) continue; ?>
        <tr>
            <td>
                <a href="/products/<?= e($p['handle']) ?>">
                    <?= e($p['title']) ?>
                </a>
            </td>
            <td><?= number_format((float)$p['price'],2) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p>No products to compare.</p>
<?php endif; ?>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/theme.php';
