<?php
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

$pageTitle = 'Marketing Overview';
require __DIR__ . '/../components/header.php';
?>
<h1>Marketing</h1>
<p>Campaign and analytics tools.</p>
<ul>
  <li><a href="marketing.php">Marketing Dashboard</a></li>
</ul>
<?php require __DIR__ . '/../components/footer.php'; ?>
