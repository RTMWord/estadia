<?php
require_once '../../app/config/db.php';
require_once '../../app/helpers/auth.php';
requireRole($pdo, 'administrador');

// Obtener métricas rápidas
    try {
    $counts = [];
    $counts['usuarios'] = (int)$pdo->query('SELECT COUNT(*) FROM usuario')->fetchColumn();
    $counts['agencias'] = (int)$pdo->query('SELECT COUNT(*) FROM agencia')->fetchColumn();
    $counts['servicios'] = (int)$pdo->query('SELECT COUNT(*) FROM servicio')->fetchColumn();
    $counts['citas'] = (int)$pdo->query('SELECT COUNT(*) FROM cita')->fetchColumn();
    $counts['sugerencias'] = (int)$pdo->query('SELECT COUNT(*) FROM sugerencia')->fetchColumn();
    // Nuevos módulos
    $counts['productos'] = (int)$pdo->query('SELECT COUNT(*) FROM producto')->fetchColumn();
    $counts['contenidos'] = (int)$pdo->query('SELECT COUNT(*) FROM contenido')->fetchColumn();
    // multimedia y testimonios (testimonio tabla puede no existir aún)
    try { $counts['multimedia'] = (int)$pdo->query('SELECT COUNT(*) FROM multimedia')->fetchColumn(); } catch (Exception $e) { $counts['multimedia'] = 0; }
    try { $counts['testimonios'] = (int)$pdo->query('SELECT COUNT(*) FROM testimonio')->fetchColumn(); } catch (Exception $e) { $counts['testimonios'] = 0; }
} catch (Exception $e) {
    // En caso de error, inicializar en 0
    $counts = ['usuarios'=>0,'agencias'=>0,'servicios'=>0,'citas'=>0,'sugerencias'=>0,'productos'=>0,'contenidos'=>0,'multimedia'=>0,'testimonios'=>0];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Panel Admin - MetaHogar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Panel de Administración</h1>
        <div>
            <a href="../index.php" class="btn btn-outline-secondary">Ir al sitio</a>
            <a href="../logout.php" class="btn btn-outline-danger">Cerrar sesión</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Usuarios</h5>
                    <p class="display-6 mb-0"><?= $counts['usuarios'] ?></p>
                </div>
                <div class="card-footer">
                    <a href="usuarios.php" class="stretched-link">Gestionar usuarios</a>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Agencias</h5>
                    <p class="display-6 mb-0"><?= $counts['agencias'] ?></p>
                </div>
                <div class="card-footer">
                    <a href="agencias.php" class="stretched-link">Validar agencias</a>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Servicios</h5>
                    <p class="display-6 mb-0"><?= $counts['servicios'] ?></p>
                </div>
                <div class="card-footer">
                    <a href="servicios.php" class="stretched-link">Gestionar servicios</a>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Productos</h5>
                    <p class="display-6 mb-0"><?= $counts['productos'] ?></p>
                </div>
                <div class="card-footer">
                    <a href="productos.php" class="stretched-link">Gestionar productos</a>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Contenidos</h5>
                    <p class="display-6 mb-0"><?= $counts['contenidos'] ?></p>
                </div>
                <div class="card-footer">
                    <a href="contenidos.php" class="stretched-link">Gestionar contenidos</a>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Testimonios</h5>
                    <p class="display-6 mb-0"><?= $counts['testimonios'] ?></p>
                </div>
                <div class="card-footer">
                    <a href="testimonios.php" class="stretched-link">Gestionar testimonios</a>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Citas</h5>
                    <p class="display-6 mb-0"><?= $counts['citas'] ?></p>
                </div>
                <div class="card-footer">
                    <a href="citas.php" class="stretched-link">Ver citas</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Buzón de sugerencias</h5>
                    <p class="mb-0">Sugerencias pendientes: <strong><?= $counts['sugerencias'] ?></strong></p>
                    <a href="sugerencias.php" class="btn btn-link mt-2">Administrar sugerencias</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Acciones rápidas</h5>
                    <a href="usuario_nuevo.php" class="btn btn-primary me-2">Crear usuario</a>
                    <a href="servicio_nuevo.php" class="btn btn-primary me-2">Crear servicio</a>
                    <a href="agencia_nueva.php" class="btn btn-primary me-2">Registrar agencia</a>
                    <a href="reportes.php" class="btn btn-info mt-2"> Generar Reportes...</a>
                    
                    <a href="backup.php" class="btn btn-success mt-2">Generar Respaldo (.sql)</a>
                    <a href="restore.php" class="btn btn-danger mt-2">Restaurar BD</a>

                    <a href="reportes.php" class="btn btn-info mt-2"> Generar Reportes...</a> 
                </div>
            </div>
        </div>
    </div>

</div>
</body>
</html>