<?php
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

$pageTitle = 'Customers Overview';
require __DIR__ . '/../components/header.php';
?>
<h1>Customers</h1>
<p>Manage your customer base.</p>
<ul>
  <li><a href="customers.php">Customers</a></li>
</ul>
<?php require __DIR__ . '/../components/footer.php'; ?>
