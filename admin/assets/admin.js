document.addEventListener('DOMContentLoaded', function () {
  const navItems = document.querySelectorAll('.nav-item');

  navItems.forEach(item => {
    item.addEventListener('click', function (e) {
      // Prevent link clicks from toggling sub-nav
      if (e.target.tagName.toLowerCase() === 'a') {
        return;
      }
      this.classList.toggle('expanded');
    });
  });
});
