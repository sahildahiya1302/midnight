<?php
$id = $id ?? 'newsletter-' . uniqid();
$heading = $settings['heading'] ?? 'Subscribe to our Newsletter';
$subheading = $settings['subheading'] ?? 'Get special offers, updates, and more.';
$textAlign = $settings['text_align'] ?? 'center';

if (!function_exists('escape_html')) {
  function escape_html($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
  }
}
?>

<style>
#<?= $id ?> {
  padding: 50px 20px;
  background: #f8f8f8;
  font-family: Arial, sans-serif;
  text-align: <?= escape_html($textAlign) ?>;
}

#<?= $id ?> form {
  max-width: 500px;
  margin: 20px auto 0;
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

#<?= $id ?> input[type="email"] {
  flex: 1;
  padding: 12px;
  border-radius: 4px;
  border: 1px solid #ccc;
}

#<?= $id ?> button {
  padding: 12px 20px;
  background: #000;
  color: white;
  border: none;
  border-radius: 4px;
  font-weight: bold;
}
</style>

<section id="<?= $id ?>">
  <h2><?= escape_html($heading) ?></h2>
  <p><?= escape_html($subheading) ?></p>
  <form method="POST" action="/subscribe-newsletter.php">
    <input type="email" name="email" placeholder="Enter your email" required />
    <button type="submit">Subscribe</button>
  </form>
</section>
