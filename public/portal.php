<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/helpers/auth.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal MetaHogar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: #f5f7fb; font-family: 'Segoe UI', sans-serif; }
        .hero { background: linear-gradient(180deg, #17466e 0%, #4b96c3 100%); color: white; padding: 80px 0 60px; }
        .card-portal { border: none; border-radius: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); transition: all 0.3s; }
        .card-portal:hover { transform: translateY(-5px); box-shadow: 0 12px 30px rgba(0,0,0,0.15); }
        .news-item { border-left: 4px solid #17466e; padding: 20px; background: white; border-radius: 8px; margin-bottom: 15px; }
        .badge-new { background: #ff6b6b; color: white; }
    </style>
    
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
    </style>
</head>
<body>
    <?php include __DIR__ . '/partials/bs-navbar.php'; ?>

    <div class="hero text-center">
        <div class="container">
            <h1 class="display-5 fw-bold mb-3">Portal MetaHogar</h1>
            <p class="lead">Centro de información corporativa y novedades internas</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8">
                <h2 class="mb-4" style="color: #17466e;">Últimas Noticias</h2>
                <div class="news-item">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="mb-0">Lanzamiento de Nuevos Servicios</h5>
                        <span class="badge badge-new">Nueva</span>
                    </div>
                    <p class="text-muted small mb-2">Hace 2 días</p>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Presentamos nuestras nuevas soluciones innovadoras para el hogar inteligente.</p>
                </div>

                <div class="news-item">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="mb-0">Evento de Capacitación Corporativa</h5>
                        <span class="badge" style="background: #4b96c3; color: white;">Evento</span>
                    </div>
                    <p class="text-muted small mb-2">Hace 1 semana</p>
                    <p>Se llevará a cabo la próxima sesión de capacitación para todos los colaboradores sobre las nuevas herramientas corporativas.</p>
                </div>

                <div class="news-item">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="mb-0">Logros del Trimestre</h5>
                        <span class="badge" style="background: #4caf50; color: white;">Logro</span>
                    </div>
                    <p class="text-muted small mb-2">Hace 2 semanas</p>
                    <p>Alcanzamos nuestras metas de crecimiento y satisfacción de clientes gracias al esfuerzo del equipo MetaHogar.</p>
                </div>
            </div>

            <div class="col-lg-4">
                <h2 class="mb-4" style="color: #17466e;">Accesos Rápidos</h2>
                <div class="card card-portal mb-3">
                    <div class="card-body">
                        <h6 class="card-title">📋 Documentos Internos</h6>
                        <p class="card-text small">Accede a políticas, procedimientos y documentación oficial.</p>
                        <a href="#" class="btn btn-sm" style="background: #17466e; color: white;">Ver más</a>
                    </div>
                </div>
                <div class="card card-portal mb-3">
                    <div class="card-body">
                        <h6 class="card-title">👥 Directorio de Personal</h6>
                        <p class="card-text small">Encuentra contactos de colaboradores y departamentos.</p>
                        <a href="#" class="btn btn-sm" style="background: #17466e; color: white;">Ver más</a>
                    </div>
                </div>
                <div class="card card-portal">
                    <div class="card-body">
                        <h6 class="card-title">📅 Calendario de Eventos</h6>
                        <p class="card-text small">Próximos eventos, reuniones y actividades corporativas.</p>
                        <a href="#" class="btn btn-sm" style="background: #17466e; color: white;">Ver más</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
