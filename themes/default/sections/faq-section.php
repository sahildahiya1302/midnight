<?php
$id = $id ?? 'faq-section-' . uniqid();
$heading = $settings['heading'] ?? 'Frequently Asked Questions';

$faqs = [];
if (!empty($blocks)) {
    foreach ($blocks as $block) {
        if (($block['type'] ?? '') === 'faq') {
            $faqs[] = $block['settings'] ?? [];
        }
    }
}
if (empty($faqs)) {
    $rawFaqs = $settings['faqs'] ?? [];
    $faqs = is_array($rawFaqs) ? $rawFaqs : json_decode($rawFaqs, true);
    if (!is_array($faqs)) $faqs = [];
}

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

#<?= $id ?> h2 {
  text-align: center;
  font-size: 2rem;
  margin-bottom: 2rem;
}

#<?= $id ?> .faq-list {
  max-width: 800px;
  margin: 0 auto;
}

#<?= $id ?> details {
  background: #fff;
  border-radius: 8px;
  margin-bottom: 15px;
  padding: 1rem 1.2rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

#<?= $id ?> summary {
  font-weight: bold;
  cursor: pointer;
  font-size: 1.05rem;
  outline: none;
}

#<?= $id ?> details[open] summary {
  color: #007BFF;
}

#<?= $id ?> .answer {
  margin-top: 10px;
  color: #444;
  line-height: 1.6;
}
</style>

<section id="<?= $id ?>">
  <h2><?= escape_html($heading) ?></h2>
  <div class="faq-list">
    <?php foreach ($faqs as $faq): ?>
      <?php
        $question = trim($faq['question'] ?? '');
        $answer = trim($faq['answer'] ?? '');
        if ($question === '' && $answer === '') continue;
      ?>
      <details>
        <summary><?= escape_html($question ?: 'No question provided') ?></summary>
        <?php if ($answer): ?>
          <div class="answer"><?= nl2br(escape_html($answer)) ?></div>
        <?php endif; ?>
      </details>
    <?php endforeach; ?>
  </div>
</section>
