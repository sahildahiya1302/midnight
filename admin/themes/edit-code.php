<?php
session_start();
require_once __DIR__ . '/../../functions.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: /backend/auth/login.php');
    exit;
}

$section = $_GET['section'] ?? '';
if (!preg_match('/^[a-zA-Z0-9_-]+$/', $section)) {
    die('Invalid section');
}

$baseDir = realpath(__DIR__ . '/../../themes/default/sections');
$path = realpath($baseDir . "/{$section}.php");
if (!$path || strpos($path, $baseDir) !== 0 || !is_file($path)) {
    die('Section not found');
}

$code = file_get_contents($path);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token');
    }
    $new = $_POST['code'] ?? '';
    file_put_contents($path, $new);
    header('Location: edit-code.php?section=' . urlencode($section));
    exit;
}

$csrfToken = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Code - <?php echo htmlspecialchars($section); ?></title>
<style>
textarea{width:100%;height:80vh;font-family:monospace;}
</style>
</head>
<body>
<h1>Edit Code - <?php echo htmlspecialchars($section); ?></h1>
<form method="post">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <textarea name="code"><?php echo htmlspecialchars($code); ?></textarea>
    <br>
    <button type="submit">Save</button>
</form>
</body>
</html>
