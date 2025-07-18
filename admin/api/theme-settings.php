<?php
// API endpoint to get and save global theme settings

$method = $_SERVER['REQUEST_METHOD'];

$settingsFile = __DIR__ . "/../../themes/default/config/settings_data.json";

if ($method === 'GET') {
    if (!file_exists($settingsFile)) {
        http_response_code(404);
        echo json_encode(['error' => 'Settings not found']);
        exit;
    }
    header('Content-Type: application/json');
    echo file_get_contents($settingsFile);
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        exit;
    }
    if (!is_writable(dirname($settingsFile))) {
        http_response_code(500);
        echo json_encode(['error' => 'Cannot write settings file']);
        exit;
    }
    file_put_contents($settingsFile, json_encode($data, JSON_PRETTY_PRINT));
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
exit;
