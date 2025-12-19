<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/helpers/auth.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soporte 24/7 - MetaHogar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/bs-navbar.css">
    <style>
        body { background: #f5f7fb; font-family: 'Segoe UI', sans-serif; }
        .hero { background: linear-gradient(180deg, #17466e 0%, #4b96c3 100%); color: white; padding: 80px 0 60px; }
        .support-card { border: none; border-radius: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); transition: all 0.3s; background: white; }
        .support-card:hover { transform: translateY(-8px); box-shadow: 0 12px 30px rgba(0,0,0,0.15); }
        .support-icon { font-size: 2.5rem; margin-bottom: 15px; }
        .faq-item { background: white; border-radius: 12px; margin-bottom: 15px; border: 1px solid #e0e0e0; }
        .faq-question { padding: 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; color: #17466e; font-weight: 600; }
        .faq-answer { padding: 0 20px 20px; color: #666; display: none; }
        .faq-item.active .faq-answer { display: block; }
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
            <h1 class="display-5 fw-bold mb-3">Soporte 24/7</h1>
            <p class="lead">Estamos aquí para ayudarte en cualquier momento</p>
        </div>
    </div>

    <div class="container py-5">
        <h2 class="mb-5" style="color: #17466e;">Canales de Soporte</h2>

        <div class="row g-4 mb-5">
            <div class="col-md-6 col-lg-3">
                <div class="support-card p-4 text-center h-100">
                    <div class="support-icon">📞</div>
                    <h5 class="card-title mb-3">Teléfono</h5>
                    <p class="card-text text-muted mb-3">Llamadas de lunes a domingo</p>
                    <p class="fw-bold text-primary">(555) 123-4567</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="support-card p-4 text-center h-100">
                    <div class="support-icon">📧</div>
                    <h5 class="card-title mb-3">Email</h5>
                    <p class="card-text text-muted mb-3">Respuesta en 24 horas</p>
                    <p class="fw-bold text-primary">soporte@metahogar.mx</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="support-card p-4 text-center h-100">
                    <div class="support-icon">🎫</div>
                    <h5 class="card-title mb-3">Ticket de Soporte</h5>
                    <p class="card-text text-muted mb-3">Reporta un problema</p>
                    <a href="#" class="btn btn-sm" style="background: #17466e; color: white;">Crear Ticket</a>
                </div>
            </div>
        </div>

        <h2 class="mb-5" style="color: #17466e;">Preguntas Frecuentes (FAQ)</h2>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="faq-item active">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>¿Cuál es el horario de soporte?</span>
                        <span>➕</span>
                    </div>
                    <div class="faq-answer">
                        Nuestro equipo de soporte está disponible 24/7 (24 horas, 7 días a la semana). Puedes contactarnos a través de teléfono, email o chat en vivo en cualquier momento.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>¿Cómo reporto un problema técnico?</span>
                        <span>➕</span>
                    </div>
                    <div class="faq-answer">
                        Puedes reportar un problema de varias formas: llamando al teléfono de soporte, enviando un email, iniciando un chat en vivo o creando un ticket de soporte en nuestro portal. Elige el canal que prefieras.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>¿Cuánto tiempo tarda la respuesta?</span>
                        <span>➕</span>
                    </div>
                    <div class="faq-answer">
                        Las respuestas por email generalmente se envían en 24 horas. Para problemas críticos, llamar o usar chat en vivo garantiza una respuesta inmediata. Los tickets de soporte se revisan en orden de prioridad.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>¿Se incluye soporte técnico con mi compra?</span>
                        <span>➕</span>
                    </div>
                    <div class="faq-answer">
                        Sí, todos nuestros productos incluyen soporte técnico gratuito durante el primer año. Después de ese período, puedes renovar tu plan de soporte o continuar con soporte básico.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>¿Ofrecen soporte remoto?</span>
                        <span>➕</span>
                    </div>
                    <div class="faq-answer">
                        Sí, nuestro equipo puede acceder a tu sistema de forma remota (con tu consentimiento) para diagnosticar y resolver problemas técnicos de manera más eficiente.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
