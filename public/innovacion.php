<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/helpers/auth.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Innovación - MetaHogar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: #f5f7fb; font-family: 'Segoe UI', sans-serif; }
        .hero { background: linear-gradient(180deg, #17466e 0%, #4b96c3 100%); color: white; padding: 80px 0 60px; }
        .innovation-card { border: none; border-radius: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); transition: all 0.3s; background: white; overflow: hidden; }
        .innovation-card:hover { transform: translateY(-8px); box-shadow: 0 12px 30px rgba(0,0,0,0.15); }
        .project-badge { background: linear-gradient(180deg, #17466e 0%, #4b96c3 100%); color: white; display: inline-block; padding: 8px 16px; border-radius: 20px; font-size: 0.85rem; margin-bottom: 12px; }
        .progress-item { margin-bottom: 20px; }
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
            <h1 class="display-5 fw-bold mb-3">Portal de Innovación</h1>
            <p class="lead">Tendencias, investigación y proyectos estratégicos</p>
        </div>
    </div>

    <div class="container py-5">
        <h2 class="mb-5" style="color: #17466e;">Proyectos en Desarrollo</h2>

        <div class="row g-4 mb-5">
            <div class="col-lg-6">
                <div class="innovation-card p-4 h-100">
                    <span class="project-badge">En Investigación</span>
                    <h5 class="card-title mb-3" style="color: #17466e;">IA para Automatización Inteligente</h5>
                    <p class="card-text text-muted mb-3">Desarrollo de algoritmos de inteligencia artificial para automatización predictiva del hogar.</p>
                    <div class="progress-item">
                        <label class="small fw-bold">Progreso: 65%</label>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" style="width: 65%; background: #17466e;"></div>
                        </div>
                    </div>
                    <p class="small text-muted">Estimado: Q2 2025</p>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="innovation-card p-4 h-100">
                    <span class="project-badge">En Desarrollo</span>
                    <h5 class="card-title mb-3" style="color: #17466e;">Integración con IoT Avanzado</h5>
                    <p class="card-text text-muted mb-3">Compatibilidad con nuevos estándares de Internet of Things para mayor interoperabilidad.</p>
                    <div class="progress-item">
                        <label class="small fw-bold">Progreso: 80%</label>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" style="width: 80%; background: #4b96c3;"></div>
                        </div>
                    </div>
                    <p class="small text-muted">Estimado: Q3 2025</p>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="innovation-card p-4 h-100">
                    <span class="project-badge">Beta Testing</span>
                    <h5 class="card-title mb-3" style="color: #17466e;">App Móvil Mejorada v2.0</h5>
                    <p class="card-text text-muted mb-3">Nueva interfaz de usuario con mejor rendimiento y accesibilidad para adultos mayores.</p>
                    <div class="progress-item">
                        <label class="small fw-bold">Progreso: 90%</label>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" style="width: 90%; background: #2d6a8f;"></div>
                        </div>
                    </div>
                    <p class="small text-muted">Estimado: Q1 2026</p>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="innovation-card p-4 h-100">
                    <span class="project-badge">Planificado</span>
                    <h5 class="card-title mb-3" style="color: #17466e;">Sistema de Vigilancia Inteligente</h5>
                    <p class="card-text text-muted mb-3">Cámaras con IA para detección de anomalías y seguridad mejorada del hogar.</p>
                    <div class="progress-item">
                        <label class="small fw-bold">Progreso: 20%</label>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" style="width: 20%; background: #7fa8c9;"></div>
                        </div>
                    </div>
                    <p class="small text-muted">Estimado: Q3 2025</p>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-lg-12">
                <h2 class="mb-4" style="color: #17466e;">Tendencias Tecnológicas</h2>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card" style="border: none; border-left: 4px solid #17466e; border-radius: 8px;">
                            <div class="card-body">
                                <h6 class="card-title" style="color: #17466e;">🤖 Machine Learning</h6>
                                <p class="card-text small text-muted">Aplicaciones de ML en domótica para predicción de comportamiento del usuario.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card" style="border: none; border-left: 4px solid #4b96c3; border-radius: 8px;">
                            <div class="card-body">
                                <h6 class="card-title" style="color: #4b96c3;">🔐 Blockchain</h6>
                                <p class="card-text small text-muted">Seguridad de datos mediante tecnología blockchain distribuida.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card" style="border: none; border-left: 4px solid #2d6a8f; border-radius: 8px;">
                            <div class="card-body">
                                <h6 class="card-title" style="color: #2d6a8f;">🌱 Sostenibilidad</h6>
                                <p class="card-text small text-muted">Energías renovables e integración con sistemas ecológicos.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
