<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/helpers/auth.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comunidad MetaHogar - Q&A</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: #f5f7fb; font-family: 'Segoe UI', sans-serif; }
        .hero { background: linear-gradient(180deg, #17466e 0%, #4b96c3 100%); color: white; padding: 80px 0 60px; }
        .question-item { background: white; border: 1px solid #e0e0e0; border-radius: 12px; padding: 20px; margin-bottom: 15px; transition: all 0.3s; }
        .question-item:hover { box-shadow: 0 6px 18px rgba(0,0,0,0.1); transform: translateY(-2px); }
        .question-title { color: #17466e; font-weight: 600; font-size: 1.1rem; text-decoration: none; }
        .question-title:hover { color: #4b96c3; text-decoration: underline; }
        .stat-box { background: white; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .stat-number { color: #17466e; font-size: 2rem; font-weight: 700; }
        .tag { display: inline-block; background: #e8f1f7; color: #17466e; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; margin-right: 8px; margin-bottom: 8px; }
        .btn-ask { background: #17466e; color: white; padding: 12px 30px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-block; }
        .btn-ask:hover { background: #4b96c3; color: white; }
        .score-badge { background: #f5f7fb; border-radius: 8px; padding: 10px 15px; text-align: center; }
        .score-number { font-size: 1.5rem; font-weight: 700; color: #17466e; }
        .user-avatar { width: 40px; height: 40px; background: linear-gradient(135deg, #17466e 0%, #4b96c3 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.9rem; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/partials/bs-navbar.php'; ?>

    <div class="hero text-center">
        <div class="container">
            <h1 class="display-5 fw-bold mb-3">Comunidad MetaHogar</h1>
            <p class="lead">Preguntas y respuestas de la comunidad</p>
            <?php if (isLogged()): ?>
                <a href="#" class="btn-ask mt-3">+ Hacer una Pregunta</a>
            <?php else: ?>
                <p class="mt-3"><a href="login.php" style="color: white; text-decoration: underline;">Inicia sesión</a> para hacer preguntas</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-md-3">
                <div class="stat-box">
                    <div class="stat-number">342</div>
                    <div class="text-muted">Preguntas</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <div class="stat-number">1,243</div>
                    <div class="text-muted">Respuestas</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <div class="stat-number">856</div>
                    <div class="text-muted">Miembros</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <div class="stat-number">94%</div>
                    <div class="text-muted">Resueltas</div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-lg-8">
                <h3 class="mb-4" style="color: #17466e;">Preguntas Recientes</h3>

                <div class="question-item">
                    <div class="row align-items-start">
                        <div class="col-auto">
                            <div class="score-badge">
                                <div class="score-number">15</div>
                                <small class="text-muted">puntos</small>
                            </div>
                        </div>
                        <div class="col">
                            <a href="#" class="question-title">¿Cómo configurar el sistema de iluminación inteligente?</a>
                            <p class="text-muted small mb-2">Hace 2 horas • 3 respuestas</p>
                            <p class="text-muted">Acabo de instalar mi sistema MetaHogar y tengo problemas al configurar las luces inteligentes...</p>
                            <div>
                                <span class="tag">iluminación</span>
                                <span class="tag">configuración</span>
                                <span class="tag">hogar-inteligente</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="question-item">
                    <div class="row align-items-start">
                        <div class="col-auto">
                            <div class="score-badge">
                                <div class="score-number">8</div>
                                <small class="text-muted">puntos</small>
                            </div>
                        </div>
                        <div class="col">
                            <a href="#" class="question-title">¿Es compatible MetaHogar con Alexa?</a>
                            <p class="text-muted small mb-2">Hace 5 horas • 2 respuestas</p>
                            <p class="text-muted">¿Puedo integrar mi sistema MetaHogar con Amazon Alexa para control por voz?</p>
                            <div>
                                <span class="tag">integraciones</span>
                                <span class="tag">alexa</span>
                                <span class="tag">voz</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="question-item">
                    <div class="row align-items-start">
                        <div class="col-auto">
                            <div class="score-badge">
                                <div class="score-number">22</div>
                                <small class="text-muted">puntos</small>
                            </div>
                        </div>
                        <div class="col">
                            <a href="#" class="question-title">Reducción del consumo de energía con domótica</a>
                            <p class="text-muted small mb-2">Hace 1 día • 7 respuestas</p>
                            <p class="text-muted">¿Qué mejores prácticas recomiendas para reducir el consumo energético usando MetaHogar?</p>
                            <div>
                                <span class="tag">energía</span>
                                <span class="tag">ahorro</span>
                                <span class="tag">eficiencia</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="question-item">
                    <div class="row align-items-start">
                        <div class="col-auto">
                            <div class="score-badge">
                                <div class="score-number">5</div>
                                <small class="text-muted">puntos</small>
                            </div>
                        </div>
                        <div class="col">
                            <a href="#" class="question-title">Problemas de conexión WiFi con sensores</a>
                            <p class="text-muted small mb-2">Hace 2 días • 4 respuestas</p>
                            <p class="text-muted">Mis sensores de temperatura pierden conexión constantemente, ¿cuál es la solución?</p>
                            <div>
                                <span class="tag">wifi</span>
                                <span class="tag">sensores</span>
                                <span class="tag">conexión</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); margin-bottom: 20px;">
                    <h5 style="color: #17466e; margin-bottom: 15px;">Etiquetas Populares</h5>
                    <div>
                        <span class="tag"><strong>configuración</strong> (45)</span>
                        <span class="tag"><strong>seguridad</strong> (38)</span>
                        <span class="tag"><strong>wifi</strong> (31)</span>
                        <span class="tag"><strong>instalación</strong> (28)</span>
                        <span class="tag"><strong>energía</strong> (25)</span>
                        <span class="tag"><strong>app-móvil</strong> (22)</span>
                        <span class="tag"><strong>sensores</strong> (19)</span>
                        <span class="tag"><strong>automatización</strong> (18)</span>
                    </div>
                </div>

                <div style="background: #f0f7ff; border-radius: 12px; padding: 20px; border-left: 4px solid #17466e;">
                    <h6 style="color: #17466e; margin-bottom: 10px;">💡 Consejos de Comunidad</h6>
                    <ul class="small text-muted" style="list-style: none; padding: 0;">
                        <li>✓ Describe tu problema con claridad</li>
                        <li>✓ Incluye el modelo de tu dispositivo</li>
                        <li>✓ Busca antes de preguntar</li>
                        <li>✓ Responde a todos con respeto</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
