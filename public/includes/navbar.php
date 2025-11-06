<?php
// Evita el acceso directo
if (!defined('ESTADIA_INIT')) { exit('Acceso denegado'); }

// Asume que el navbar debe estar en el header.php
?>
<header class="navbar-header">
    <nav class="navbar-nav">
        <a href="index.php" class="logo">Estadía</a>
        <div class="nav-links">
            <a href="servicios.php">Servicios</a>
            <a href="citas.php">Mis Citas</a>
            <?php if (is_logged_in()): ?>
                <a href="admin/" class="btn-admin">Admin</a>
                <a href="logout.php">Cerrar Sesión</a>
            <?php else: ?>
                <a href="login.php">Iniciar Sesión</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<style>
/* Estilos básicos para el navbar (deberían ir en style.css) */
</style>