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

$pageTitle = 'Themes';

// Handle form submissions for activating or deleting themes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $themeId = $_POST['theme_id'] ?? null;

    if ($action === 'activate' && $themeId) {
        // Deactivate all themes
        db_query('UPDATE themes SET active = 0');
        // Activate selected theme
        db_query('UPDATE themes SET active = 1 WHERE id = :id', [':id' => $themeId]);
        $_SESSION['flash_message'] = 'Theme activated successfully.';
    } elseif ($action === 'duplicate' && $themeId) {
        $theme = db_query('SELECT name, settings FROM themes WHERE id = :id', [':id' => $themeId])->fetch(PDO::FETCH_ASSOC);
        if ($theme) {
            db_query('INSERT INTO themes (name, settings) VALUES (:name, :settings)', [
                ':name' => $theme['name'] . ' Copy',
                ':settings' => $theme['settings']
            ]);
            $_SESSION['flash_message'] = 'Theme duplicated successfully.';
        }
    } elseif ($action === 'delete' && $themeId) {
        db_query('DELETE FROM themes WHERE id = :id', [':id' => $themeId]);
        $_SESSION['flash_message'] = 'Theme deleted successfully.';
    }
    header('Location: /admin/themes.php');
    exit;
}

// Fetch all themes
$themes = db_query('SELECT id, name, created_at FROM themes ORDER BY created_at DESC')->fetchAll();
if (!$themes) {
    $themes[] = ['id' => 1, 'name' => 'Custom Theme', 'created_at' => date('Y-m-d'), 'active' => 1];
}

foreach ($themes as &$theme) {
    if (!isset($theme['active'])) {
        $theme['active'] = 0; // Set default value if 'active' is not set
    }
}
unset($theme); // Unset the reference

require __DIR__ . '/../components/header.php';
?>

<h1>Themes</h1>
<div class="theme-actions">
    <a href="create-theme.php" class="btn">Create Theme</a>
    <a href="upload-theme.php" class="btn">Upload Theme</a>
</div>

<?php if (!empty($_SESSION['flash_message'])): ?>
    <div class="flash-message"><?= htmlspecialchars($_SESSION['flash_message']) ?></div>
    <?php unset($_SESSION['flash_message']); ?>
<?php endif; ?>

<!-- Themes list -->
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($themes as $theme): ?>
        <tr>
            <td><?= htmlspecialchars($theme['name']) ?></td>
            <td><?= htmlspecialchars($theme['created_at']) ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="action" value="activate">
                    <input type="hidden" name="theme_id" value="<?= $theme['id'] ?>">
                    <button type="submit">Activate</button>
                </form>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="action" value="duplicate">
                    <input type="hidden" name="theme_id" value="<?= $theme['id'] ?>">
                    <button type="submit">Duplicate</button>
                </form>
                <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this theme?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="theme_id" value="<?= $theme['id'] ?>">
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
require __DIR__ . '/../components/footer.php';
?>
