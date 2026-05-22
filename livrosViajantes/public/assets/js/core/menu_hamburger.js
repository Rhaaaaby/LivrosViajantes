// Menu hamburger functionality with event delegation for dynamically loaded content
document.addEventListener('click', function(event) {
  const menuHamburger = event.target.closest('.menu-hamburger');
  const linksContainer = document.querySelector('.links-container');
  const cabecalho = document.querySelector('.cabecalho');

  if (menuHamburger) {
    menuHamburger.classList.toggle('ativo');
    if (linksContainer) {
      linksContainer.classList.toggle('ativo');
    }
    return;
  }

  // Close menu when clicking outside
  if (cabecalho && !cabecalho.contains(event.target)) {
    const btn = cabecalho.querySelector('.menu-hamburger');
    if (btn) btn.classList.remove('ativo');
    if (linksContainer) linksContainer.classList.remove('ativo');
  }

  // Close menu when clicking a link
  if (event.target.closest('.links-btn')) {
    const btn = document.querySelector('.menu-hamburger');
    if (btn) btn.classList.remove('ativo');
    if (linksContainer) linksContainer.classList.remove('ativo');
  }
});

// Handle resize - close menu on desktop
window.addEventListener('resize', function() {
  if (window.innerWidth > 768) {
    const btn = document.querySelector('.menu-hamburger');
    const linksContainer = document.querySelector('.links-container');
    if (btn) btn.classList.remove('ativo');
    if (linksContainer) linksContainer.classList.remove('ativo');
  }
});
