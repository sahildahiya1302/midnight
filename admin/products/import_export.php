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

// Handle CSV import
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['import_file'])) {
    $fileTmpPath = $_FILES['import_file']['tmp_name'];
    $fileName = $_FILES['import_file']['name'];
    $fileSize = $_FILES['import_file']['size'];
    $fileType = $_FILES['import_file']['type'];

    if ($fileType === 'text/csv' || pathinfo($fileName, PATHINFO_EXTENSION) === 'csv') {
        $handle = fopen($fileTmpPath, 'r');
        if ($handle !== false) {
            $header = fgetcsv($handle);
            $expectedHeaders = ['sku', 'name', 'description', 'price', 'quantity'];
            if ($header === $expectedHeaders) {
                $importedCount = 0;
                while (($data = fgetcsv($handle)) !== false) {
                    // Sanitize and insert/update product
                    $sku = $data[0];
                    $name = $data[1];
                    $description = $data[2];
                    $price = floatval($data[3]);
                    $quantity = intval($data[4]);

                    // Check if product exists
                    $stmt = db_query('SELECT id FROM products WHERE sku = ?', [$sku]);
                    $existing = $stmt->fetch();
                    if ($existing) {
                        // Update product
                        db_query('UPDATE products SET name = ?, description = ?, price = ?, quantity = ? WHERE sku = ?', [$name, $description, $price, $quantity, $sku]);
                    } else {
                        // Insert new product
                        db_query('INSERT INTO products (sku, name, description, price, quantity) VALUES (?, ?, ?, ?, ?)', [$sku, $name, $description, $price, $quantity]);
                    }
                    $importedCount++;
                }
                $message = "Successfully imported $importedCount products.";
            } else {
                $message = 'Invalid CSV header. Expected: ' . implode(', ', $expectedHeaders);
            }
            fclose($handle);
        } else {
            $message = 'Failed to open uploaded file.';
        }
    } else {
        $message = 'Please upload a valid CSV file.';
    }
}

// Handle CSV export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="products_export.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['sku', 'name', 'description', 'price', 'quantity']);

    $stmt = db_query('SELECT sku, name, description, price, quantity FROM products');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}

require __DIR__ . '/../components/header.php';
?>

<h1>Import / Export Products</h1>

<?php if ($message): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<h2>Import Products (CSV)</h2>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="import_file" accept=".csv" required>
    <button type="submit">Import</button>
</form>

<h2>Export Products</h2>
<a href="?export=csv">Download CSV Export</a>

<?php
require __DIR__ . '/../components/footer.php';
?>
