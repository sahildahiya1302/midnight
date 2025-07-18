document.addEventListener('DOMContentLoaded', () => {
  const navItems = document.querySelectorAll('.nav-item');
  const current = window.location.pathname;
  const expandedKey = 'adminNavExpanded';

  navItems.forEach(item => {
    const link = item.querySelector(':scope > a');
    const subLinks = item.querySelectorAll('.sub-nav a');

    // expand if current page matches any link
    const links = [link, ...subLinks];
    let matched = false;
    links.forEach(a => {
      if (!a) return;
      if (current.indexOf(a.getAttribute('href')) !== -1) {
        matched = true;
        item.classList.add('expanded', 'active');
      }
    });

    if (!matched && link && sessionStorage.getItem(expandedKey) === link.getAttribute('href')) {
      item.classList.add('expanded');
    }

    if (link) {
      link.addEventListener('click', () => {
        item.classList.toggle('expanded');
        sessionStorage.setItem(expandedKey, link.getAttribute('href'));
      });
    }

    item.addEventListener('click', e => {
      if (link && e.target === link) return;
      item.classList.toggle('expanded');
    });
  });
});
