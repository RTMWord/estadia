<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Preserve possible error codes from previous login attempts
$error = isset($_GET['error']) ? intval($_GET['error']) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - MetaHogar</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS (fallback to local if exists) -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="css/style.css">
    
    <!-- Widget de Accesibilidad -->
    <script src="https://cdn.userway.org/widget.js" data-account="kjnkkEfZy1" defer></script>

    <style>
        /* Small inline adjustments to keep layout consistent if custom CSS is absent */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #eaf2ff 0%, #f7fbff 100%);
        }
        .border-radius-custom {
            border-radius: 12px;
        }
        .bg-gradient-primary {
            background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 100%);
        }
    </style>
</head>
<body class="bg-gradient-primary">
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="row w-100 justify-content-center">
            <div class="col-lg-4 col-md-6 col-sm-8">
                <div class="card shadow-lg border-0 border-radius-custom">
                    <div class="card-body p-5">
                        <!-- Logo y Título -->
                        <div class="text-center mb-4">
                            <img src="images/LogoMeta.png" alt="LogoMetaHogar" height="60" class="mb-3">
                            <h2 class="fw-bold text-primary">Iniciar Sesión</h2>
                            <p class="text-muted">Bienvenido a MetaHogar</p>
                        </div>

                        <!-- Alertas (from PHP login flow) -->
                        <?php if ($error === 1): ?>
                            <div class="alert alert-danger" role="alert">
                                Credenciales incorrectas.
                            </div>
                        <?php elseif ($error === 2): ?>
                            <div class="alert alert-warning" role="alert">
                                Tu cuenta está bloqueada temporalmente por intentos fallidos. Intenta más tarde.
                            </div>
                        <?php endif; ?>

                        <!-- Formulario -->
                        <form id="loginForm" method="POST" action="app/controllers/AuthController.php" novalidate>
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="fas fa-envelope me-2 text-primary"></i>Correo Electrónico
                                </label>
                                <input type="email" id="email" name="email" class="form-control" 
                                       placeholder="usuario@dominiocorreo.com" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="fas fa-lock me-2 text-primary"></i>Contraseña
                                </label>
                                <input type="password" id="password" name="password" class="form-control" 
                                       placeholder="Tu contraseña" required>
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">
                                        Recordarme
                                    </label>
                                </div>
                            </div>

                            <button type="submit" name="login" class="btn btn-primary w-100 py-3 fw-semibold">
                                <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                            </button>
                        </form>

                        <!-- Enlaces -->
                        <div class="text-center mt-4">
                            <p class="text-muted">
                                ¿No tienes cuenta? 
                                <a href="signup.php" class="text-primary fw-semibold text-decoration-none">
                                    Regístrate aquí
                                </a>
                            </p>
                            <a href="forgot.php" class="text-primary text-decoration-none small">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>

                        <!-- Esta sección es de pruebas y se muestran correos y contraseñas -->
                        <div class="mt-4 p-3 bg-light rounded">
                            <h6 class="fw-bold text-primary mb-2">
                                <i class="fas fa-key me-2"></i>Credenciales de cuentas prueba:
                            </h6>
                            <div class="small">
                                <p class="mb-1"><strong>Administrador:</strong> admin@metahogar.com / Adm1n+-*</p>
                                <p class="mb-1"><strong>Público:</strong> cliente@metahogar.com / contrasenia123</p>
                                <p class="mb-0"><strong>Prestadores de servicio:</strong> asociado@metahogar.com / soysocio123</p>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <a href="index.php" class="text-primary text-decoration-none">
                                <i class="fas fa-arrow-left me-2"></i>Volver al inicio
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS (if exists) -->
    <script src="js/main.js"></script>

    <script>
        // Basic client-side validation to improve UX before submitting to server
        document.getElementById('loginForm')?.addEventListener('submit', function(e) {
            const email = document.getElementById('email');
            const password = document.getElementById('password');

            if (!email.value.trim() || !password.value.trim()) {
                e.preventDefault();
                // show a simple bootstrap alert inside the form
                const alertContainer = document.createElement('div');
                alertContainer.className = 'alert alert-danger mt-3';
                alertContainer.role = 'alert';
                alertContainer.innerText = 'Por favor completa todos los campos.';
                // remove previous small alerts if any
                const existing = document.querySelector('.card-body .alert-inline-js');
                if (existing) existing.remove();
                alertContainer.classList.add('alert-inline-js');
                const cardBody = document.querySelector('.card-body');
                cardBody.insertBefore(alertContainer, cardBody.firstChild.nextSibling);
                return false;
            }
            return true;
        });
    </script>
</body>
</html>
