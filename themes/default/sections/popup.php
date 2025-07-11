<?php
$id = $id ?? 'popup-' . uniqid();
$heading = $settings['heading'] ?? 'Get 10% Off!';
$subheading = $settings['subheading'] ?? 'Subscribe and receive a 10% coupon.';
$delay = intval($settings['delay_seconds'] ?? 3);
$background = $settings['background_color'] ?? '#fff';
$textColor = $settings['text_color'] ?? '#000';
?>

<style>
#<?= $id ?> {
  position: fixed;
  bottom: 20px;
  right: 20px;
  width: 320px;
  background: <?= escape_html($background) ?>;
  color: <?= escape_html($textColor) ?>;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.15);
  display: none;
  z-index: 9999;
  font-family: Arial, sans-serif;
}

#<?= $id ?> .close-btn {
  position: absolute;
  top: 8px;
  right: 12px;
  cursor: pointer;
  font-weight: bold;
}
</style>

<div id="<?= $id ?>">
  <div class="close-btn" onclick="document.getElementById('<?= $id ?>').style.display='none'">×</div>
  <h3><?= escape_html($heading) ?></h3>
  <p><?= escape_html($subheading) ?></p>
</div>

<script>
setTimeout(() => {
  document.getElementById('<?= $id ?>').style.display = 'block';
}, <?= $delay * 1000 ?>);
</script>
