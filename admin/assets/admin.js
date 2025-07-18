document.addEventListener('DOMContentLoaded', () => {
  const navItems = document.querySelectorAll('.nav-item');

  navItems.forEach(item => {
    const link = item.querySelector(':scope > a');

    if (link) {
      link.addEventListener('click', () => {
        item.classList.toggle('expanded');
      });
    }

    item.addEventListener('click', e => {
      if (link && e.target === link) return;
      item.classList.toggle('expanded');
    });
  });
});
