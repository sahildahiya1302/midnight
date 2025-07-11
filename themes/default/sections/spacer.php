<?php
$id = $id ?? 'spacer-' . uniqid();
$height = intval($settings['height'] ?? 40); // in pixels

if (!function_exists('escape_html')) {
  function escape_html($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
  }
}
?>

<style>
#<?= $id ?> {
  height: <?= escape_html($height) ?>px;
}
</style>

<div id="<?= $id ?>"></div>
