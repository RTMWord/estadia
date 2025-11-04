<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña - MetaHogar</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card p-4">
                    <h2 class="text-center text-primary mb-4">Recuperar Contraseña</h2>
                    <form method="POST" action="../app/controllers/AuthController.php">
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <button type="submit" name="forgot" class="btn btn-primary w-100">Enviar enlace de recuperación</button>
                    </form>
                    <div class="mt-3 text-center">
                        <a href="login.php">Volver al inicio de sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
