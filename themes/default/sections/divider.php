<?php
$id = $id ?? 'divider-' . uniqid();
$height = $settings['height'] ?? '30px';
$color = $settings['color'] ?? '#e0e0e0';
?>

<style>
#<?= $id ?> {
  height: <?= escape_html($height) ?>;
  background-color: <?= escape_html($color) ?>;
  width: 100%;
}
</style>

<div id="<?= $id ?>" aria-hidden="true"></div>
