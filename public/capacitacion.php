<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/helpers/auth.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capacitación - MetaHogar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: #f5f7fb; font-family: 'Segoe UI', sans-serif; }
        .hero { background: linear-gradient(180deg, #17466e 0%, #4b96c3 100%); color: white; padding: 80px 0 60px; }
        .course-card { border: none; border-radius: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); transition: all 0.3s; overflow: hidden; }
        .course-card:hover { transform: translateY(-8px); box-shadow: 0 12px 30px rgba(0,0,0,0.15); }
        .course-header { background: linear-gradient(180deg, #17466e 0%, #4b96c3 100%); color: white; padding: 30px; text-align: center; }
        .course-body { background: white; padding: 25px; }
        .level-badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; margin-bottom: 10px; }
        .level-beginner { background: #d4edda; color: #155724; }
        .level-intermediate { background: #fff3cd; color: #856404; }
        .level-advanced { background: #f8d7da; color: #721c24; }
        .course-info { display: flex; justify-content: space-between; align-items: center; margin: 15px 0; font-size: 0.9rem; }
        .course-info-item { color: #666; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/partials/bs-navbar.php'; ?>

    <div class="hero text-center">
        <div class="container">
            <h1 class="display-5 fw-bold mb-3">Centro de Capacitación</h1>
            <p class="lead">Cursos, webinars y materiales de formación</p>
        </div>
    </div>

    <div class="container py-5">
        <h2 class="mb-5" style="color: #17466e;">Cursos Disponibles</h2>

        <div class="row g-4 mb-5">
            <div class="col-lg-6">
                <div class="course-card h-100">
                    <div class="course-header">
                        <h5 class="mb-0">Introducción a MetaHogar</h5>
                    </div>
                    <div class="course-body">
                        <span class="level-badge level-beginner">Principiante</span>
                        <p class="card-text text-muted mt-3">Curso introductorio para nuevos usuarios. Aprende los conceptos básicos y cómo comenzar con tu sistema MetaHogar.</p>
                        <div class="course-info">
                            <span class="course-info-item">⏱️ 2 horas</span>
                            <span class="course-info-item">👥 542 estudiantes</span>
                        </div>
                        <a href="#" class="btn btn-sm" style="background: #17466e; color: white;">Acceder al Curso</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="course-card h-100">
                    <div class="course-header">
                        <h5 class="mb-0">Instalación y Configuración</h5>
                    </div>
                    <div class="course-body">
                        <span class="level-badge level-intermediate">Intermedio</span>
                        <p class="card-text text-muted mt-3">Guía completa para instalar y configurar todos los componentes de tu hogar inteligente MetaHogar.</p>
                        <div class="course-info">
                            <span class="course-info-item">⏱️ 4 horas</span>
                            <span class="course-info-item">👥 389 estudiantes</span>
                        </div>
                        <a href="#" class="btn btn-sm" style="background: #17466e; color: white;">Acceder al Curso</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="course-card h-100">
                    <div class="course-header">
                        <h5 class="mb-0">Automatización Avanzada</h5>
                    </div>
                    <div class="course-body">
                        <span class="level-badge level-advanced">Avanzado</span>
                        <p class="card-text text-muted mt-3">Crea automatizaciones complejas y personaliza tu hogar para tus necesidades específicas con MetaHogar.</p>
                        <div class="course-info">
                            <span class="course-info-item">⏱️ 6 horas</span>
                            <span class="course-info-item">👥 234 estudiantes</span>
                        </div>
                        <a href="#" class="btn btn-sm" style="background: #17466e; color: white;">Acceder al Curso</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="course-card h-100">
                    <div class="course-header">
                        <h5 class="mb-0">Seguridad y Privacidad</h5>
                    </div>
                    <div class="course-body">
                        <span class="level-badge level-intermediate">Intermedio</span>
                        <p class="card-text text-muted mt-3">Aprende cómo proteger tu hogar inteligente y garantizar la privacidad de tus datos personales.</p>
                        <div class="course-info">
                            <span class="course-info-item">⏱️ 3 horas</span>
                            <span class="course-info-item">👥 467 estudiantes</span>
                        </div>
                        <a href="#" class="btn btn-sm" style="background: #17466e; color: white;">Acceder al Curso</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="course-card h-100">
                    <div class="course-header">
                        <h5 class="mb-0">Ahorro de Energía</h5>
                    </div>
                    <div class="course-body">
                        <span class="level-badge level-beginner">Principiante</span>
                        <p class="card-text text-muted mt-3">Descubre cómo optimizar el consumo energético de tu hogar y ahorrar en tus facturas de servicios.</p>
                        <div class="course-info">
                            <span class="course-info-item">⏱️ 1.5 horas</span>
                            <span class="course-info-item">👥 612 estudiantes</span>
                        </div>
                        <a href="#" class="btn btn-sm" style="background: #17466e; color: white;">Acceder al Curso</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="course-card h-100">
                    <div class="course-header">
                        <h5 class="mb-0">Integraciones Externas</h5>
                    </div>
                    <div class="course-body">
                        <span class="level-badge level-advanced">Avanzado</span>
                        <p class="card-text text-muted mt-3">Integra MetaHogar con otros dispositivos y servicios como Alexa, Google Home, y más.</p>
                        <div class="course-info">
                            <span class="course-info-item">⏱️ 5 horas</span>
                            <span class="course-info-item">👥 298 estudiantes</span>
                        </div>
                        <a href="#" class="btn btn-sm" style="background: #17466e; color: white;">Acceder al Curso</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-lg-12">
                <h2 class="mb-4" style="color: #17466e;">Webinars Próximos</h2>
                <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <h6 style="color: #17466e; margin-bottom: 10px;">📅 Webinar: Futuro del Hogar Inteligente</h6>
                                <small class="text-muted">Próximo: 15 de Diciembre | 7:00 PM</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <h6 style="color: #17466e; margin-bottom: 10px;">🎙️ Panel: Expertos en Domótica</h6>
                                <small class="text-muted">Próximo: 22 de Diciembre | 6:00 PM</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <h6 style="color: #17466e; margin-bottom: 10px;">🛠️ Taller: Troubleshooting Avanzado</h6>
                                <small class="text-muted">Próximo: 29 de Diciembre | 5:00 PM</small>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <a href="#" class="btn" style="background: #17466e; color: white;">Ver Calendario Completo</a>
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
