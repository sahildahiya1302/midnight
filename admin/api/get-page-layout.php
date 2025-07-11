<?php
// API endpoint to get JSON layout for a given page

$page = $_GET['page'] ?? 'index';

// Basic validation - allow letters, numbers, dots and dashes
if (!preg_match('/^[A-Za-z0-9._-]+$/', $page)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid page']);
    exit;
}

$layoutFile = __DIR__ . "/../../themes/default/templates/{$page}.json";

if (!file_exists($layoutFile)) {
    http_response_code(404);
    echo json_encode(['error' => 'Layout not found']);
    exit;
}

header('Content-Type: application/json');
$content = file_get_contents($layoutFile);
$data = json_decode($content, true);

// Convert from old format with "sections" as object with keys to new format with array of sections
if (isset($data['sections']) && !is_array($data['sections'])) {
    $sections = [];
    foreach ($data['order'] ?? [] as $key) {
        if (isset($data['sections'][$key])) {
            $section = $data['sections'][$key];
            // Add the key as an id for reference if needed
            $section['id'] = $key;
            $sections[] = $section;
        }
    }
    $data['sections'] = $sections;
}

echo json_encode($data);
exit;
