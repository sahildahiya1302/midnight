document.addEventListener('DOMContentLoaded', () => {
  const navItems = document.querySelectorAll('.nav-item');
  const current = window.location.pathname;

  navItems.forEach(item => {
    const parentLink = item.querySelector(':scope > a');
    const subLinks = item.querySelectorAll('.sub-nav a');

    // expand if current page matches parent or a sub link
    if (parentLink && parentLink.getAttribute('href') === current) {
      item.classList.add('expanded');
      parentLink.classList.add('active');
    }
    subLinks.forEach(link => {
      if (link.getAttribute('href') === current) {
        item.classList.add('expanded');
        link.classList.add('active');
      }
    });

    item.addEventListener('click', e => {
      if (e.target.closest('.sub-nav')) {
        return; // don't toggle when clicking sub nav links
      }
      item.classList.toggle('expanded');
    });
  });
});
