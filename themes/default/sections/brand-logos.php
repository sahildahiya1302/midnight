<?php
$id = $id ?? 'brand-logos-' . uniqid();
$heading = $settings['heading'] ?? 'Trusted by Top Brands';
$layoutStyle = $settings['layout_style'] ?? 'slider'; // options: slider / grid
$maxLogos = intval($settings['max_logos'] ?? 10);
$autoplay = $settings['autoplay'] ?? true;
$textAlign = $settings['text_align'] ?? 'center';

$logos = $settings['logos'] ?? []; // array of image URLs

if (!function_exists('escape_html')) {
  function escape_html($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
  }
}
?>

<?php if ($layoutStyle === 'slider'): ?>
<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
<?php endif; ?>

<style>
#<?= $id ?> {
  padding: 50px 20px;
  text-align: <?= escape_html($textAlign) ?>;
  font-family: Arial, sans-serif;
}

#<?= $id ?> h2 {
  font-size: 2rem;
  margin-bottom: 2rem;
}

#<?= $id ?> .logo-item {
  padding: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

#<?= $id ?> .logo-item img {
  max-height: 60px;
  max-width: 100%;
  object-fit: contain;
  filter: grayscale(100%);
  transition: filter 0.3s ease;
}

#<?= $id ?> .logo-item img:hover {
  filter: grayscale(0%);
}

#<?= $id ?> .brand-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 20px;
  justify-items: center;
}
</style>

<section id="<?= $id ?>">
  <h2><?= escape_html($heading) ?></h2>

  <?php if ($layoutStyle === 'grid'): ?>
    <div class="brand-grid">
      <?php foreach (array_slice($logos, 0, $maxLogos) as $logo): ?>
        <div class="logo-item">
          <img src="<?= escape_html($logo['image'] ?? '') ?>" alt="Brand Logo">
        </div>
      <?php endforeach; ?>
    </div>

  <?php else: ?>
    <div class="swiper <?= $id ?>-swiper">
      <div class="swiper-wrapper">
        <?php foreach (array_slice($logos, 0, $maxLogos) as $logo): ?>
          <div class="swiper-slide logo-item">
            <img src="<?= escape_html($logo['image'] ?? '') ?>" alt="Brand Logo">
          </div>
        <?php endforeach; ?>
      </div>
      <div class="swiper-pagination"></div>
    </div>
  <?php endif; ?>
</section>

<?php if ($layoutStyle === 'slider'): ?>
<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
<script>
new Swiper('.<?= $id ?>-swiper', {
  slidesPerView: 2,
  spaceBetween: 20,
  loop: true,
  autoplay: <?= $autoplay ? '{ delay: 3000 }' : 'false' ?>,
  pagination: {
    el: '.swiper-pagination',
    clickable: true
  },
  breakpoints: {
    640: { slidesPerView: 3 },
    768: { slidesPerView: 4 },
    1024: { slidesPerView: 5 }
  }
});
</script>
<?php endif; ?>
