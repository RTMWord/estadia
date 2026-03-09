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
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Shared auth styles (login/signup) -->
    <link rel="stylesheet" href="assets/css/auth.css">
    
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
                        <form id="registerForm" method="post" action="php/register.php">
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

                            <!-- Tipo de usuario eliminado: todos los registros se asignan automáticamente al rol 'Usuario' -->

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

                        <!-- Mensajes -->
                        <?php if (!empty($_GET['registered'])): ?>
                            <div class="alert alert-success mt-3">Registro exitoso. Ahora puedes iniciar sesión.</div>
                        <?php elseif (!empty($_GET['error'])): ?>
                            <div class="alert alert-danger mt-3"><?= htmlspecialchars($_GET['error']) ?></div>
                        <?php endif; ?>

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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom JS -->
    <script src="js/main.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            const nombresInput = document.getElementById('nombres');
            const apellidoPInput = document.getElementById('apellido_paterno');
            const apellidoMInput = document.getElementById('apellido_materno');
            const emailInput = document.getElementById('email');
            const telefonoInput = document.getElementById('telefono');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            
            // Función para validar que solo contiene letras y espacios
            function onlyLetters(e) {
                const input = e.target;
                // Permitir solo letras (a-z, A-Z) y espacios, incluyendo acentos
                input.value = input.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
            }
            
            // Función para validar formato de teléfono (solo números, espacios, guiones, paréntesis y +)
            function onlyPhoneChars(e) {
                const input = e.target;
                // Permitir solo números, espacios, guiones, paréntesis y el símbolo +
                input.value = input.value.replace(/[^0-9\s\-()+]/g, '');
            }
            
            // Función para mostrar validación en tiempo real
            function validateField(input, pattern, message) {
                if (input.value && !pattern.test(input.value)) {
                    input.classList.add('is-invalid');
                    input.classList.remove('is-valid');
                } else if (input.value) {
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                } else {
                    input.classList.remove('is-invalid', 'is-valid');
                }
            }
            
            // Aplicar validaciones en tiempo real
            nombresInput.addEventListener('input', onlyLetters);
            apellidoPInput.addEventListener('input', onlyLetters);
            apellidoMInput.addEventListener('input', onlyLetters);
            telefonoInput.addEventListener('input', onlyPhoneChars);
            
            // Validación de email
            emailInput.addEventListener('blur', function() {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                validateField(this, emailPattern);
            });
            
            // Validación de contraseña (mínimo 6 caracteres)
            passwordInput.addEventListener('blur', function() {
                if (this.value && this.value.length < 6) {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                } else if (this.value) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            });
            
            // Validación de confirmación de contraseña
            confirmPasswordInput.addEventListener('blur', function() {
                if (this.value !== passwordInput.value) {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                } else if (this.value) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            });
            
            // Manejar envío del formulario
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Validaciones finales
                const nombres = nombresInput.value.trim();
                const apellidoP = apellidoPInput.value.trim();
                const apellidoM = apellidoMInput.value.trim();
                const email = emailInput.value.trim();
                const telefono = telefonoInput.value.trim();
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;
                const termsChecked = document.getElementById('terms').checked;
                
                // Validar que todos los campos sean válidos
                if (!nombres || !apellidoP || !apellidoM || !email || !password || !confirmPassword) {
                    Swal.fire('Campo vacío', 'Por favor completa todos los campos obligatorios', 'warning');
                    return;
                }
                
                if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(nombres)) {
                    Swal.fire('Nombres inválidos', 'Los nombres no pueden contener números ni signos especiales', 'error');
                    return;
                }
                
                if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(apellidoP)) {
                    Swal.fire('Apellido paterno inválido', 'El apellido no puede contener números ni signos especiales', 'error');
                    return;
                }
                
                if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(apellidoM)) {
                    Swal.fire('Apellido materno inválido', 'El apellido no puede contener números ni signos especiales', 'error');
                    return;
                }
                
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    Swal.fire('Email inválido', 'Por favor ingresa un correo válido (ej: usuario@dominio.com)', 'error');
                    return;
                }
                
                // Validar teléfono si se proporcionó
                if (telefono && !/^[0-9\s\-()+]+$/.test(telefono)) {
                    Swal.fire('Teléfono inválido', 'El teléfono solo puede contener números, espacios, guiones, paréntesis o el símbolo +', 'error');
                    return;
                }
                
                // Validar que el teléfono tenga al menos 10 dígitos (sin contar caracteres de formato)
                if (telefono) {
                    const soloNumeros = telefono.replace(/[^0-9]/g, '');
                    if (soloNumeros.length < 10) {
                        Swal.fire('Teléfono inválido', 'El teléfono debe tener al menos 10 dígitos', 'error');
                        return;
                    }
                }
                
                if (password.length < 6) {
                    Swal.fire('Contraseña débil', 'La contraseña debe tener mínimo 6 caracteres', 'error');
                    return;
                }
                
                if (password !== confirmPassword) {
                    Swal.fire('Contraseñas no coinciden', 'Las contraseñas no son iguales. Intenta de nuevo', 'error');
                    return;
                }
                
                if (!termsChecked) {
                    Swal.fire('Términos no aceptados', 'Debes aceptar los términos para continuar', 'warning');
                    return;
                }
                
                // Mostrar loader mientras se procesa
                Swal.fire({
                    title: 'Registrando cuenta',
                    html: 'Por favor espera...',
                    icon: 'info',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Enviar formulario
                try {
                    const formData = new FormData(form);
                    const response = await fetch('php/register.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        Swal.fire({
                            title: '¡Registro exitoso!',
                            html: result.message,
                            icon: 'success',
                            confirmButtonText: 'Entendido',
                            allowOutsideClick: false
                        }).then(() => {
                            window.location.href = 'login.php';
                        });
                    } else {
                        Swal.fire('Error en el registro', result.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error', 'Hubo un problema al procesar tu solicitud. Intenta de nuevo.', 'error');
                    console.error('Error:', error);
                }
            });
        });
    </script>
</body>
</html>
