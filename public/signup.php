<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - MetaHogar</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    
    <!-- Widget de Accesibilidad -->
    <script src="https://cdn.userway.org/widget.js" data-account="kjnkkEfZy1"></script>
</head>
<body class="bg-gradient-primary">
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="row w-100 justify-content-center">
            <div class="col-lg-5 col-md-7 col-sm-9">
                <div class="card shadow-lg border-0 border-radius-custom">
                    <div class="card-body p-5">
                        <!-- Logo y Título -->
                        <div class="text-center mb-4">
                            <img src="assets/css/images/LogoMeta.png" alt="MetaHogar" height="145" class="mb-3">
                            <h2 class="fw-bold text-primary">Crear Cuenta</h2>
                            <p class="text-muted">Crea una cuenta para formar parte de nuestra comunidad</p>
                        </div>

                        <!-- Alertas -->
                        <div id="alertContainer"></div>

                        <!-- Formulario -->
                        <form id="registerForm">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="nombres" class="form-label fw-semibold">
                                        <i class="fas fa-user me-2 text-primary"></i>Nombres
                                    </label>
                                    <input type="text" id="nombres" name="nombres" class="form-control" 
                                           placeholder="Nombre/s" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="apellido_paterno" class="form-label fw-semibold">
                                        <i class="text-primary"></i>Apellido Paterno   <!-- Aquí no se en que me quede -->
                                    </label>
                                    <input type="text" id="apellido_paterno" name="apellido_paterno" class="form-control" 
                                           placeholder="Apellido paterno" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="apellido_materno" class="form-label fw-semibold">
                                        <i class="forced-color-adjust-none text-primary"></i>Apellido Materno <!-- Aquí no se en que me quede -->
                                    </label>
                                    <input type="text" id="apellido_materno" name="apellido_materno" class="form-control" 
                                           placeholder="Apellido materno" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-semibold">
                                        <i class="fas fa-envelope me-2 text-primary"></i>Correo Electrónico
                                    </label>
                                    <input type="email" id="email" name="email" class="form-control" 
                                           placeholder="usuario@dominiocorreo.com" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="telefono" class="form-label fw-semibold">
                                        <i class="fas fa-phone me-2 text-primary"></i>Teléfono
                                    </label>
                                    <input type="tel" id="telefono" name="telefono" class="form-control" 
                                           placeholder="777-000-1122">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="tipo_usuario" class="form-label fw-semibold">
                                    <i class="fas fa-user-tag me-2 text-primary"></i>Tipo de Usuario
                                </label>
                                <select id="tipo_usuario" name="tipo_usuario" class="form-select" required>
                                    <option value="">Selecciona el tipo de usuario</option>
                                    <option value="cliente">Cliente</option>
                                    <option value="administrador">Administrador</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label fw-semibold">
                                        <i class="fas fa-lock me-2 text-primary"></i>Contraseña
                                    </label>
                                    <input type="password" id="password" name="password" class="form-control" 
                                           placeholder="Mínimo 6 caracteres" required minlength="6">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label fw-semibold">
                                        <i class="fas fa-lock me-2 text-primary"></i>Confirmar Contraseña
                                    </label>
                                    <input type="password" id="confirm_password" name="confirm_password" 
                                           class="form-control" placeholder="Repita su contraseña" required minlength="6">
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" required>
                                    <label class="form-check-label" for="terms">
                                      
                                        Estoy de acuerdo en crear una cuenta en MetaHogar
                                        <!--Acepto los <a href="#" class="text-primary">términos y condiciones</a> --> <!-- Aquí solamente es cuestión de que preguntemos si va a haber politica de privacidad y los términos y condiciones -->
                                        <!--y la  <a href="#" class="text-primary">política de privacidad</a> -->
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 fw-semibold">
                                <i class="fa-regular fa-user-circle-plus"></i>Crear Cuenta
                            </button>
                        </form>

                        <!-- Enlaces -->
                        <div class="text-center mt-4">
                            <p class="text-muted">
                                ¿Ya tienes cuenta? 
                                <a href="login.php" class="text-primary fw-semibold text-decoration-none">
                                    Inicia sesión aquí
                                </a>
                            </p>
                        </div>

                        <!-- Beneficios 
                        <div class="mt-4 p-3 bg-light rounded">
                            <h6 class="fw-bold text-primary mb-2">
                                <i class="fas fa-gift me-2"></i>Beneficios de registrarte:
                            </h6>
                            <ul class="list-unstyled mb-0 small">
                                <li><i class="fas fa-check text-success me-2"></i>Diagnóstico remoto gratuito</li>
                                <li><i class="fas fa-check text-success me-2"></i>Soporte técnico 24/7</li>
                                <li><i class="fas fa-check text-success me-2"></i>Descuentos exclusivos para miembros</li>
                                <li><i class="fas fa-check text-success me-2"></i>Acceso prioritario a nuevos productos</li>
                            </ul>
                        </div>
                        -->

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
    <!-- Custom JS -->
    <script src="js/main.js"></script>
</body>
</html>
