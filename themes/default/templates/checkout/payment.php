<?php
setSecurityHeaders();
?>
<h1>Payment</h1>
<form action="/checkout/success" method="post">
    <label>Payment Method
        <select name="payment_method">
            <option value="cod">Cash on Delivery</option>
            <option value="stripe">Credit Card</option>
        </select>
    </label>
    <button type="submit">Place Order</button>
</form>
