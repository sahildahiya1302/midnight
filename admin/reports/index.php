<?php
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

$pageTitle = 'Reports Overview';
require __DIR__ . '/../components/header.php';
?>
<h1>Reports</h1>
<p>Summary of analytics reports.</p>
<ul>
  <li><a href="reports.php">Reports</a></li>
</ul>
<?php require __DIR__ . '/../components/footer.php'; ?>
