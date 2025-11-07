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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        /* FONDO AZUL DEGRADADO (Mantenido de tu login.php original) */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f4c81 0%, #031526 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            margin: 0;
        }
        
        /* AJUSTES DE DISEÑO (Tomado de loginchido) */
        .card {
            border-radius: 12px; /* border-radius-custom */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); /* Sombra fuerte y moderna */
            border: none;
        }

        /* Usamos el fondo blanco normal para la tarjeta, no el degradado ligero de loginchido */
        .card-body {
            background-color: #ffffff; 
        }

        .btn-primary {
            background-color: #175e8d; /* Color primario de MetaHogar (ajustado para ser legible) */
            border-color: #175e8d;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #0f4c81;
            border-color: #0f4c81;
        }

    </style>
</head>
<body>
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="row w-100 justify-content-center">
            <div class="col-lg-4 col-md-6 col-sm-8">
                <div class="card">
                    <div class="card-body p-5">
                        
                        <div class="text-center mb-4">
                            <img src="assets/css/images/LogoMeta.png" alt="Logo MetaHogar" style="height: 140px; margin-bottom: 1rem;" class="mb-3">
                            <h2 class="fw-bold" style="color: #0f4c81;">Iniciar Sesión</h2>
                            <p class="text-muted">Bienvenido a MetaHogar</p>
                        </div>

                        <?php if ($error === 1): ?>
                            <div class="alert alert-danger" role="alert">
                                Credenciales incorrectas.
                            </div>
                        <?php elseif ($error === 2): ?>
                            <div class="alert alert-warning" role="alert">
                                Tu cuenta está bloqueada temporalmente por intentos fallidos. Intenta más tarde.
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_GET['reset'])): ?>
                            <div class="alert alert-success" role="alert">
                                ¡Contraseña restablecida con éxito! Ya puedes iniciar sesión.
                            </div>
                        <?php endif; ?>

                        <?php $next = isset($_GET['next']) ? htmlspecialchars($_GET['next']) : ''; ?>
                        <form method="POST" action="../app/controllers/AuthController.php" novalidate>
                             <input type="hidden" name="next" value="<?= $next ?>">
                            
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

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                    <label class="form-check-label small" for="remember">
                                        Recordarme
                                    </label>
                                </div>
                                <a href="forgot.php" class="text-primary small text-decoration-none fw-semibold">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            </div>

                            <button type="submit" name="login" class="btn btn-primary w-100 py-3 fw-semibold">
                                <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <p class="text-muted mb-0">
                                ¿No tienes cuenta? 
                                <a href="signup.php" class="text-primary fw-semibold text-decoration-none">
                                    Regístrate aquí
                                </a>
                            </p>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>