<?php
$id = $id ?? 'footer-' . uniqid();
$logo = $settings['logo'] ?? '';
$copyright = $settings['copyright'] ?? '';
$menus = [];
if (!empty($blocks)) {
    foreach ($blocks as $block) {
        if (($block['type'] ?? '') === 'menu') {
            $menus[] = $block['settings'] ?? [];
        }
    }
}
if (empty($menus)) {
    $menus = $settings['menus'] ?? [];
}

if (!function_exists('escape_html')) {
  function escape_html($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
  }
}
?>

<style>
#<?= $id ?> {
  background: #111;
  color: #eee;
  padding: 40px 20px;
  font-family: Arial, sans-serif;
}

#<?= $id ?> .footer-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 40px;
  justify-content: space-between;
  margin-bottom: 20px;
}

#<?= $id ?> .footer-col {
  flex: 1;
  min-width: 180px;
}

#<?= $id ?> a {
  color: #ccc;
  text-decoration: none;
  display: block;
  margin-bottom: 8px;
}

#<?= $id ?> a:hover {
  text-decoration: underline;
}

#<?= $id ?> .footer-bottom {
  text-align: center;
  font-size: 0.85rem;
  color: #888;
}
</style>

<footer id="<?= $id ?>">
  <div class="footer-grid">
    <?php if ($logo): ?>
      <div class="footer-col">
        <img src="<?= escape_html($logo) ?>" alt="Logo" style="max-height: 60px;">
      </div>
    <?php endif; ?>

    <?php foreach ($menus as $menu): ?>
      <div class="footer-col">
        <h4><?= escape_html($menu['title']) ?></h4>
        <?php foreach ($menu['links'] as $link): ?>
          <a href="<?= escape_html($link['url']) ?>"><?= escape_html($link['label']) ?></a>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="footer-bottom">
    <?= escape_html($copyright) ?>
  </div>
</footer>
