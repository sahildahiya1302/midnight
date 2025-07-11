<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /backend/auth/login.php');
    exit;
}
$section = $_GET['section'] ?? '';
if (!preg_match('/^[a-zA-Z0-9_-]+$/', $section)) {
    die('Invalid section');
}
$path = __DIR__ . "/../../themes/default/sections/{$section}.php";
if (!is_file($path)) {
    die('Section not found');
}
$code = file_get_contents($path);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new = $_POST['code'] ?? '';
    file_put_contents($path, $new);
    header('Location: edit-code.php?section=' . urlencode($section));
    exit;
}
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
<textarea name="code"><?php echo htmlspecialchars($code); ?></textarea>
<br>
<button type="submit">Save</button>
</form>
</body>
</html>
