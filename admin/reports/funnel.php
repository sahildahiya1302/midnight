<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /backend/auth/login.php');
    exit;
}

$pageTitle = 'Conversion Funnel';
require __DIR__ . '/../components/header.php';
?>

<h1>Conversion Funnel</h1>
<p>Analyze the funnel from product view to purchase.</p>

<?php require __DIR__ . '/../components/footer.php'; ?>
