<?php
$variants = $context['product_variants'] ?? [];
if (count($variants) <= 1) return;
$id = $id ?? 'variant-selector-' . uniqid();
?>
<div id="<?= $id ?>" class="variant-selector">
  <label for="<?= $id ?>-select">Select option:</label>
  <select id="<?= $id ?>-select">
    <?php foreach ($variants as $v): ?>
      <option value="<?= (int)$v['id'] ?>" data-price="<?= $v['price'] ?>" data-image="<?= htmlspecialchars($v['image_url']) ?>">
        <?= htmlspecialchars($v['option_label'] ?: $v['sku']) ?>
      </option>
    <?php endforeach; ?>
  </select>
</div>
<script>
(function(){
  const sel = document.getElementById('<?= $id ?>-select');
  if(!sel) return;
  sel.addEventListener('change', function(){
    const opt = sel.options[sel.selectedIndex];
    const price = parseFloat(opt.getAttribute('data-price') || '0');
    const img = opt.getAttribute('data-image');
    document.querySelectorAll('.product-price').forEach(el => { el.textContent = '₹' + price.toFixed(2); });
    if(img){
      const imgEl = document.querySelector('#<?= $id ?>').closest('section,div').querySelector('img');
      if(imgEl) imgEl.src = img;
    }
  });
})();
</script>
