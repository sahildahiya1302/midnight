<?php
session_start();

$data = json_decode(file_get_contents("php://input"), true);

$page = $data['page'] ?? '';
$layout = $data['layout'] ?? [];

if (!preg_match('/^[a-z0-9_-]+$/i', $page)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid page']);
    exit;
}

$_SESSION['live_preview'][$page] = $layout;

echo json_encode(['success' => true]);
