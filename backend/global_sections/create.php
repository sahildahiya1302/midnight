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
$data = json_decode(file_get_contents('php://input'), true);
$handle = slugify(trim($data['handle'] ?? ''));
$title = trim($data['title'] ?? '');
$type = trim($data['type'] ?? '');
$sectionJson = $data['section_json'] ?? null;
if (!$handle || !$title || !$type || !$sectionJson) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
    exit;
}
$stmt = db()->prepare('INSERT INTO global_sections (handle, title, type, section_json) VALUES (?, ?, ?, ?)');
$stmt->execute([$handle, $title, $type, json_encode($sectionJson)]);
echo json_encode(['success' => true, 'id' => db()->lastInsertId()]);
