<?php
// Price snippet template

// Load global settings
$settings = json_decode(file_get_contents(__DIR__ . '/../config/settings_data.json'), true);

// Load schema for editor UI
$schema = [
    'name' => 'Price',
    'settings' => [
        ['type' => 'number', 'id' => 'price', 'label' => 'Price', 'default' => 0],
        ['type' => 'text', 'id' => 'currency_symbol', 'label' => 'Currency Symbol', 'default' => '$'],
        ['type' => 'select', 'id' => 'format', 'label' => 'Format', 'options' => [
            ['value' => 'prefix', 'label' => 'Prefix (e.g. $100)'],
            ['value' => 'suffix', 'label' => 'Suffix (e.g. 100$)'],
        ], 'default' => 'prefix'],
    ],
];

// Extract settings
$price = $settings['price'] ?? 0;
$currencySymbol = $settings['currency_symbol'] ?? '$';
$format = $settings['format'] ?? 'prefix';

if (!isset($price)) return;

$formattedPrice = number_format((float)$price, 2);

?>
<div class="price">
    <?php if ($format === 'prefix'): ?>
        <?php echo htmlspecialchars($currencySymbol) . $formattedPrice; ?>
    <?php else: ?>
        <?php echo $formattedPrice . htmlspecialchars($currencySymbol); ?>
    <?php endif; ?>
</div>
