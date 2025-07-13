<?php
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

$pageTitle = 'Plugins Overview';
require __DIR__ . '/../components/header.php';
?>
<h1>Plugins</h1>
<p>Manage installed plugins.</p>
<ul>
  <li><a href="plugins.php">Plugins</a></li>
  <li><a href="plugins_add.php">Add Plugin</a></li>
  <li><a href="plugins_delete.php">Delete Plugin</a></li>
  <li><a href="plugins_toggle.php">Toggle Plugin</a></li>
</ul>
<?php require __DIR__ . '/../components/footer.php'; ?>
