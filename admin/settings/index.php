<?php
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

$pageTitle = 'Settings Overview';
require __DIR__ . '/../components/header.php';
?>
<h1>Settings</h1>
<p>Configure store preferences.</p>
<ul>
  <li><a href="settings.php">Settings</a></li>
  <li><a href="checkout_settings.php">Checkout Success</a></li>
  <li><a href="presets.php">Section Presets</a></li>
  <li><a href="global_sections.php">Global Sections</a></li>
</ul>
<?php require __DIR__ . '/../components/footer.php'; ?>
