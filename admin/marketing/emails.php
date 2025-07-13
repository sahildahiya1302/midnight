<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /backend/auth/login.php');
    exit;
}

$pageTitle = 'Email Blasts';
require __DIR__ . '/../components/header.php';
?>

<h1>Email Blasts</h1>
<p>Send marketing emails to customer segments.</p>

<?php require __DIR__ . '/../components/footer.php'; ?>
