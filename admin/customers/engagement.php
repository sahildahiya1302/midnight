<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /backend/auth/login.php');
    exit;
}

$pageTitle = 'Email/SMS Engagement';
require __DIR__ . '/../components/header.php';
?>

<h1>Email/SMS Engagement</h1>
<p>View email and SMS engagement metrics.</p>

<?php require __DIR__ . '/../components/footer.php'; ?>
