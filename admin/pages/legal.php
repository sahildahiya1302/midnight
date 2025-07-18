<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /backend/auth/login.php');
    exit;
}

$pageTitle = 'Legal Pages';
require __DIR__ . '/../components/header.php';
?>

<h1>Legal Pages</h1>
<p>Manage terms of service, privacy policy and other legal pages.</p>

<?php require __DIR__ . '/../components/footer.php'; ?>
