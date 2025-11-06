// public/assets/js/bs-navbar-sticky.js
// Hace "sticky on scroll" para el navbar Bootstrap que definimos en partial.
// Añade la clase 'fixed-top' al elemento .meta-navbar-root cuando se hace scroll
// más allá de su posición original y gestiona el placeholder para que no salte el contenido.

(function () {
  'use strict';

  var root = document.querySelector('.meta-navbar-root');
  var placeholder = document.getElementById('meta-navbar-placeholder');
  if (!root || !placeholder) return;

  var state = {
    initialTop: 0,
    height: 0
  };

  function calcState() {
    // Si el navbar ya tiene fixed-top (por alguna razón), quítalo temporalmente para medir
    var wasFixed = root.classList.contains('fixed-top');
    if (wasFixed) {
      root.classList.remove('fixed-top');
      placeholder.style.height = '0px';
    }
    // Recalcula
    state.initialTop = root.getBoundingClientRect().top + window.pageYOffset;
    state.height = root.offsetHeight;
    // restaura estado
    if (wasFixed) {
      root.classList.add('fixed-top');
      placeholder.style.height = state.height + 'px';
    }
  }

  function onScroll() {
    var y = window.pageYOffset || document.documentElement.scrollTop;
    if (y >= state.initialTop) {
      if (!root.classList.contains('fixed-top')) {
        placeholder.style.height = state.height + 'px';
        root.classList.add('fixed-top');
      }
    } else {
      if (root.classList.contains('fixed-top')) {
        root.classList.remove('fixed-top');
        placeholder.style.height = '0px';
      }
    }
  }

  // recalcular al resize y on load
  window.addEventListener('load', function () {
    calcState();
    onScroll();
  });
  window.addEventListener('resize', function () {
    // quita fixed, recalcula, luego aplica si corresponde
    var wasFixed = root.classList.contains('fixed-top');
    if (wasFixed) {
      root.classList.remove('fixed-top');
      placeholder.style.height = '0px';
    }
    calcState();
    if (wasFixed) {
      // si estaba fijo mantenlo
      placeholder.style.height = state.height + 'px';
      root.classList.add('fixed-top');
    }
    onScroll();
  }, { passive: true });

  window.addEventListener('scroll', onScroll, { passive: true });
})();