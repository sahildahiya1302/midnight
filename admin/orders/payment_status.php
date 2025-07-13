<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /backend/auth/login.php');
    exit;
}

$pageTitle = 'Payment Status';
require __DIR__ . '/../components/header.php';
?>

<h1>Payment Status</h1>
<p>View and update payment status for orders.</p>

<?php require __DIR__ . '/../components/footer.php'; ?>
