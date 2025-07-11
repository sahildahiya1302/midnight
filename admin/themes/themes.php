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

// Handle form submissions for activating, deleting or duplicating themes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $themeId = $_POST['theme_id'] ?? null;

    if ($action === 'activate' && $themeId) {
        // Deactivate all themes
        db_query('UPDATE themes SET active = 0');
        // Activate selected theme
        db_query('UPDATE themes SET active = 1 WHERE id = :id', [':id' => $themeId]);
        $_SESSION['flash_message'] = 'Theme activated successfully.';
    } elseif ($action === 'delete' && $themeId) {
        db_query('DELETE FROM themes WHERE id = :id', [':id' => $themeId]);
        $_SESSION['flash_message'] = 'Theme deleted successfully.';
    } elseif ($action === 'duplicate' && $themeId) {
        $theme = db_query('SELECT name, layout_json, settings_json FROM themes WHERE id = :id', [':id' => $themeId])->fetch(PDO::FETCH_ASSOC);
        if ($theme) {
            $newName = $theme['name'] . ' Copy ' . date('Y-m-d H:i');
            db_query('INSERT INTO themes (name, layout_json, settings_json, active) VALUES (:name, :layout, :settings, 0)', [
                ':name' => $newName,
                ':layout' => $theme['layout_json'] ?? '',
                ':settings' => $theme['settings_json'] ?? ''
            ]);
            $_SESSION['flash_message'] = 'Theme duplicated.';
        }
    }
    header('Location: /admin/themes.php');
    exit;
}

// Fetch all themes
$themes = db_query('SELECT id, name, created_at, active FROM themes ORDER BY created_at DESC')->fetchAll();
$activeThemeId = 0;
foreach ($themes as $t) {
    if ($t['active']) { $activeThemeId = (int)$t['id']; break; }
}
if (!$activeThemeId && !empty($themes)) {
    $activeThemeId = (int)$themes[0]['id'];
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

<iframe src="/?preview_theme_id=<?= $activeThemeId ?>" style="width:100%;height:400px;border:1px solid #ccc;margin-bottom:20px;"></iframe>

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
            <td><?= htmlspecialchars($theme['name']) ?><?= $theme['active'] ? ' (active)' : '' ?></td>
            <td><?= htmlspecialchars($theme['created_at']) ?></td>
            <td>
                <a href="/admin/themes/theme-editor.php?theme_id=<?= $theme['id'] ?>" target="_blank">Edit</a>
                <form method="post" style="display:inline; margin-left:5px;">
                    <input type="hidden" name="action" value="activate">
                    <input type="hidden" name="theme_id" value="<?= $theme['id'] ?>">
                    <button type="submit">Activate</button>
                </form>
                <form method="post" style="display:inline; margin-left:5px;">
                    <input type="hidden" name="action" value="duplicate">
                    <input type="hidden" name="theme_id" value="<?= $theme['id'] ?>">
                    <button type="submit">Duplicate</button>
                </form>
                <form method="post" style="display:inline; margin-left:5px;" onsubmit="return confirm('Are you sure you want to delete this theme?');">
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
