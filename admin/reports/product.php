<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /backend/auth/login.php');
    exit;
}

$pageTitle = 'Product Performance';
require __DIR__ . '/../components/header.php';
?>

<h1>Product Performance</h1>
<p>Report on top selling and low performing products.</p>

<?php require __DIR__ . '/../components/footer.php'; ?>
