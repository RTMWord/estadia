<?php
// public/partials/bs-navbar.php
// Partial que renderiza el navbar Bootstrap que enviaste.
// Instrucciones: inclúyelo justo después de <body> en tu index.php con:
// <?php include __DIR__ . '/partials/bs-navbar.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-gradient-primary meta-navbar-root fixed-top" role="navigation" aria-label="Main navbar">
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
                <li class="nav-item">
                    <a class="nav-link" href="quienes_somos.php">¿Quiénes Somos?</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="vision.php">Visión</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="mision.php">Misión</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="servicios.html">Servicios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="productos.html">Productos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="blog.html">Blog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="sugerencias.php">Sugerencias MH</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contacto.html">Contacto</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="sitios_interes.php">Sitios de Interés</a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto d-flex align-items-center">
                <?php if (isLogged()): ?>
                    <li class="nav-item"><a href="cita_nueva.php" class="nav-link">Agendar Cita</a></li>
                    <li class="nav-item"><a href="testimonios.php" class="nav-link">Testimonios</a></li>
                    <li class="nav-item"><a href="logout.php" class="nav-link">Cerrar sesión</a></li>
                <?php else: ?>
                    <li class="nav-item"><a href="login.php" class="nav-link text-light me-2">Iniciar Sesión</a></li>
                    <li class="nav-item"><a href="signup.php" class="nav-link text-warning">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="navbar-placeholder" style="height: 31px;" aria-hidden="true"></div>