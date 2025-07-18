<?php
// admin/api/save-layout.php
header('Content-Type: application/json');

// Simple error logging function
function log_error($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . PHP_EOL, 3, __DIR__ . '/../../logs/save-layout-errors.log');
}

// Read the input JSON
$input = json_decode(file_get_contents('php://input'), true);

if (
    !$input || 
    !isset($input['page']) || 
    !isset($input['layout']) ||
    !is_array($input['layout']) || 
    !isset($input['layout']['sections']) || 
    !isset($input['layout']['order'])
) {
    $errorMsg = 'Invalid layout format. Expecting { sections: {}, order: [] }';
    log_error($errorMsg);
    echo json_encode([
        'success' => false, 
        'error' => $errorMsg
    ]);
    http_response_code(400);
    exit;
}

$page = $input['page'];
$layout = $input['layout'];

// Validate page name
if (!preg_match('/^[A-Za-z0-9._-]+$/', $page)) {
    $errorMsg = 'Invalid page name: ' . $page;
    log_error($errorMsg);
    echo json_encode(['success' => false, 'error' => $errorMsg]);
    http_response_code(400);
    exit;
}

// Path to save layout file
$layoutFile = __DIR__ . "/../../themes/default/templates/{$page}.json";
$dir = dirname($layoutFile);

// Ensure directory exists and is writable
if (!is_dir($dir)) {
    if (!mkdir($dir, 0755, true)) {
        $errorMsg = 'Failed to create directory: ' . $dir;
        log_error($errorMsg);
        echo json_encode(['success' => false, 'error' => $errorMsg]);
        http_response_code(500);
        exit;
    }
}
if (!is_writable($dir)) {
    $errorMsg = 'Cannot write layout file: Directory not writable: ' . $dir;
    log_error($errorMsg);
    echo json_encode(['success' => false, 'error' => $errorMsg]);
    http_response_code(500);
    exit;
}

// Save layout
if (file_put_contents($layoutFile, json_encode($layout, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) === false) {
    $errorMsg = 'Failed to write layout file: ' . $layoutFile;
    log_error($errorMsg);
    echo json_encode(['success' => false, 'error' => $errorMsg]);
    http_response_code(500);
    exit;
}

echo json_encode(['success' => true]);
exit;
?>
