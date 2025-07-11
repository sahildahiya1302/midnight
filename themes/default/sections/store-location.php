<?php
$id = $id ?? 'store-location-' . uniqid();

$storeName = $settings['store_name'] ?? 'Our Store';
$address = $settings['address'] ?? '123 Market Street, City, Country';
$phone = $settings['phone'] ?? '';
$email = $settings['email'] ?? '';
$hours = $settings['hours'] ?? 'Mon–Fri: 9am–7pm';
$mapEmbed = $settings['map_embed'] ?? ''; // iframe code from Google Maps

if (!function_exists('escape_html')) {
  function escape_html($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
  }
}
?>

<style>
#<?= $id ?> {
  padding: 60px 20px;
  font-family: Arial, sans-serif;
  background: #f9f9f9;
}

#<?= $id ?> .location-wrapper {
  display: flex;
  flex-wrap: wrap;
  gap: 40px;
  max-width: 1200px;
  margin: auto;
  align-items: flex-start;
}

#<?= $id ?> .map-box {
  flex: 1 1 400px;
  min-height: 300px;
  border-radius: 10px;
  overflow: hidden;
}

#<?= $id ?> .info-box {
  flex: 1 1 300px;
}

#<?= $id ?> .info-box h3 {
  font-size: 1.6rem;
  margin-bottom: 15px;
}

#<?= $id ?> .info-item {
  margin-bottom: 15px;
  font-size: 1rem;
  color: #333;
}

#<?= $id ?> .info-label {
  font-weight: bold;
  display: block;
  margin-bottom: 4px;
  color: #000;
}
</style>

<section id="<?= $id ?>">
  <div class="location-wrapper">
    <div class="map-box">
      <?= $mapEmbed ?>
    </div>
    <div class="info-box">
      <h3><?= escape_html($storeName) ?></h3>
      <div class="info-item">
        <span class="info-label">Address</span>
        <?= nl2br(escape_html($address)) ?>
      </div>
      <?php if ($phone): ?>
        <div class="info-item">
          <span class="info-label">Phone</span>
          <a href="tel:<?= escape_html($phone) ?>"><?= escape_html($phone) ?></a>
        </div>
      <?php endif; ?>
      <?php if ($email): ?>
        <div class="info-item">
          <span class="info-label">Email</span>
          <a href="mailto:<?= escape_html($email) ?>"><?= escape_html($email) ?></a>
        </div>
      <?php endif; ?>
      <div class="info-item">
        <span class="info-label">Hours</span>
        <?= escape_html($hours) ?>
      </div>
    </div>
  </div>
</section>
