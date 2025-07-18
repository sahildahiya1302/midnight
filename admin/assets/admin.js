document.addEventListener('DOMContentLoaded', () => {
  const navItems = document.querySelectorAll('.nav-item');
  const current = window.location.pathname;

  navItems.forEach(item => {
    const link = item.querySelector(':scope > a');
    const subLinks = item.querySelectorAll('.sub-nav a');

    // expand if current page matches any link
    const links = [link, ...subLinks];
    links.forEach(a => {
      if (!a) return;
      if (current.indexOf(a.getAttribute('href')) !== -1) {
        item.classList.add('expanded', 'active');
      }
    });

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
