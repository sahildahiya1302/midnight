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

$pageTitle = 'Dashboard';

// Fetch some summary data for dashboard display
$totalCustomers = db_query('SELECT COUNT(*) FROM customers')->fetchColumn();
$totalOrders = db_query('SELECT COUNT(*) FROM orders')->fetchColumn();
$totalProducts = db_query('SELECT COUNT(*) FROM products')->fetchColumn();
$totalRevenue = db_query('SELECT SUM(total_amount) FROM orders WHERE status = "completed"')->fetchColumn();

require __DIR__ . '/../components/header.php';
?>

<h1>Dashboard</h1>

<div class="dashboard-summary">
    <div class="summary-item">
        <h2>Total Customers</h2>
        <p><?= (int)$totalCustomers ?></p>
    </div>
    <div class="summary-item">
        <h2>Total Orders</h2>
        <p><?= (int)$totalOrders ?></p>
    </div>
    <div class="summary-item">
        <h2>Total Products</h2>
        <p><?= (int)$totalProducts ?></p>
    </div>
    <div class="summary-item">
        <h2>Total Revenue</h2>
        <p>$<?= number_format((float)$totalRevenue, 2) ?></p>
    </div>
</div>

<?php
require __DIR__ . '/../components/footer.php';
?>
