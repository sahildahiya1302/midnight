<?php
declare(strict_types=1);
ob_start();
?>
<section class="gift-card" role="main" aria-labelledby="gift-card-title">
    <h1 id="gift-card-title">Gift Card</h1>
    <p>Give the perfect gift with our store gift cards. Redeemable online and in-store.</p>
    <img src="/themes/default/assets/images/gift-card.png" alt="Gift Card" class="gift-card-image" />
    <h2>How to Redeem</h2>
    <ol>
        <li>Add items to your cart.</li>
        <li>Enter your gift card code at checkout.</li>
        <li>Enjoy your purchase!</li>
    </ol>
</section>
<style>
.gift-card {
    max-width: 600px;
    margin: 40px auto;
    padding: 20px;
    text-align: center;
}
.gift-card-image {
    max-width: 100%;
    height: auto;
    margin: 20px 0;
}
</style>
<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/theme.php";
