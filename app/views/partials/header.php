<?php
// app/views/partials/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Asegurarse de que existan constantes (si alguien incluye solo este archivo)
if (!defined('ROOT_PATH')) {
    // Si no están definidas, intenta cargarlas por ruta relativa
    require_once __DIR__ . '/../../app/init.php';
}

// Conexión / auth helpers (opcional: si ya lo hace la página, no hace daño)
if (file_exists(ROOT_PATH . '/app/config/db.php')) {
    require_once ROOT_PATH . '/app/config/db.php';
}
if (file_exists(ROOT_PATH . '/app/helpers/auth.php')) {
    require_once ROOT_PATH . '/app/helpers/auth.php';
}

// Obtener usuario si hay sesión
$user = null;
if (function_exists('isLogged') && isLogged()) {
    // getUserId() debe estar en app/helpers/auth.php
    try {
        $stmt = $pdo->prepare('SELECT idUsuario, Nombre, ApellidoP FROM Usuario WHERE idUsuario = ? LIMIT 1');
        $stmt->execute([getUserId()]);
        $user = $stmt->fetch();
    } catch (Exception $e) {
        // Silenciar errores aquí; las páginas que necesiten DB ya incluyen db.php
    }
}

// Título por página: permitir que la página defina $pageTitle antes de incluir header
if (!isset($pageTitle)) {
    $pageTitle = 'MetaHogar';
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?> - MetaHogar</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand text-primary" href="index.php">MetaHogar</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php#nosotros">Nosotros</a></li>
        <li class="nav-item"><a class="nav-link" href="servicios.php">Servicios</a></li>
        <li class="nav-item"><a class="nav-link" href="citas.php">Citas</a></li>
        <li class="nav-item"><a class="nav-link" href="sugerencias.php">Sugerencias</a></li>
      </ul>

      <div class="d-flex">
        <?php if (!$user): ?>
            <a href="login.php?next=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn btn-outline-primary">Iniciar sesión</a>
        <?php else: ?>
            <span class="me-2 align-self-center">Hola, <?= htmlspecialchars($user['Nombre']) ?></span>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'administrador'): ?>
                <a href="admin/index.php" class="btn btn-outline-secondary me-2">Panel</a>
            <?php endif; ?>
            <a href="logout.php" class="btn btn-outline-danger">Salir</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
<main class="container my-5"></main>