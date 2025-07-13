<?php
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

$pageTitle = 'Products Overview';
require __DIR__ . '/../components/header.php';
?>
<h1>Products</h1>
<p>Manage your catalog from here.</p>
<ul>
  <li><a href="products.php">Products</a></li>
  <li><a href="collections.php">Collections</a></li>
</ul>
<?php require __DIR__ . '/../components/footer.php'; ?>
