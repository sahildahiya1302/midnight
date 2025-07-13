<?php
$id = $id ?? 'collection-list-' . uniqid();
$heading = $settings['heading'] ?? 'Shop by Collection';
$subheading = $settings['subheading'] ?? '';
$maxCollections = intval($settings['max_collections'] ?? 6);
$textAlign = $settings['text_align'] ?? 'center';
$showDescription = $settings['show_description'] ?? false;

// Example data. Replace this with a DB/API call like getFeaturedCollections()
$collections = [
  [
    'title' => 'Rings',
    'handle' => 'rings',
    'image' => '/assets/collections/rings.jpg',
    'description' => 'Timeless silver rings for every occasion.'
  ],
  [
    'title' => 'Necklaces',
    'handle' => 'necklaces',
    'image' => '/assets/collections/necklaces.jpg',
    'description' => 'Elegant silver necklaces crafted for elegance.'
  ],
  [
    'title' => 'Bracelets',
    'handle' => 'bracelets',
    'image' => '/assets/collections/bracelets.jpg',
    'description' => 'Bold and minimal bracelet pieces.'
  ]
];

if (!function_exists('escape_html')) {
  function escape_html($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
  }
}
?>

<style>
#<?= $id ?> {
  padding: 60px 20px;
  text-align: <?= escape_html($textAlign) ?>;
  font-family: Arial, sans-serif;
}

#<?= $id ?> h2 {
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

#<?= $id ?> .subheading {
  color: #666;
  margin-bottom: 2rem;
}

#<?= $id ?> .collection-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 30px;
}

#<?= $id ?> .collection-card {
  background: #fff;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 3px 8px rgba(0,0,0,0.08);
  transition: transform 0.2s ease;
  text-align: center;
  text-decoration: none;
  color: inherit;
}

#<?= $id ?> .collection-card:hover {
  transform: translateY(-5px);
}

#<?= $id ?> .collection-card img {
  width: 100%;
  height: 180px;
  object-fit: cover;
}

#<?= $id ?> .collection-card .content {
  padding: 1rem;
}

#<?= $id ?> .collection-card h3 {
  font-size: 1.2rem;
  margin-bottom: 0.5rem;
}

#<?= $id ?> .collection-card .description {
  font-size: 0.95rem;
  color: #555;
}
</style>

<section id="<?= $id ?>" aria-label="Collection List">
  <h2><?= escape_html($heading) ?></h2>
  <?php if ($subheading): ?>
    <p class="subheading"><?= escape_html($subheading) ?></p>
  <?php endif; ?>

  <div class="collection-grid">
    <?php foreach (array_slice($collections, 0, $maxCollections) as $collection): ?>
      <a href="/collections/<?= escape_html($collection['handle']) ?>" class="collection-card">
        <img src="<?= escape_html($collection['image']) ?>" alt="<?= escape_html($collection['title']) ?>">
        <div class="content">
          <h3><?= escape_html($collection['title']) ?></h3>
          <?php if ($showDescription && !empty($collection['description'])): ?>
            <div class="description"><?= escape_html($collection['description']) ?></div>
          <?php endif; ?>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>
