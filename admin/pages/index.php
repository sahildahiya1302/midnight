<?php
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

$pageTitle = 'Pages Overview';
require __DIR__ . '/../components/header.php';
?>
<h1>Pages</h1>
<p>Manage site pages and templates.</p>
<ul>
  <li><a href="pages.php">Pages</a></li>
  <li><a href="templates.php">Page Templates</a></li>
</ul>
<?php require __DIR__ . '/../components/footer.php'; ?>
