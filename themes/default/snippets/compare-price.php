<?php
// Compare Price snippet template

// Load global settings
$settings = json_decode(file_get_contents(__DIR__ . '/../config/settings_data.json'), true);

// Load schema for editor UI
$schema = [
    'name' => 'Compare Price',
    'settings' => [
        ['type' => 'text', 'id' => 'original_price', 'label' => 'Original Price', 'default' => '100'],
        ['type' => 'text', 'id' => 'sale_price', 'label' => 'Sale Price', 'default' => '80'],
        ['type' => 'text', 'id' => 'currency', 'label' => 'Currency', 'default' => '$'],
    ],
];

// Extract settings
$originalPrice = $settings['original_price'] ?? '100';
$salePrice = $settings['sale_price'] ?? '80';
$currency = $settings['currency'] ?? '$';

?>
<div class="compare-price">
    <span class="original-price" style="text-decoration: line-through;"><?php echo htmlspecialchars($currency) . htmlspecialchars($originalPrice); ?></span>
    <span class="sale-price"><?php echo htmlspecialchars($currency) . htmlspecialchars($salePrice); ?></span>
</div>
