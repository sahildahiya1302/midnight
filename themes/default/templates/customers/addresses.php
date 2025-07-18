<?php
declare(strict_types=1);

// Load global settings
$settings = json_decode(file_get_contents(__DIR__ . '/../../config/settings_data.json'), true);

// Load schema for editor UI
$schema = [
    'name' => 'Customer Addresses',
    'settings' => [
        ['type' => 'text', 'id' => 'page_title', 'label' => 'Page Title', 'default' => 'Customer Addresses'],
        ['type' => 'textarea', 'id' => 'content', 'label' => 'Content', 'default' => '<h1>Customer Addresses</h1>'],
    ],
];

// Extract settings
$pageTitle = $settings['page_title'] ?? 'Customer Addresses';
$content = $settings['content'] ?? '<h1>Customer Addresses</h1>';

// Render content within main theme layout
include __DIR__ . "/../../layouts/theme.php";
