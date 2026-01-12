<?php
// public/partials/bs-navbar.php
// Partial que renderiza el navbar Bootstrap que enviaste.
// Instrucciones: inclúyelo justo después de <body> en tu index.php con:
// <?php include __DIR__ . '/partials/bs-navbar.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-gradient-primary meta-navbar-root fixed-top" role="navigation" aria-label="Main navbar" style="padding-top: 1rem; padding-bottom: 1.05rem;">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <span class="ms-2">MetaHogar</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Inicio</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="quienesSomosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        ¿Quiénes Somos?
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="quienesSomosDropdown">
                        <li><a class="dropdown-item" href="quienes_somos.php">Quiénes Somos</a></li>
                        <li><a class="dropdown-item" href="vision.php">Visión</a></li>
                        <li><a class="dropdown-item" href="mision.php">Misión</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="productos.php">Productos</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="blogDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Contenido Informativo
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="blogDropdown">
                        <li><a class="dropdown-item" href="blog.php">Blog</a></li>
                        <li><a class="dropdown-item" href="noticias.php">Noticias</a></li>
                        <li><a class="dropdown-item" href="articulos.php">Artículos</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="sitiosInteresDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Sitios de Interés
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="sitiosInteresDropdown">
                        <li><a class="dropdown-item" href="sitios_interes.php">Sitios de Interés</a></li>
                        <li><a class="dropdown-item" href="comunidad.php">Comunidad Digital</a></li>
                        <li><a class="dropdown-item" href="sugerencias.php">Sugerencias MH</a></li>
                        <li><a class="dropdown-item" href="testimonios.php">Testimonios</a></li>
                    </ul>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto d-flex align-items-center">
                <?php if (isLogged()): ?>
                    <li class="nav-item"><a href="cita_nueva.php" class="nav-link">Agendar Cita</a></li>
                    <li class="nav-item"><a href="logout.php" class="nav-link">Cerrar sesión</a></li>
                <?php else: ?>
                    <li class="nav-item"><a href="login.php" class="nav-link text-light me-2">Iniciar Sesión</a></li>
                    <li class="nav-item"><a href="signup.php" class="nav-link text-warning">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="meta-navbar-placeholder" aria-hidden="true" style="height: 2.5rem; background: transparent;"></div>