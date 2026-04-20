setTimeout(() => {
  const flash = document.querySelector('.flash');
  if (flash) {
    flash.style.opacity = '0';
    flash.style.transition = 'opacity .4s ease';
    setTimeout(() => flash.remove(), 400);
  }
}, 3500);
