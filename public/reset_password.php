<?php
require_once '../app/config/db.php';
// Nota: Se asume que el archivo db.php es suficiente para la conexión PDO $pdo

$token = $_GET['token'] ?? null;
$error = $_GET['error'] ?? 0;

// Validar el token y su caducidad
$token_valido = false;

if ($token) {
    // Buscar el usuario por token
    $stmt = $pdo->prepare('SELECT idUsuario, PasswordResetExpires FROM Usuario WHERE PasswordResetToken = ? LIMIT 1');
    $stmt->execute([$token]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        // Verificar si el token no ha caducado
        if (strtotime($usuario['PasswordResetExpires']) > time()) {
            $token_valido = true;
        } else {
            $error = 2; // Token caducado
        }
    } else {
        $error = 2; // Token inválido o ya usado
    }
} else {
    $error = 3; // No se proporcionó token
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña - MetaHogar</title>
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
                            <h2 class="fw-bold" style="color: #0f4c81;">Restablecer Contraseña</h2>
                            <p class="text-muted">Ingresa y confirma tu nueva contraseña</p>
                        </div>

                        <?php if ($error == 1): ?>
                            <div class="alert alert-danger" role="alert">
                                Las contraseñas no coinciden o la nueva contraseña es muy corta (mínimo 6 caracteres).
                            </div>
                        <?php elseif ($error == 2): ?>
                            <div class="alert alert-danger" role="alert">
                                El enlace de restablecimiento es inválido o ha caducado. Por favor, solicita uno nuevo.
                            </div>
                        <?php endif; ?>

                        <?php if ($token_valido): ?>
                            <form method="POST" action="../app/controllers/AuthController.php">
                                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                                <input type="hidden" name="reset_password" value="1">
                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold">
                                        <i class="fas fa-lock me-2 text-primary"></i>Nueva Contraseña
                                    </label>
                                    <input type="password" class="form-control" id="password" name="password" required minlength="6">
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label fw-semibold">
                                        <i class="fas fa-lock me-2 text-primary"></i>Confirmar Contraseña
                                    </label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Restablecer Contraseña</button>
                            </form>
                        <?php else: ?>
                            <div class="mt-3 text-center">
                                <p>Si deseas restablecer tu contraseña, puedes volver a solicitar el enlace.</p>
                                <a href="forgot.php" class="btn btn-primary">Volver a solicitar</a>
                            </div>
                        <?php endif; ?>

                        <div class="mt-3 text-center">
                            <a href="login.php">Volver al inicio de sesión</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>