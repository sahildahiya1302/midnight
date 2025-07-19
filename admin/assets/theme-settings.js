const inputs = document.querySelectorAll('.setting-input');

// menu navigation
const groups = document.querySelectorAll('.settings-group');
const links = document.querySelectorAll('.settings-menu .menu-link');
if (groups.length && links.length) {
  groups.forEach((g, i) => { if (i > 0) g.style.display = 'none'; });
  links[0].classList.add('active');
  links.forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault();
      const id = link.getAttribute('href').substring(1);
      groups.forEach(g => g.style.display = g.id === id ? 'block' : 'none');
      links.forEach(l => l.classList.remove('active'));
      link.classList.add('active');
    });
  });
}

// save all settings
const saveBtn = document.getElementById('save-settings');
if (saveBtn) {
  saveBtn.addEventListener('click', () => {
    const data = {};
    inputs.forEach(input => {
      data[input.dataset.key] = input.type === 'checkbox' ? (input.checked ? '1' : '0') : input.value;
    });
    fetch('/admin/api/theme-settings.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify(data)
    }).then(() => {
      saveBtn.textContent = 'Saved';
      setTimeout(() => { saveBtn.textContent = 'Save'; }, 1000);
    });
  });
}
