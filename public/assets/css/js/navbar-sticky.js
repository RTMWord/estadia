// public/assets/js/navbar-sticky.js
// Script que fija la barra arriba cuando se hace scroll.
// Añade/quita la clase 'upe-fixed' en el root (upe-navbar-root) y gestiona el placeholder.
//
// Incluir antes de cerrar </body> con:
// <script src="assets/js/navbar-sticky.js"></script>

(function () {
  'use strict';

  var root = document.querySelector('.upe-navbar-root');
  var placeholder = document.getElementById('upe-navbar-placeholder');
  if (!root || !placeholder) return;

  // función para actualizar el offset inicial y la altura del placeholder
  var state = {
    rootTop: root.getBoundingClientRect().top + window.pageYOffset,
    rootHeight: root.offsetHeight
  };

  function refreshState() {
    state.rootTop = root.getBoundingClientRect().top + window.pageYOffset;
    state.rootHeight = root.offsetHeight;
  }

  function setPlaceholderHeight(h) {
    placeholder.style.height = (h ? h + 'px' : '0');
  }

  // comprobar scroll y alternar clase
  function onScroll() {
    var y = window.pageYOffset || document.documentElement.scrollTop;
    if (y >= state.rootTop) {
      if (!root.classList.contains('upe-fixed')) {
        // establecer placeholder para no provocar salto
        setPlaceholderHeight(state.rootHeight);
        root.classList.add('upe-fixed');
      }
    } else {
      if (root.classList.contains('upe-fixed')) {
        root.classList.remove('upe-fixed');
        setPlaceholderHeight(0);
      }
    }
  }

  // recalcular al redimensionar (por si cambia la altura del header)
  function onResize() {
    // si está fijo, quítalo temporalmente para medir correctamente
    var fixed = root.classList.contains('upe-fixed');
    if (fixed) {
      root.classList.remove('upe-fixed');
      setPlaceholderHeight(0);
    }
    // recalcular medidas
    refreshState();
    // si estaba fijo vuelva a aplicar
    if (fixed) {
      setPlaceholderHeight(state.rootHeight);
      root.classList.add('upe-fixed');
    }
    // forzar comprobación scroll
    onScroll();
  }

  // init
  refreshState();
  // escucha eventos
  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onResize);
  // aplica en load por si la página se abre ya con scroll
  window.addEventListener('load', function () {
    refreshState();
    onScroll();
  });
})();