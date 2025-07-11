<?php
$id = $id ?? 'announcement-bar-' . uniqid();

// Load tiles from blocks if provided
$tiles = [];
if (!empty($blocks)) {
    foreach ($blocks as $block) {
        if (($block['type'] ?? '') === 'tile') {
            $tiles[] = $block['settings'] ?? [];
        }
    }
}

// Fallback to legacy settings structure
if (empty($tiles)) {
    $tiles = $settings['tiles'] ?? [
        [
            'text' => 'Welcome to our store!',
            'background_color' => '#000000',
            'text_color' => '#ffffff',
            'padding' => '0 1rem',
            'font_size' => '1rem',
            'font_weight' => '600'
        ]
    ];
}

// ✅ Fix: Decode if JSON string
if (is_string($tiles)) {
    $tiles = json_decode($tiles, true) ?? [];
}

// If decoding fails, fallback
if (!is_array($tiles)) {
    $tiles = [
        [
            'text' => 'Welcome to our store!',
            'background_color' => '#000000',
            'text_color' => '#ffffff',
            'padding' => '0 1rem',
            'font_size' => '1rem',
            'font_weight' => '600'
        ]
    ];
}

$animationType = $settings['animation_type'] ?? 'horizontal';
$animationSpeed = intval($settings['animation_speed'] ?? 10);
$animationDirection = $settings['animation_direction'] ?? 'left';
$backgroundColor = $settings['background_color'] ?? '#000000';
$textColor = $settings['text_color'] ?? '#ffffff';
$fontSize = $settings['font_size'] ?? '1rem';
$fontWeight = $settings['font_weight'] ?? '600';
$padding = $settings['padding'] ?? '10px 1rem';

$animationDuration = $animationSpeed . 's';
$animationClass = 'announcement-animation-' . uniqid();

if (!function_exists('escape_html')) {
    function escape_html($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}
?>

<style>
#<?= $animationClass ?> {
  background-color: <?= escape_html($backgroundColor) ?>;
  color: <?= escape_html($textColor) ?>;
  overflow: hidden;
  white-space: nowrap;
  padding: <?= escape_html($padding) ?>;
  position: relative;
  font-weight: <?= escape_html($fontWeight) ?>;
  font-size: <?= escape_html($fontSize) ?>;
  font-family: var(--font-family, Arial, Helvetica, sans-serif);
}

#<?= $animationClass ?> .announcement-list {
  display: inline-block;
  white-space: nowrap;
  animation-timing-function: linear;
  animation-iteration-count: infinite;
}

<?php if ($animationType === 'horizontal' || $animationType === 'marquee'): ?>
#<?= $animationClass ?> .announcement-list {
  animation-name: scroll-horizontal;
  animation-duration: <?= $animationDuration ?>;
  animation-direction: <?= ($animationDirection === 'right') ? 'reverse' : 'normal' ?>;
}

@keyframes scroll-horizontal {
  0% {
    transform: translateX(100%);
  }
  100% {
    transform: translateX(-100%);
  }
}
<?php endif; ?>

<?php if ($animationType === 'vertical'): ?>
#<?= $animationClass ?> {
  white-space: normal;
  height: 2em;
}

#<?= $animationClass ?> .announcement-list {
  display: block;
  animation-name: scroll-vertical;
  animation-duration: <?= $animationDuration ?>;
  animation-direction: <?= ($animationDirection === 'down') ? 'reverse' : 'normal' ?>;
  animation-timing-function: ease-in-out;
  animation-iteration-count: infinite;
  position: relative;
  top: 0;
}

#<?= $animationClass ?> .announcement-item {
  height: 2em;
  line-height: 2em;
}

@keyframes scroll-vertical {
  0% {
    top: 0;
  }
  100% {
    top: -<?= count($tiles) * 2 ?>em;
  }
}
<?php endif; ?>
</style>

<section id="<?= $animationClass ?>" role="region" aria-label="Announcement Bar">
  <div class="announcement-list">
    <?php foreach ($tiles as $tile): ?>
      <?php
        $tileText = $tile['text'] ?? 'Announcement';
        $tileColor = $tile['text_color'] ?? $textColor;
        $tileBg = $tile['background_color'] ?? $backgroundColor;
        $tilePadding = $tile['padding'] ?? '0 1rem';
        $tileFontSize = $tile['font_size'] ?? $fontSize;
        $tileFontWeight = $tile['font_weight'] ?? $fontWeight;
      ?>
      <span class="announcement-item" style="
        background-color: <?= escape_html($tileBg) ?>;
        color: <?= escape_html($tileColor) ?>;
        padding: <?= escape_html($tilePadding) ?>;
        font-size: <?= escape_html($tileFontSize) ?>;
        font-weight: <?= escape_html($tileFontWeight) ?>;
        margin-right: 2rem;
        display: inline-block;
      ">
        <?= escape_html($tileText) ?>
      </span>
    <?php endforeach; ?>
  </div>
</section>
