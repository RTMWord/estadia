<?php
// public/partials/navbar.php
// Partial para la barra (franja roja + header morado + nav + logo).
// Incluye al inicio de <body> con: <?php include __DIR__ . '/partials/navbar.php';
?>
<header class="upe-navbar-root" role="banner" aria-label="Barra principal">
  <div class="upe-top-strip" aria-hidden="true"></div>

  <div class="upe-header">
    <!-- Aquí no incluí el contador (ya lo tienes), si lo deseas puedes reinsertarlo -->
    <div class="upe-logo" aria-hidden="true">
      <img src="assets/img/upemor-logo.png" alt="Upemor">
    </div>

    <div class="upe-title" aria-hidden="true">
      Universidad Politécnica del Estado de Morelos
    </div>

    <nav class="upe-nav" role="navigation" aria-label="Menú principal">
      <ul class="upe-nav-list">
        <li class="upe-nav-item active"><a href="#">Inicio</a></li>

        <li class="upe-nav-item dropdown">
          <a href="#" class="drop-toggle">Oferta educativa <span class="caret">▾</span></a>
          <ul class="upe-dropdown">
            <li><a href="#">Carrera 1</a></li>
            <li><a href="#">Carrera 2</a></li>
          </ul>
        </li>

        <li class="upe-nav-item"><a href="#">Nosotros</a></li>

        <li class="upe-nav-item dropdown">
          <a href="#" class="drop-toggle">Transparencia <span class="caret">▾</span></a>
          <ul class="upe-dropdown">
            <li><a href="#">Info 1</a></li>
            <li><a href="#">Info 2</a></li>
          </ul>
        </li>

        <li class="upe-nav-item"><a href="#">Avisos de privacidad</a></li>
        <li class="upe-nav-item"><a href="#">Buzón de Sugerencias</a></li>
        <li class="upe-nav-item"><a href="#">Sitios de interés</a></li>
      </ul>
    </nav>
  </div>
</header>

<!-- Placeholder: ocupado sólo cuando la barra está fija para evitar salto del contenido -->
<div id="upe-navbar-placeholder" class="upe-navbar-placeholder" aria-hidden="true"></div>