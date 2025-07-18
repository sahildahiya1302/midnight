<?php
// API endpoint to save a versioned layout for a given page

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$page = $data['page'] ?? null;
$layout = $data['layout'] ?? null;
$version = $data['version'] ?? null; // optional version label or timestamp

// Basic validation - allow letters, numbers, dots and dashes
if (!$page || !preg_match('/^[A-Za-z0-9._-]+$/', $page) || !$layout) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$versionsDir = __DIR__ . "/../../themes/default/layout_versions/{$page}";

if (!is_dir($versionsDir)) {
    mkdir($versionsDir, 0755, true);
}

$timestamp = $version ?? date('YmdHis');
$versionFile = "{$versionsDir}/layout_{$timestamp}.json";

if (file_put_contents($versionFile, json_encode($layout, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save layout version']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Layout version saved', 'version' => $timestamp]);
exit;
?>
