<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /backend/auth/login.php');
    exit;
}

$pageTitle = 'UTM & Funnel Tracking';
require __DIR__ . '/../components/header.php';
?>

<h1>UTM & Funnel Tracking</h1>
<p>Monitor campaign attribution and conversion funnels.</p>

<?php require __DIR__ . '/../components/footer.php'; ?>
