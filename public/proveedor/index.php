<?php
require_once __DIR__ . '/_security_check.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Panel Proveedor - MetaHogar</title>
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container py-5 provider-panel">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Panel de Proveedor</h1>
            <div>
                <a href="../index.php" class="btn btn-outline-secondary">Ir al sitio</a>
                <a href="../logout.php" class="btn btn-outline-danger">Cerrar sesión</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Contenidos</h5>
                        <p class="card-text">Gestiona y propone artículos, noticias o blogs.</p>
                        <a href="contenidos.php" class="btn btn-primary">Mis contenidos</a>
                        <a href="contenido_nuevo.php" class="btn btn-outline-primary ms-2">Proponer contenido</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Servicios</h5>
                        <p class="card-text">Propón servicios que el administrador validará.</p>
                        <a href="servicio_proponer.php" class="btn btn-primary">Proponer servicio</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Comunidad Digital</h5>
                        <p class="card-text">Participa en la comunidad: preguntas y respuestas.</p>
                        <a href="../comunidad.php" class="btn btn-success" draggable="false" ondragstart="return false;" style="-webkit-user-drag: none; user-drag: none; user-select: none;">Ir a Comunidad</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
