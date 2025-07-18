<?php
declare(strict_types=1);

if (!isset($_SESSION['user_id'])) {
    header('Location: /backend/auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Admin'; ?></title>
    <link rel="stylesheet" href="/admin/assets/admin.css">
    <link rel="icon" href="/admin/assets/favicon.ico" type="image/x-icon">
</head>
<body>
<div class="container">
<?php include __DIR__ . '/nav.php'; ?>
<div class="content">
