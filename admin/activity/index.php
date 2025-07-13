<?php
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

$pageTitle = 'Activity Overview';
require __DIR__ . '/../components/header.php';
?>
<h1>Activity</h1>
<p>Quick links:</p>
<ul>
  <li><a href="activity_log.php">View Activity Log</a></li>
</ul>
<?php require __DIR__ . '/../components/footer.php'; ?>
