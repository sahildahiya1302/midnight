<?php
$id = $id ?? 'blog-posts-' . uniqid();
$heading = $settings['heading'] ?? 'Latest Articles';
$subheading = $settings['subheading'] ?? '';
$maxPosts = intval($settings['max_posts'] ?? 3);
$showExcerpt = $settings['show_excerpt'] ?? true;
$showDate = $settings['show_date'] ?? true;
$textAlign = $settings['text_align'] ?? 'center';

if (!function_exists('escape_html')) {
  function escape_html($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
  }
}

// Fake blog posts for demo. Replace with DB or API fetch logic.
$blogPosts = [
  [
    'title' => 'How to Style Jewelry in 2025',
    'excerpt' => 'Trendy tips for combining sterling silver with modern outfits.',
    'image' => '/assets/blog1.jpg',
    'link' => '/blog/how-to-style',
    'date' => '2025-06-10'
  ],
  [
    'title' => 'Behind the Scenes: Luxez Photoshoot',
    'excerpt' => 'An exclusive look into our brand shoot preparation.',
    'image' => '/assets/blog2.jpg',
    'link' => '/blog/photoshoot',
    'date' => '2025-06-01'
  ],
  [
    'title' => 'Sterling Silver Care Guide',
    'excerpt' => 'Keep your jewelry shiny with these simple tips.',
    'image' => '/assets/blog3.jpg',
    'link' => '/blog/care-guide',
    'date' => '2025-05-25'
  ]
];
?>

<style>
#<?= $id ?> {
  padding: 60px 20px;
  text-align: <?= escape_html($textAlign) ?>;
  font-family: Arial, sans-serif;
}

#<?= $id ?> h2 {
  font-size: 2.2rem;
  margin-bottom: 0.5rem;
}

#<?= $id ?> p.subheading {
  font-size: 1rem;
  color: #666;
  margin-bottom: 2rem;
}

#<?= $id ?> .blog-grid {
  display: grid;
  gap: 30px;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
}

#<?= $id ?> .blog-card {
  background: #fff;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
  text-align: left;
  transition: transform 0.2s ease;
}

#<?= $id ?> .blog-card:hover {
  transform: translateY(-5px);
}

#<?= $id ?> .blog-card img {
  width: 100%;
  height: 180px;
  object-fit: cover;
}

#<?= $id ?> .blog-card .content {
  padding: 1rem;
}

#<?= $id ?> .blog-card h3 {
  font-size: 1.2rem;
  margin-bottom: 0.5rem;
}

#<?= $id ?> .blog-card .date {
  font-size: 0.85rem;
  color: #999;
  margin-bottom: 0.5rem;
}

#<?= $id ?> .blog-card .excerpt {
  font-size: 0.95rem;
  color: #555;
}
</style>

<section id="<?= $id ?>">
  <h2><?= escape_html($heading) ?></h2>
  <?php if (!empty($subheading)): ?>
    <p class="subheading"><?= escape_html($subheading) ?></p>
  <?php endif; ?>
  
  <div class="blog-grid">
    <?php foreach (array_slice($blogPosts, 0, $maxPosts) as $post): ?>
      <a class="blog-card" href="<?= escape_html($post['link']) ?>">
        <img src="<?= escape_html($post['image']) ?>" alt="<?= escape_html($post['title']) ?>">
        <div class="content">
          <h3><?= escape_html($post['title']) ?></h3>
          <?php if ($showDate): ?>
            <div class="date"><?= date('F j, Y', strtotime($post['date'])) ?></div>
          <?php endif; ?>
          <?php if ($showExcerpt): ?>
            <div class="excerpt"><?= escape_html($post['excerpt']) ?></div>
          <?php endif; ?>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>
