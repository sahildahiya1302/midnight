<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /backend/auth/login.php');
    exit;
}

$pageTitle = 'Customer LTV & Segments';
require __DIR__ . '/../components/header.php';
?>

<h1>Customer LTV & Segments</h1>
<p>Analyze customer lifetime value by segment.</p>

<?php require __DIR__ . '/../components/footer.php'; ?>
