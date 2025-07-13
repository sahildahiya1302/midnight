<?php
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /backend/auth/login.php');
    exit;
}

$pageTitle = 'Pages & Content';
require __DIR__ . '/../components/header.php';
?>
<h1>Pages &amp; Content</h1>
<p>This is a placeholder for pages and content management.</p>
<?php require __DIR__ . '/../components/footer.php'; ?>
