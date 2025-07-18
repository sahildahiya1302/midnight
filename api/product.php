<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../functions.php';

header('Content-Type: application/json');

$ids = $_GET['ids'] ?? null;

if (!$ids) {
    echo json_encode(['success' => false, 'message' => 'Product IDs required']);
    exit;
}

$idArray = explode(',', $ids);
$idArray = array_map('intval', $idArray);

if (empty($idArray)) {
    echo json_encode(['success' => false, 'message' => 'Invalid product IDs']);
    exit;
}

$placeholders = implode(',', array_fill(0, count($idArray), '?'));
$sql = "SELECT p.*, pv.price, pv.image_url, pv.compare_at_price 
        FROM products p 
        LEFT JOIN product_variants pv ON pv.product_id = p.id 
        WHERE p.id IN ($placeholders) 
        GROUP BY p.id";

$stmt = db()->prepare($sql);
$stmt->execute($idArray);
$products = $stmt->fetchAll();

$formattedProducts = array_map(function($product) {
    return [
        'id' => $product['id'],
        'title' => $product['title'],
        'price' => $product['price'] ?? 0,
        'compare_price' => $product['compare_at_price'] ?? 0,
        'image' => $product['image_url'] ?? '/assets/images/placeholder.png',
        'handle' => $product['handle']
    ];
}, $products);

echo json_encode(['success' => true, 'data' => $formattedProducts]);
?>
