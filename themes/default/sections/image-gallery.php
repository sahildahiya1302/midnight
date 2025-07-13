<?php
$id = $id ?? 'image-gallery-' . uniqid();
$heading = $settings['heading'] ?? 'Gallery';
$images = $settings['images'] ?? [];
?>

<style>
#<?= $id ?> {
  padding: 50px 20px;
  text-align: center;
  font-family: Arial, sans-serif;
}

#<?= $id ?> .gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

#<?= $id ?> img {
  width: 100%;
  height: auto;
  border-radius: 8px;
}
</style>

<section id="<?= $id ?>">
  <h2><?= escape_html($heading) ?></h2>
  <div class="gallery-grid">
    <?php foreach ($images as $img): ?>
      <img src="<?= escape_html($img['image']) ?>" alt="Gallery Image">
    <?php endforeach; ?>
  </div>
</section>
