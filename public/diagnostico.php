<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/helpers/auth.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Diagnóstico - MetaHogar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: #f5f7fb; font-family: 'Segoe UI', sans-serif; }
        .hero { background: linear-gradient(180deg, #17466e 0%, #4b96c3 100%); color: white; padding: 80px 0 60px; }
        .service-card { border: none; border-radius: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); transition: all 0.3s; background: white; }
        .service-card:hover { transform: translateY(-8px); box-shadow: 0 12px 30px rgba(0,0,0,0.15); }
        .service-icon { font-size: 2.5rem; margin-bottom: 15px; }
    </style>
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
            <h1 class="display-5 fw-bold mb-3">Centro de Diagnóstico</h1>
            <p class="lead">Servicios especializados y soporte técnico</p>
        </div>
    </div>

    <div class="container py-5">
        <h2 class="mb-5" style="color: #17466e;">Nuestros Servicios</h2>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="service-card p-4 text-center h-100">
                    <div class="service-icon">🔍</div>
                    <h5 class="card-title mb-3">Diagnóstico de Sistemas</h5>
                    <p class="card-text text-muted">Análisis profundo de tu hogar inteligente y detección de problemas.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="service-card p-4 text-center h-100">
                    <div class="service-icon">🛠️</div>
                    <h5 class="card-title mb-3">Mantenimiento Preventivo</h5>
                    <p class="card-text text-muted">Servicios de mantenimiento para optimizar rendimiento.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="service-card p-4 text-center h-100">
                    <div class="service-icon">⚙️</div>
                    <h5 class="card-title mb-3">Instalación y Configuración</h5>
                    <p class="card-text text-muted">Instalamos y configuramos tus dispositivos MetaHogar.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="service-card p-4 text-center h-100">
                    <div class="service-icon">📊</div>
                    <h5 class="card-title mb-3">Reportes Detallados</h5>
                    <p class="card-text text-muted">Reportes completos del estado y desempeño de tu sistema.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="service-card p-4 text-center h-100">
                    <div class="service-icon">💡</div>
                    <h5 class="card-title mb-3">Optimización de Consumo</h5>
                    <p class="card-text text-muted">Recomendaciones para reducir consumo energético.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="service-card p-4 text-center h-100">
                    <div class="service-icon">🔐</div>
                    <h5 class="card-title mb-3">Auditoría de Seguridad</h5>
                    <p class="card-text text-muted">Verificamos la seguridad de tu hogar y datos.</p>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-lg-8 mx-auto">
                <div class="card" style="border-radius: 15px; border: none; box-shadow: 0 8px 20px rgba(0,0,0,0.1);">
                    <div class="card-body p-5">
                        <h4 class="card-title mb-3" style="color: #17466e;">¿Necesitas un diagnóstico?</h4>
                        <p class="card-text text-muted mb-4">Contacta con nuestro equipo de expertos para agendar una cita y diagnosticar el estado de tu sistema.</p>
                        <a href="cita_nueva.php" class="btn btn-lg" style="background: #17466e; color: white;">Agendar Cita</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
