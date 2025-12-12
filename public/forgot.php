<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña - MetaHogar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body class="bg-gradient-primary">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card p-4">
                    <h2 class="text-center text-primary mb-4">Recuperar Contraseña</h2>
                    <?php if (isset($_GET['sent'])): ?>
                        <div class="alert alert-success" role="alert">
                            Si la dirección de correo electrónico está registrada, te hemos enviado un enlace de recuperación.
                        </div>
                    <?php endif; ?>
                    
                    <!-- <form method="POST" action="../app/controllers/AuthController.php"></form> -->
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
