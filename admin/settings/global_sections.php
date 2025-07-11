<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /backend/auth/login.php');
    exit;
}

$pageTitle = 'Global Sections';
$current = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = db()->prepare('SELECT * FROM global_sections WHERE id=?');
    $stmt->execute([$id]);
    $current = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $id = intval($_POST['id']);
        db_query('DELETE FROM global_sections WHERE id = ?', [$id]);
    } elseif (isset($_POST['id'])) {
        // update existing
        $id = intval($_POST['id']);
        $handle = slugify(trim($_POST['handle']));
        $title = trim($_POST['title']);
        $type = trim($_POST['type']);
        $json = $_POST['section_json'];
        $pages = trim($_POST['used_on_pages']);
        db_query('UPDATE global_sections SET handle=?, title=?, type=?, section_json=?, used_on_pages=? WHERE id=?', [$handle, $title, $type, $json, $pages, $id]);
    } elseif (isset($_POST['handle'], $_POST['title'], $_POST['type'], $_POST['section_json'])) {
        $handle = slugify(trim($_POST['handle']));
        $title = trim($_POST['title']);
        $type = trim($_POST['type']);
        $json = $_POST['section_json'];
        $pages = trim($_POST['used_on_pages'] ?? '');
        db_query('INSERT INTO global_sections (handle, title, type, section_json, used_on_pages) VALUES (?, ?, ?, ?, ?)', [$handle, $title, $type, $json, $pages]);
    }
}

$sections = db_query('SELECT * FROM global_sections ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
include __DIR__ . '/../components/header.php';
?>
<h1>Global Sections</h1>
<form method="post" class="mb-3">
    <input type="hidden" name="id" value="<?= $current['id'] ?? '' ?>">
    <input type="text" name="handle" placeholder="Handle" required value="<?= htmlspecialchars($current['handle'] ?? '') ?>">
    <input type="text" name="title" placeholder="Title" required value="<?= htmlspecialchars($current['title'] ?? '') ?>">
    <input type="text" name="type" placeholder="Section Type" required value="<?= htmlspecialchars($current['type'] ?? '') ?>">
    <input type="text" name="used_on_pages" placeholder="Page slugs (comma separated)" value="<?= htmlspecialchars($current['used_on_pages'] ?? '') ?>">
    <textarea name="section_json" placeholder='{"settings":{}}' required><?= htmlspecialchars($current['section_json'] ?? '') ?></textarea>
    <button class="btn btn-primary"><?= $current ? 'Update' : 'Create' ?></button>
</form>
<table class="table">
<tr><th>ID</th><th>Handle</th><th>Type</th><th>Pages</th><th>Actions</th></tr>
<?php foreach ($sections as $s): ?>
<tr>
<td><?= htmlspecialchars($s['id']) ?></td>
<td><?= htmlspecialchars($s['handle']) ?></td>
<td><?= htmlspecialchars($s['type']) ?></td>
<td><?= htmlspecialchars($s['used_on_pages']) ?></td>
<td>
<a class="btn btn-sm btn-primary" href="?edit=<?= $s['id'] ?>">Edit</a>
<form method="post" style="display:inline">
    <input type="hidden" name="id" value="<?= $s['id'] ?>">
    <button class="btn btn-sm btn-danger" name="delete" value="1" onclick="return confirm('Delete?')">Delete</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</table>
<?php include __DIR__ . '/../components/footer.php'; ?>
