<?php
// Icon snippet template

// Load global settings
$settings = json_decode(file_get_contents(__DIR__ . '/../config/settings_data.json'), true);

// Load schema for editor UI
$schema = [
    'name' => 'Icon',
    'settings' => [
        ['type' => 'text', 'id' => 'icon_name', 'label' => 'Icon Name', 'default' => 'star'],
        ['type' => 'color', 'id' => 'icon_color', 'label' => 'Icon Color', 'default' => '#000000'],
        ['type' => 'text', 'id' => 'icon_size', 'label' => 'Icon Size (px)', 'default' => '24'],
    ],
];

// Extract settings
$iconName = $settings['icon_name'] ?? 'star';
$iconColor = $settings['icon_color'] ?? '#000000';
$iconSize = $settings['icon_size'] ?? '24';

?>
<span class="icon" style="color: <?php echo htmlspecialchars($iconColor); ?>; font-size: <?php echo htmlspecialchars($iconSize); ?>px;">
    <i class="icon-<?php echo htmlspecialchars($iconName); ?>"></i>
</span>
