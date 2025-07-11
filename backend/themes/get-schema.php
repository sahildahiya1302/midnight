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

$themeId = intval($_GET['theme_id'] ?? 0);

if (!$themeId) {
    http_response_code(400);
    echo json_encode(['error' => 'Theme ID is required']);
    exit;
}

$schema = db_query('SELECT schema_json FROM themes WHERE id = :id', [':id' => $themeId])->fetchColumn();

if (!$schema) {
    http_response_code(404);
    echo json_encode(['error' => 'Theme schema not found']);
    exit;
}

echo $schema;
