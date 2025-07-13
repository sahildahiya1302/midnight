document.addEventListener('DOMContentLoaded', () => {
  const panel = document.getElementById('panel');
  document.querySelectorAll('.nav-item[data-url]').forEach(item => {
    item.addEventListener('click', () => {
      const url = item.dataset.url;
      if (item.dataset.external === '1') {
        window.open(url, '_blank');
        return;
      }
      if (panel) {
        panel.innerHTML = `<iframe src="${url}"></iframe>`;
        panel.classList.add('open');
      }
    });
  });

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && panel.classList.contains('open')) {
      panel.classList.remove('open');
      panel.innerHTML = '';
    }
  });
});
