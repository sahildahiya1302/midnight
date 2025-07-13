<?php
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

$pageTitle = 'Themes Overview';
require __DIR__ . '/../components/header.php';
?>
<h1>Themes</h1>
<p>Theme management options.</p>
<ul>
  <li><a href="themes.php">Themes</a></li>
  <li><a href="theme-editor.php" target="_blank">Theme Editor</a></li>
  <li><a href="../editor/theme-settings.php">Theme Settings</a></li>
</ul>
<?php require __DIR__ . '/../components/footer.php'; ?>
