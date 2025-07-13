<?php
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

$pageTitle = 'Discounts Overview';
require __DIR__ . '/../components/header.php';
?>
<h1>Discounts</h1>
<p>Overview of available promotions.</p>
<ul>
  <li><a href="discounts.php">Discounts</a></li>
</ul>
<?php require __DIR__ . '/../components/footer.php'; ?>
