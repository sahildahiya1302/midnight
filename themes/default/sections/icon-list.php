<?php
$id = $id ?? 'icon-list-' . uniqid();
$heading = $settings['heading'] ?? 'Why Shop With Us';
$icons = $settings['icons'] ?? [];
?>

<style>
#<?= $id ?> {
  padding: 50px 20px;
  font-family: Arial, sans-serif;
  text-align: center;
}

#<?= $id ?> .icon-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 30px;
  margin-top: 30px;
}

#<?= $id ?> .icon-item img {
  height: 50px;
  margin-bottom: 10px;
}

#<?= $id ?> .icon-item .label {
  font-size: 1rem;
  font-weight: 600;
}
</style>

<section id="<?= $id ?>">
  <h2><?= escape_html($heading) ?></h2>
  <div class="icon-grid">
    <?php foreach ($icons as $icon): ?>
      <div class="icon-item">
        <img src="<?= escape_html($icon['image']) ?>" alt="<?= escape_html($icon['label']) ?>">
        <div class="label"><?= escape_html($icon['label']) ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
