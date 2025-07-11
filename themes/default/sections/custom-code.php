<?php
$id = $id ?? 'custom-code-' . uniqid();
$html = $settings['custom_html'] ?? '';
$css = $settings['custom_css'] ?? '';
$js = $settings['custom_js'] ?? '';
?>

<section id="<?= $id ?>">
  <?= $html ?>
</section>

<?php if (!empty($css)): ?>
  <style><?= $css ?></style>
<?php endif; ?>

<?php if (!empty($js)): ?>
  <script><?= $js ?></script>
<?php endif; ?>
