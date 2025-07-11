<?php
// API endpoint to get schema.json for a given section type

$sectionType = $_GET['section'] ?? null;

if (!$sectionType) {
    http_response_code(400);
    echo json_encode(['error' => 'Section type required']);
    exit;
}

$schemaFile = __DIR__ . "/../../themes/default/sections/{$sectionType}.schema.json";

if (!file_exists($schemaFile)) {
    http_response_code(404);
    echo json_encode(['error' => 'Schema not found']);
    exit;
}

header('Content-Type: application/json');
echo file_get_contents($schemaFile);
exit;
