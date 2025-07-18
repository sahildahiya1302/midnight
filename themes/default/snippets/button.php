<?php
// Button snippet template

// Load global settings
$settings = json_decode(file_get_contents(__DIR__ . '/../config/settings_data.json'), true);

// Load schema for editor UI
$schema = [
    'name' => 'Button',
    'settings' => [
        ['type' => 'text', 'id' => 'label', 'label' => 'Button Label', 'default' => 'Click Me'],
        ['type' => 'url', 'id' => 'url', 'label' => 'Button URL', 'default' => '#'],
        ['type' => 'select', 'id' => 'style', 'label' => 'Button Style', 'options' => [
            ['value' => 'primary', 'label' => 'Primary'],
            ['value' => 'secondary', 'label' => 'Secondary'],
            ['value' => 'tertiary', 'label' => 'Tertiary'],
        ], 'default' => 'primary'],
    ],
];

// Extract settings
$label = $settings['label'] ?? 'Click Me';
$url = $settings['url'] ?? '#';
$style = $settings['style'] ?? 'primary';

?>
<a href="<?php echo htmlspecialchars($url); ?>" class="btn btn-<?php echo htmlspecialchars($style); ?>">
    <?php echo htmlspecialchars($label); ?>
</a>
