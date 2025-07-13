<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /backend/auth/login.php');
    exit;
}

$pageTitle = 'Customer Insights';
require __DIR__ . '/../components/header.php';
?>

<h1>Customer Insights</h1>
<p>Analyze customer behavior and lifetime value.</p>

<?php require __DIR__ . '/../components/footer.php'; ?>
