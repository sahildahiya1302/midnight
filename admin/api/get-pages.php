<?php
// API endpoint to list available pages (template JSON files) for theme editor page selector

$templatesDir = __DIR__ . '/../../themes/default/templates';

$files = glob($templatesDir . '/*.json');

$pages = [];

foreach ($files as $file) {
    $name = basename($file, '.json');
    $pages[] = $name;
}

header('Content-Type: application/json');
echo json_encode($pages);
exit;
