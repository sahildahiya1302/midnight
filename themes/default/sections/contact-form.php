<?php
$id = $id ?? 'contact-form-' . uniqid();
$heading = $settings['heading'] ?? 'Get in Touch';
$subheading = $settings['subheading'] ?? 'We usually respond within 24 hours.';
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
  text-align: <?= escape_html($textAlign) ?>;
  font-family: Arial, sans-serif;
}

#<?= $id ?> form {
  max-width: 600px;
  margin: 0 auto;
  text-align: left;
}

#<?= $id ?> input, #<?= $id ?> textarea {
  width: 100%;
  padding: 10px;
  margin-bottom: 1rem;
  border: 1px solid #ccc;
  border-radius: 4px;
}

#<?= $id ?> button {
  background-color: #000;
  color: white;
  padding: 12px 24px;
  border: none;
  border-radius: 30px;
  cursor: pointer;
  font-weight: bold;
}

#<?= $id ?> button:hover {
  background-color: #333;
}
</style>

<section id="<?= $id ?>">
  <h2><?= escape_html($heading) ?></h2>
  <p><?= escape_html($subheading) ?></p>
  <form method="post" action="/submit-contact.php">
    <input type="text" name="name" placeholder="Your Name" required />
    <input type="email" name="email" placeholder="Email Address" required />
    <textarea name="message" placeholder="Your Message" rows="5" required></textarea>
    <button type="submit">Send Message</button>
  </form>
</section>
