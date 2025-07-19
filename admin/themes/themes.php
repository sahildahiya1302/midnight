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

// Recursively copy a directory
function copy_dir(string $src, string $dest): void
{
    if (!is_dir($dest)) {
        mkdir($dest, 0777, true);
    }
    foreach (scandir($src) as $file) {
        if ($file === '.' || $file === '..') continue;
        $srcPath = "$src/$file";
        $destPath = "$dest/$file";
        if (is_dir($srcPath)) {
            copy_dir($srcPath, $destPath);
        } else {
            copy($srcPath, $destPath);
        }
    }
}

// Handle form submissions for activating or deleting themes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $themeId = $_POST['theme_id'] ?? null;

    if ($action === 'activate' && $themeId) {
        db_query('UPDATE themes SET active = 0');
        db_query('UPDATE themes SET active = 1 WHERE id = :id', [':id' => $themeId]);
        $_SESSION['flash_message'] = 'Theme activated successfully.';
    } elseif ($action === 'duplicate' && $themeId) {
        $theme = db_query('SELECT name, settings FROM themes WHERE id = :id', [':id' => $themeId])->fetch(PDO::FETCH_ASSOC);
        if ($theme) {
            db_query('INSERT INTO themes (name, settings) VALUES (:name, :settings)', [
                ':name' => $theme['name'] . ' Copy',
                ':settings' => $theme['settings']
            ]);
            $newId = (int)db()->lastInsertId();
            $srcDir = __DIR__ . '/../../themes/default';
            $destDir = __DIR__ . '/../../themes/theme' . $newId;
            copy_dir($srcDir, $destDir);
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
$themes = db_query('SELECT id, name, created_at, active FROM themes ORDER BY created_at DESC')->fetchAll();
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


<?php
$activeTheme = null;
$otherThemes = [];
foreach ($themes as $t) {
    if (!empty($t['active'])) {
        $activeTheme = $t;
    } else {
        $otherThemes[] = $t;
    }
}
?>

<?php if ($activeTheme): ?>
<?php
    $score = '';
    $tips = [];
    $desktopShot = null;
    $mobileShot = null;
    $insightsDesktop = fetchPageSpeedInsights(BASE_URL, 'desktop');
    $insightsMobile = fetchPageSpeedInsights(BASE_URL, 'mobile');
    if ($insightsDesktop) {
        $desktopShot = $insightsDesktop['screenshot'] ?? null;
    }
    if ($insightsMobile) {
        $score = $insightsMobile['score'] ?? '';
        $tips = $insightsMobile['tips'] ?? [];
        $mobileShot = $insightsMobile['screenshot'] ?? null;
    }
    $placeholder = '/themes/default/assets/images/placeholder.png';
    $screenshot = $desktopShot ?: $placeholder;
    $mobileShot = $mobileShot ?: $screenshot;
?>
<div class="active-theme">
    <h2><?= htmlspecialchars($activeTheme['name']) ?></h2>
    <div class="performance">
        <strong>Lighthouse Score: <?= htmlspecialchars($score) ?></strong>
        <?php if ($tips): ?>
        <ul class="tips">
            <?php foreach ($tips as $tip): ?>
            <li><?= htmlspecialchars($tip) ?></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>
    <div class="screenshot">
        <img src="<?= $screenshot ?>" alt="desktop" class="desktop">
        <img src="<?= $mobileShot ?>" alt="mobile" class="mobile">
    </div>
    <div class="actions">
        <a class="btn" href="/admin/themes/theme-editor.php?theme_id=<?= $activeTheme['id'] ?>&page=index" target="_blank">Customize</a>
        <button disabled>Activated</button>
        <div class="menu">
            <button class="bi bi-three-dots-vertical" type="button"></button>
            <div class="menu-items">
                <a href="/admin/themes/code-editor.php?theme_id=<?= $activeTheme['id'] ?>" target="_blank">Edit Code</a>
                <form method="post">
                    <input type="hidden" name="action" value="duplicate">
                    <input type="hidden" name="theme_id" value="<?= $activeTheme['id'] ?>">
                    <button type="submit">Duplicate</button>
                </form>
                <form method="post" onsubmit="return confirm('Delete this theme?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="theme_id" value="<?= $activeTheme['id'] ?>">
                    <button type="submit">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="theme-list">
<?php foreach ($otherThemes as $theme): ?>
    <div class="theme-card compact">
        <div class="info">
            <h3><?= htmlspecialchars($theme['name']) ?></h3>
            <small><?= htmlspecialchars(date('M j, Y', strtotime($theme['created_at']))) ?></small>
            <div class="actions">
                <a class="btn" href="/admin/themes/theme-editor.php?theme_id=<?= $theme['id'] ?>&page=index" target="_blank">Customize</a>
                <form method="post">
                    <input type="hidden" name="action" value="activate">
                    <input type="hidden" name="theme_id" value="<?= $theme['id'] ?>">
                    <button type="submit">Activate</button>
                </form>
                <div class="menu">
                    <button class="bi bi-three-dots-vertical" type="button"></button>
                    <div class="menu-items">
                        <a href="/admin/themes/code-editor.php?theme_id=<?= $theme['id'] ?>" target="_blank">Edit Code</a>
                        <form method="post">
                            <input type="hidden" name="action" value="duplicate">
                            <input type="hidden" name="theme_id" value="<?= $theme['id'] ?>">
                            <button type="submit">Duplicate</button>
                        </form>
                        <form method="post" onsubmit="return confirm('Delete this theme?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="theme_id" value="<?= $theme['id'] ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>

<?php
require __DIR__ . '/../components/footer.php';
?>
