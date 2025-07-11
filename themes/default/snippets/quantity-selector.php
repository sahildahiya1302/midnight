<?php
// Quantity Selector snippet template

// Load global settings
$settings = json_decode(file_get_contents(__DIR__ . '/../config/settings_data.json'), true);

// Load schema for editor UI
$schema = [
    'name' => 'Quantity Selector',
    'settings' => [
        ['type' => 'text', 'id' => 'name', 'label' => 'Input Name', 'default' => 'quantity'],
        ['type' => 'number', 'id' => 'value', 'label' => 'Initial Value', 'default' => 1],
        ['type' => 'number', 'id' => 'min', 'label' => 'Minimum Value', 'default' => 1],
        ['type' => 'number', 'id' => 'max', 'label' => 'Maximum Value', 'default' => 100],
    ],
];

// Extract settings
$name = $settings['name'] ?? 'quantity';
$value = $settings['value'] ?? 1;
$min = $settings['min'] ?? 1;
$max = $settings['max'] ?? 100;

?>
<div class="quantity-selector">
    <button type="button" class="qty-decrease" aria-label="Decrease quantity">-</button>
    <input type="number" name="<?php echo htmlspecialchars($name); ?>" value="<?php echo (int)$value; ?>" min="<?php echo (int)$min; ?>" max="<?php echo (int)$max; ?>">
    <button type="button" class="qty-increase" aria-label="Increase quantity">+</button>
</div>
<script>
(function(){
    const wrapper = document.currentScript.previousElementSibling;
    if(!wrapper) return;
    const input = wrapper.querySelector('input');
    wrapper.querySelector('.qty-decrease').addEventListener('click',()=>{
        input.stepDown();
    });
    wrapper.querySelector('.qty-increase').addEventListener('click',()=>{
        input.stepUp();
    });
})();
</script>
