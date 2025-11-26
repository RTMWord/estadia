<?php
// public/admin/partials/admin_nav.php
// Reusable admin navigation bar
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Admin Panel</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="adminNavbar">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="servicios.php">Servicios</a></li>
        <li class="nav-item"><a class="nav-link" href="agencias.php">Agencias</a></li>
        <li class="nav-item"><a class="nav-link" href="productos.php">Productos</a></li>
        <li class="nav-item"><a class="nav-link" href="contenidos.php">Contenidos</a></li>
        <li class="nav-item"><a class="nav-link" href="galeria.php">Galería</a></li>
        <li class="nav-item"><a class="nav-link" href="testimonios.php">Testimonios</a></li>
        <li class="nav-item"><a class="nav-link" href="sugerencias.php">Sugerencias</a></li>
        <li class="nav-item"><a class="nav-link" href="citas.php">Citas</a></li>
        <li class="nav-item"><a class="nav-link" href="reportes.php">Reportes</a></li>
        <li class="nav-item"><a class="nav-link" href="backup.php">Backups</a></li>
        <li class="nav-item"><a class="nav-link" href="usuarios.php">Usuarios</a></li>
      </ul>
      <div class="d-flex">
        <a class="btn btn-outline-light btn-sm me-2" href="../index.php">Ver sitio</a>
        <a class="btn btn-danger btn-sm" href="../logout.php">Cerrar sesión</a>
      </div>
    </div>
  </div>
</nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

