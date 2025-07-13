<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /backend/auth/login.php');
    exit;
}

$pageTitle = 'Media Manager';
require __DIR__ . '/../components/header.php';
?>

<h1>Media Manager</h1>
<p>Upload and organize media files.</p>

<?php require __DIR__ . '/../components/footer.php'; ?>
