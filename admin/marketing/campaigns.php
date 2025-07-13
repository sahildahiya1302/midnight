<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /backend/auth/login.php');
    exit;
}

$pageTitle = 'Ad Campaigns';
require __DIR__ . '/../components/header.php';
?>

<h1>Ad Campaigns</h1>
<p>Create and manage advertising campaigns.</p>

<?php require __DIR__ . '/../components/footer.php'; ?>
