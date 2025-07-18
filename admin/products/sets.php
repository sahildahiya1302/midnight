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

// Handle create, update, delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $setId = intval($_POST['set_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');

    if ($action === 'create' && $name !== '') {
        db_query('INSERT INTO product_sets (name) VALUES (?)', [$name]);
    } elseif ($action === 'update' && $setId > 0 && $name !== '') {
        db_query('UPDATE product_sets SET name = ? WHERE id = ?', [$name, $setId]);
    } elseif ($action === 'delete' && $setId > 0) {
        db_query('DELETE FROM product_sets WHERE id = ?', [$setId]);
        db_query('DELETE FROM product_set_items WHERE set_id = ?', [$setId]);
    }
}

// Fetch all product sets
$sets = db_query('SELECT id, name FROM product_sets ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/../components/header.php';
?>

<h1>Product Sets</h1>

<h2>Create New Set</h2>
<form method="post">
    <input type="hidden" name="action" value="create">
    <input type="text" name="name" placeholder="Set Name" required>
    <button type="submit">Create</button>
</form>

<h2>Existing Sets</h2>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sets as $set): ?>
        <tr>
            <form method="post">
                <td>
                    <input type="text" name="name" value="<?= htmlspecialchars($set['name']) ?>" required>
                    <input type="hidden" name="set_id" value="<?= (int)$set['id'] ?>">
                </td>
                <td>
                    <button type="submit" name="action" value="update">Update</button>
                    <button type="submit" name="action" value="delete" onclick="return confirm('Are you sure?')">Delete</button>
                </td>
            </form>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
require __DIR__ . '/../components/footer.php';
?>
