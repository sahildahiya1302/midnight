<?php
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /backend/auth/login.php');
    exit;
}

$pageTitle = 'Orders';
require __DIR__ . '/../components/header.php';
?>
<h1>Orders Panel</h1>
<p>This is a placeholder for order management.</p>
<?php require __DIR__ . '/../components/footer.php'; ?>
