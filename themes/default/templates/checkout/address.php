<?php
setSecurityHeaders();
?>
<h1>Shipping Address</h1>
<form action="/checkout/payment" method="post">
    <label>Name <input type="text" name="name" required></label>
    <label>Email <input type="email" name="email" required></label>
    <label>Phone <input type="tel" name="phone" required></label>
    <label>Address <textarea name="address" required></textarea></label>
    <button type="submit">Continue to Payment</button>
</form>
