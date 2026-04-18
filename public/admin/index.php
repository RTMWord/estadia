<?php

require_once __DIR__ . '/_security_check.php';

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
    try { $counts['incidencias'] = (int)$pdo->query('SELECT COUNT(*) FROM incidencia')->fetchColumn(); } catch (Exception $e) { $counts['incidencias'] = 0; }
} catch (Exception $e) {
    // En caso de error, inicializar en 0
    $counts = ['usuarios'=>0,'agencias'=>0,'servicios'=>0,'citas'=>0,'sugerencias'=>0,'productos'=>0,'contenidos'=>0,'multimedia'=>0,'testimonios'=>0,'incidencias'=>0];
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
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/bs-navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.userway.org/widget.js" data-account="kjnkkEfZy1"></script>
    <style>
        .userway-icon {
            position: fixed !important;
            right: 20px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            z-index: 9999 !important;
        }
        body {
            background: #eeeeee;
            color: #2f3d4a;
        }
        .admin-dashboard {
            max-width: 1140px;
        }
        .admin-title {
            font-size: 2rem;
            letter-spacing: .5px;
            margin-bottom: .2rem;
            text-transform: uppercase;
        }
        .admin-subtitle {
            font-size: 1.1rem;
            color: #4f5d6b;
            margin-bottom: 2rem;
        }
        .dashboard-tile {
            border: 1px solid #d8d8d8;
            background: #ffffff;
            border-radius: 4px;
            box-shadow: 0 1px 0 rgba(0, 0, 0, .03);
            height: 100%;
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .dashboard-tile:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 24px rgba(0, 0, 0, .08);
        }
        .dashboard-tile .card-header {
            border-bottom: 1px solid #dfdfdf;
            background: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            color: #2f6374;
            text-align: center;
            font-size: 1.05rem;
            letter-spacing: .4px;
        }
        .dashboard-tile .card-body {
            text-align: center;
            padding: 1.2rem .75rem 1rem;
        }
        .tile-icon {
            font-size: 5rem;
            color: #3f6570;
            line-height: 1;
            margin-bottom: .8rem;
        }
        .tile-counter {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1.1;
            color: #2f3d4a;
        }
        .tile-label {
            color: #315d67;
            font-size: 1.05rem;
            margin-top: .2rem;
            font-weight: 600;
        }
        .dashboard-tile .card-footer {
            text-align: center;
            background: #fafafa;
            border-top: 1px solid #e3e3e3;
        }
        .dashboard-tile .card-footer a {
            color: #1e70d2;
            text-decoration: none;
            font-weight: 500;
        }
        .quick-panel {
            border: 1px solid #dadada;
            background: #fff;
            border-radius: 6px;
        }
        .quick-panel .card-title {
            font-weight: 700;
            color: #2f3d4a;
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/partials/admin_nav.php'; ?>
<?php
$dashboardTiles = [
    [
        'title' => 'Usuarios',
        'count' => $counts['usuarios'],
        'icon' => 'fa-solid fa-user',
        'href' => 'usuarios.php',
        'cta' => 'Gestionar usuarios',
        'suffix' => 'Registrados'
    ],
    [
        'title' => 'Agencias',
        'count' => $counts['agencias'],
        'icon' => 'fa-solid fa-truck',
        'href' => 'agencias.php',
        'cta' => 'Validar agencias',
        'suffix' => 'Registradas'
    ],
    [
        'title' => 'Servicios',
        'count' => $counts['servicios'],
        'icon' => 'fa-solid fa-gears',
        'href' => 'servicios.php',
        'cta' => 'Gestionar servicios',
        'suffix' => 'Registrados'
    ],
    [
        'title' => 'Productos',
        'count' => $counts['productos'],
        'icon' => 'fa-solid fa-box-open',
        'href' => 'productos.php',
        'cta' => 'Gestionar productos',
        'suffix' => 'Registrados'
    ],
    [
        'title' => 'Contenidos',
        'count' => $counts['contenidos'],
        'icon' => 'fa-solid fa-tags',
        'href' => 'contenidos.php',
        'cta' => 'Gestionar contenidos',
        'suffix' => 'Registrados'
    ],
    [
        'title' => 'Testimonios',
        'count' => $counts['testimonios'],
        'icon' => 'fa-solid fa-comments',
        'href' => 'testimonios.php',
        'cta' => 'Gestionar testimonios',
        'suffix' => 'Registrados'
    ],
    [
        'title' => 'Citas',
        'count' => $counts['citas'],
        'icon' => 'fa-solid fa-calendar-check',
        'href' => 'citas.php',
        'cta' => 'Ver citas',
        'suffix' => 'Registradas'
    ],
];
?>
<div class="container admin-dashboard py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="admin-title"><i class="fa-solid fa-gauge-high me-2"></i>Dashboard</h1>
            <p class="admin-subtitle mb-0">¡Bienvenido <strong>Administrador Principal</strong>! Este es el panel principal del sistema.</p>
        </div>
        <div>
            <a href="../index.php" class="btn btn-outline-secondary">Ir al sitio</a>
            <a href="../logout.php" class="btn btn-outline-danger">Cerrar sesión</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <?php foreach ($dashboardTiles as $tile): ?>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card dashboard-tile">
                    <div class="card-header"><?= htmlspecialchars($tile['title']) ?></div>
                    <div class="card-body">
                        <div class="tile-icon"><i class="<?= htmlspecialchars($tile['icon']) ?>" aria-hidden="true"></i></div>
                        <div class="tile-counter"><?= (int)$tile['count'] ?></div>
                        <div class="tile-label"><?= htmlspecialchars($tile['suffix']) ?></div>
                    </div>
                    <div class="card-footer">
                        <a href="<?= htmlspecialchars($tile['href']) ?>" class="stretched-link"><?= htmlspecialchars($tile['cta']) ?></a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="col-6 col-md-3">
            <div class="card dashboard-tile">
                <div class="card-header">Solicitudes Diagnóstico</div>
                <div class="card-body">
                    <?php
                    $diagFile = __DIR__ . '/../../data/diagnosticos.json';
                    $diagCount = 0;
                    if (is_file($diagFile)) {
                        $raw = @file_get_contents($diagFile);
                        $arr = $raw ? json_decode($raw, true) : [];
                        if (is_array($arr)) $diagCount = count($arr);
                    }
                    ?>
                    <div class="tile-icon"><i class="fa-solid fa-clipboard-check" aria-hidden="true"></i></div>
                    <div class="tile-counter"><?= $diagCount ?></div>
                    <div class="tile-label">Pendientes</div>
                </div>
                <div class="card-footer">
                    <a href="solicitudes_diagnostico.php" class="stretched-link">Ver solicitudes</a>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card dashboard-tile">
                <div class="card-header">Comunidad</div>
                <div class="card-body">
                    <div class="tile-icon"><i class="fa-solid fa-users" aria-hidden="true"></i></div>
                    <div class="tile-counter"><?= $counts['incidencias'] ?></div>
                    <div class="tile-label">Registrados</div>
                </div>
                <div class="card-footer">
                    <a href="comunidad.php" class="stretched-link">Gestionar comunidad</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card quick-panel">
                <div class="card-body">
                    <h5 class="card-title">Buzón de sugerencias</h5>
                    <p class="mb-0">Sugerencias pendientes: <strong><?= $counts['sugerencias'] ?></strong></p>
                    <a href="sugerencias.php" class="btn btn-link mt-2">Administrar sugerencias</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card quick-panel">
                <div class="card-body">
                    <h5 class="card-title">Acciones rápidas</h5>
                    <a href="usuario_nuevo.php" class="btn btn-primary me-2">Crear usuario</a>
                    <a href="servicio_nuevo.php" class="btn btn-primary me-2">Crear servicio</a>
                    <a href="agencia_nueva.php" class="btn btn-primary me-2">Registrar agencia</a>
                    <a href="comunidad.php" class="btn btn-primary me-2">Gestionar comunidad</a>
                    <a href="reportes.php" class="btn btn-info mt-2"> Generar Reportes...</a>
                    
                    <a href="backup.php" class="btn btn-success mt-2">Generar Respaldo (.sql)</a>
                    <a href="restore.php" class="btn btn-danger mt-2">Restaurar BD</a>
                    <a href="multimedia_testimonios.php" class="btn btn-warning mt-2">Multimedia (carrusel testimonios)</a>
                </div>
            </div>
        </div>
    </div>

    </div>
</body>
</html>