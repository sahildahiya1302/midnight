<?php
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

$pageTitle = 'Orders Overview';
require __DIR__ . '/../components/header.php';
?>
<h1>Orders</h1>
<p>Access order management tools below.</p>
<ul>
  <li><a href="orders.php">Orders</a></li>
</ul>
<?php require __DIR__ . '/../components/footer.php'; ?>
