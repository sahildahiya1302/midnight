<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /backend/auth/login.php');
    exit;
}

$message = '';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['asset_file'])) {
    $uploadDir = __DIR__ . '/../../themes/default/assets/';
    $uploadFile = $uploadDir . basename($_FILES['asset_file']['name']);

    if (move_uploaded_file($_FILES['asset_file']['tmp_name'], $uploadFile)) {
        $message = 'File uploaded successfully.';
    } else {
        $message = 'File upload failed.';
    }
}

// Handle file deletion
if (isset($_GET['delete'])) {
    $fileToDelete = basename($_GET['delete']);
    $filePath = __DIR__ . '/../../themes/default/assets/' . $fileToDelete;
    if (file_exists($filePath)) {
        unlink($filePath);
        $message = 'File deleted successfully.';
    } else {
        $message = 'File not found.';
    }
}

// List files in assets directory
$assetsDir = __DIR__ . '/../../themes/default/assets/';
$files = array_diff(scandir($assetsDir), ['.', '..']);

require __DIR__ . '/../components/header.php';
?>

<h1>Theme Assets</h1>

<?php if ($message): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<h2>Upload New Asset</h2>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="asset_file" required>
    <button type="submit">Upload</button>
</form>

<h2>Existing Assets</h2>
<ul>
    <?php foreach ($files as $file): ?>
        <li>
            <?= htmlspecialchars($file) ?>
            <a href="?delete=<?= urlencode($file) ?>" onclick="return confirm('Delete this file?')">Delete</a>
        </li>
    <?php endforeach; ?>
</ul>

<?php
require __DIR__ . '/../components/footer.php';
?>
