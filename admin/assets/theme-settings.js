document.querySelectorAll('.setting-input').forEach(input => {
  const save = () => {
    let value = input.type === 'checkbox' ? (input.checked ? '1' : '0') : input.value;
    fetch('/admin/api/theme/save-setting.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ theme_id: CURRENT_THEME_ID, key: input.dataset.key, value })
    });
  };
  input.addEventListener('change', save);
  if (input.type === 'range') {
    input.addEventListener('input', save);
  }
});
