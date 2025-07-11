<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../functions.php';

$action = $_GET['action'] ?? '';
$cart = &$_SESSION['cart'];
$cart = $cart ?? [];

function json($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

if ($action === 'add') {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $pid = (int)($input['product_id'] ?? 0);
    $qty = (int)($input['quantity'] ?? 1);
    if ($pid > 0) {
        $cart[$pid] = ($cart[$pid] ?? 0) + max(1, $qty);
        json(['success' => true]);
    }
    json(['success' => false, 'message' => 'Invalid product']);
} elseif ($action === 'remove') {
    $pid = (int)($_GET['product_id'] ?? 0);
    unset($cart[$pid]);
    json(['success' => true]);
} elseif ($action === 'clear') {
    $cart = [];
    json(['success' => true]);
}

json(['success' => true, 'items' => $cart]);

