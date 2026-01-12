<?php
require_once '../app/config/db.php';
require_once '../app/models/Servicio.php';
require_once '../app/helpers/auth.php';
$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $stmt = $pdo->prepare("SELECT s.*, a.Nombre AS Agencia FROM Servicio s LEFT JOIN Agencia a ON s.Agencia_idAgencia = a.idAgencia WHERE s.Activo=1 AND (s.Nombre LIKE ? OR s.Descripcion LIKE ?) ORDER BY s.Nombre");
    $like = "%" . $q . "%";
    $stmt->execute([$like, $like]);
    $servicios = $stmt->fetchAll();
} else {
    $servicios = Servicio::getAll($pdo);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de Servicios - MetaHogar</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/bs-navbar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Widget de Accesibilidad -->
    <script src="https://cdn.userway.org/widget.js" data-account="kjnkkEfZy1"></script>
    <style>
        .userway-icon {
            position: fixed !important;
            right: 20px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            z-index: 9999 !important;
        }
        .future-service-card { 
            border: none; 
            border-radius: 15px; 
            box-shadow: 0 8px 20px rgba(0,0,0,0.1); 
            transition: all 0.3s; 
            background: white; 
            border-left: 4px solid #17466e;
        }
        .future-service-card:hover { 
            transform: translateY(-4px); 
            box-shadow: 0 12px 30px rgba(0,0,0,0.15); 
        }
        .service-icon { 
            font-size: 2rem; 
            margin-bottom: 10px; 
        }

        /* Contenedor de imagen: fija la altura y evita que la imagen se salga del card */
        .service-img-wrapper {
            width: 100%;
            height: 220px; /* ajuste inicial, cambia según diseño */
            overflow: hidden;
            border-top-left-radius: 0.375rem;
            border-top-right-radius: 0.375rem;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* La imagen ocupa el contenedor y se recorta manteniendo proporción
           - max-* evita que imágenes muy altas/anchas escapen del layout
           - object-fit: cover recorta manteniendo el aspecto; cambiar a contain si
             prefieres que la imagen completa se muestre (con letterbox)
        */
        .service-img-wrapper img {
            width: 100%;
            height: 100%;
            max-width: 100%;
            max-height: 100%;
            object-fit: cover; /* use 'contain' si prefieres sin recorte */
            object-position: center center;
            display: block;
        }

        /* Evita que la imagen u otros contenidos sobresalgan del card */
        .card {
            overflow: hidden;
        }

        @media (max-width: 576px) {
            .service-img-wrapper { height: 160px; }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/partials/bs-navbar.php'; ?>
    <?php
    // Mostrar botón al panel de admin solo si el usuario es administrador
    $isAdmin = false;
    if (isLogged()) {
        $userId = getUserId();
        try {
            $stm = $pdo->prepare('SELECT r.Nombre FROM usuariorol ur JOIN rol r ON ur.Rol_idRol = r.idRol WHERE ur.Usuario_idUsuario = ? LIMIT 1');
            $stm->execute([$userId]);
            $rol = $stm->fetchColumn();
            if ($rol === 'administrador' || $rol === 'admin') $isAdmin = true;
        } catch (Exception $e) {
            // ignore
        }
    }
    ?>
    <div class="container py-5">
        <?php if ($isAdmin): ?>
            <div class="mb-3 text-end">
                <a href="admin/servicios.php" class="btn btn-sm btn-outline-primary">Panel Admin</a>
            </div>
        <?php endif; ?>
        <h2 class="text-primary mb-4">Servicios Disponibles</h2>
        <div class="row">
            <?php foreach ($servicios as $s): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="service-img-wrapper">
                        <?php if (!empty($s['Imagen'])): ?>
                            <img src="assets/img/servicios/<?= htmlspecialchars($s['Imagen']) ?>" alt="<?= htmlspecialchars($s['Nombre']) ?>">
                        <?php else: ?>
                            <img src="assets/img/service-placeholder.png" alt="Sin imagen">
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-primary"><?= htmlspecialchars($s['Nombre']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($s['Descripcion']) ?></p>
                        <p><strong>Costo:</strong> $<?= number_format($s['Costo'],2) ?></p>
                        <p><strong>Agencia:</strong> <?= htmlspecialchars($s['Agencia']) ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Sección de Servicios Futuros -->
        <div class="mt-5 pt-4 border-top">
            <div class="text-center mb-5">
                <h3 class="text-primary mb-2">Próximamente</h3>
                <p class="text-muted">Servicios especializados que estarán disponibles pronto</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="future-service-card p-4 text-center h-100">
                        <div class="service-icon">🔍</div>
                        <h5 class="card-title mb-3">Diagnóstico de Sistemas</h5>
                        <p class="card-text text-muted">Análisis profundo de tu hogar inteligente y detección de problemas.</p>
                        <span class="badge bg-secondary">Próximamente</span>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="future-service-card p-4 text-center h-100">
                        <div class="service-icon">🛠️</div>
                        <h5 class="card-title mb-3">Mantenimiento Preventivo</h5>
                        <p class="card-text text-muted">Servicios de mantenimiento para optimizar rendimiento.</p>
                        <span class="badge bg-secondary">Próximamente</span>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="future-service-card p-4 text-center h-100">
                        <div class="service-icon">⚙️</div>
                        <h5 class="card-title mb-3">Instalación y Configuración</h5>
                        <p class="card-text text-muted">Instalamos y configuramos tus dispositivos MetaHogar.</p>
                        <span class="badge bg-secondary">Próximamente</span>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="future-service-card p-4 text-center h-100">
                        <div class="service-icon">📊</div>
                        <h5 class="card-title mb-3">Reportes Detallados</h5>
                        <p class="card-text text-muted">Reportes completos del estado y desempeño de tu sistema.</p>
                        <span class="badge bg-secondary">Próximamente</span>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="future-service-card p-4 text-center h-100">
                        <div class="service-icon">💡</div>
                        <h5 class="card-title mb-3">Optimización de Consumo</h5>
                        <p class="card-text text-muted">Recomendaciones para reducir consumo energético.</p>
                        <span class="badge bg-secondary">Próximamente</span>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="future-service-card p-4 text-center h-100">
                        <div class="service-icon">🔐</div>
                        <h5 class="card-title mb-3">Auditoría de Seguridad</h5>
                        <p class="card-text text-muted">Verificamos la seguridad de tu hogar y datos.</p>
                        <span class="badge bg-secondary">Próximamente</span>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-lg-8 mx-auto">
                    <div class="card" style="border-radius: 15px; border: none; box-shadow: 0 8px 20px rgba(0,0,0,0.1);">
                        <div class="card-body p-4 text-center">
                            <h5 class="card-title mb-3 text-primary">¿Te interesa alguno de estos servicios?</h5>
                            <p class="card-text text-muted mb-4">Contáctanos para conocer más sobre nuestra próxima oferta de servicios especializados.</p>
                            <a href="cita_nueva.php" class="btn btn-primary">Contactar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>