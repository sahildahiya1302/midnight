<?php
// API endpoint to list saved layout versions for a given page

header('Content-Type: application/json');

$page = $_GET['page'] ?? null;

if (!$page || !preg_match('/^[A-Za-z0-9._-]+$/', $page)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid page']);
    exit;
}

$versionsDir = __DIR__ . "/../../themes/default/layout_versions/{$page}";

if (!is_dir($versionsDir)) {
    echo json_encode(['success' => true, 'versions' => []]);
    exit;
}

$files = glob($versionsDir . '/layout_*.json');

$versions = [];

foreach ($files as $file) {
    $basename = basename($file, '.json');
    // Extract version label from filename layout_{version}.json
    $version = substr($basename, 7);
    $versions[] = $version;
}

// Sort versions descending (newest first)
rsort($versions);

echo json_encode(['success' => true, 'versions' => $versions]);
exit;
?>
