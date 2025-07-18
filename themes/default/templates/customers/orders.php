<?php
declare(strict_types=1);

// Load global settings
$settings = json_decode(file_get_contents(__DIR__ . '/../../config/settings_data.json'), true);

// Load schema for editor UI
$schema = [
    'name' => 'Customer Orders',
    'settings' => [
        ['type' => 'text', 'id' => 'page_title', 'label' => 'Page Title', 'default' => 'Customer Orders'],
        ['type' => 'textarea', 'id' => 'content', 'label' => 'Content', 'default' => '<h1>Customer Orders</h1>'],
    ],
];

// Extract settings
$pageTitle = $settings['page_title'] ?? 'Customer Orders';
$content = $settings['content'] ?? '<h1>Customer Orders</h1>';

// Render content within main theme layout
include __DIR__ . "/../../layouts/theme.php";
