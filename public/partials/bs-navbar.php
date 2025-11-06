<?php
// public/partials/bs-navbar.php
// Partial que renderiza el navbar Bootstrap que enviaste.
// Instrucciones: inclúyelo justo después de <body> en tu index.php con:
// <?php include __DIR__ . '/partials/bs-navbar.php';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-gradient-primary meta-navbar-root fixed-top" role="navigation" aria-label="Main navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="meta3.html">
            <span class="ms-2">MetaHogar</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="meta3.html">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="sobre-nosotros.html">¿Quiénes Somos?</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="vision.html">Visión</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="mision.html">Misión</a>
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
                    <a class="nav-link" href="buzon.html">Buzón</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contacto.html">Contacto</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="sitiosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Sitios de Interés
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="sitiosDropdown">
                        <li><a class="dropdown-item" href="https://www.microsoft.com/es-mx" target="_blank" rel="noopener">Microsoft</a></li>
                        <li><a class="dropdown-item" href="https://www.hp.com/mx-es/home.html" target="_blank" rel="noopener">HP</a></li>
                        <li><a class="dropdown-item" href="https://www.dell.com/es-mx" target="_blank" rel="noopener">Dell</a></li>
                        <li><a class="dropdown-item" href="https://www.lenovo.com/mx/es/" target="_blank" rel="noopener">Lenovo</a></li>
                    </ul>
                </li>
            </ul>

            <div class="d-flex">
                <a href="login.php" class="btn btn-outline-light me-2">Iniciar Sesión</a>
                <a href="signup.php" class="btn btn-warning">Registrarse</a>
            </div>
        </div>
    </div>
</nav>
<div class="navbar-placeholder" style="height: 31px;" aria-hidden="true"></div>