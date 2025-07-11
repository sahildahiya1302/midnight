<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');
$stmt = db_query('SELECT * FROM global_sections ORDER BY created_at DESC');
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($sections);
