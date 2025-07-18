<?php
$product = $product ?? [];
$variants = $variants ?? [];
$images = $images ?? [];
$related = $related ?? [];
?>

<div class="product-page">
    <div class="product-container">
        <div class="product-images">
            <?php if (!empty($images)): ?>
                <img src="<?= htmlspecialchars($images[0]['src'] ?? '/assets/images/placeholder.png') ?>" 
                     alt="<?= htmlspecialchars($product['title'] ?? '') ?>" 
                     class="main-image">
            <?php else: ?>
                <img src="/assets/images/placeholder.png" alt="Product Image" class="main-image">
            <?php endif; ?>
        </div>

        <div class="product-details">
            <h1><?= htmlspecialchars($product['title'] ?? '') ?></h1>
            <p class="price">₹<?= number_format($variants[0]['price'] ?? 0, 2) ?></p>
            
            <?php if (!empty($product['description'])): ?>
                <div class="description">
                    <?= nl2br(htmlspecialchars($product['description'])) ?>
                </div>
            <?php endif; ?>

            <div class="add-to-cart-section">
                <button class="btn btn-primary add-to-cart-button" 
                        data-product-id="<?= $product['id'] ?? '' ?>">
                    Add to Cart
                </button>
            </div>
        </div>
    </div>

    <?php if (!empty($related)): ?>
        <div class="related-products">
            <h2>Related Products</h2>
            <div class="product-grid">
                <?php foreach ($related as $relatedProduct): ?>
                    <div class="product-card">
                        <a href="/products/<?= htmlspecialchars($relatedProduct['handle'] ?? '') ?>">
                            <img src="<?= htmlspecialchars($relatedProduct['image'] ?? '/assets/images/placeholder.png') ?>" 
                                 alt="<?= htmlspecialchars($relatedProduct['title'] ?? '') ?>">
                            <h3><?= htmlspecialchars($relatedProduct['title'] ?? '') ?></h3>
                            <p>₹<?= number_format($relatedProduct['price'] ?? 0, 2) ?></p>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="/themes/default/assets/js/cart-popup.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addToCartBtn = document.querySelector('.add-to-cart-button');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', async function() {
            const productId = this.dataset.productId;
            if (!productId) return;

            try {
                const response = await fetch('/api/cart.php?action=add', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({product_id: productId, quantity: 1})
                });
                const data = await response.json();
                
                if (data.success) {
                    alert('Product added to cart!');
                    // Update cart count
                    const cartCount = document.getElementById('cart-count');
                    if (cartCount) {
                        cartCount.textContent = Object.values(data.cart).reduce((a, b) => a + b, 0);
                    }
                } else {
                    alert('Failed to add to cart: ' + data.message);
                }
            } catch (error) {
                alert('Error adding to cart: ' + error.message);
            }
        });
    }
});
</script>
