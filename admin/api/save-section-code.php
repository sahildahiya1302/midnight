<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
$data = json_decode(file_get_contents('php://input'), true);
$section = $data['section'] ?? '';
$code = $data['code'] ?? '';
if (!preg_match('/^[a-zA-Z0-9_-]+$/', $section)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid section']);
    exit;
}
$path = __DIR__ . "/../../themes/default/sections/{$section}.php";
if (!is_file($path)) {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
    exit;
}
if (file_put_contents($path, $code) === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save']);
    exit;
}
echo json_encode(['success' => true]);
