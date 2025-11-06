<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimonios - MetaHogar</title>
    
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
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-gradient-primary fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="login.php">
                <!-- <img src="images/LogoMeta.png" alt="MetaHogar" height="40" class="me-2"> -->
                <span class="fw-bold">MetaHogar</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sobre-nosotros.php">Sobre Nosotros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="servicios.php">Servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="productos.php">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="blog.php">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contacto.php">Contacto</a>
                    </li>
                </ul>
                
                <div class="d-flex">
                    <a href="login.php" class="btn btn-outline-light me-2">Iniciar Sesión</a>
                    <a href="signup.php" class="btn btn-warning">Registrarse</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-12 text-center">
                    <h1 class="display-4 fw-bold text-white mb-4">
                        <span class="text-warning">Testimonios</span>
                    </h1>
                    <p class="lead text-white-50 mb-4">
                        Lo que dicen nuestros clientes satisfechos
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4" id="testimonials-container">
                <!-- Los testimonios se cargarán dinámicamente -->
            </div>
            
            <div id="loading-testimonials" class="text-center py-5">
                <div class="loading-spinner"></div>
                <p class="mt-3 text-muted">Cargando testimonios...</p>
            </div>
        </div>
    </section>

    <!-- Add Testimonial Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-lg border-0 border-radius-custom">
                        <div class="card-body p-5">
                            <h3 class="fw-bold text-primary mb-4 text-center">
                                <i class="fas fa-star me-2"></i>Comparte tu Experiencia
                            </h3>
                            
                            <div id="testimonial-alert"></div>
                            
                            <form id="testimonialForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nombre" class="form-label fw-semibold">Nombre Completo</label>
                                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label fw-semibold">Correo Electrónico</label>
                                        <input type="email" id="email" name="email" class="form-control" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="calificacion" class="form-label fw-semibold">Calificación</label>
                                    <div class="rating-input">
                                        <input type="radio" id="star5" name="calificacion" value="5" required>
                                        <label for="star5" class="star">★</label>
                                        <input type="radio" id="star4" name="calificacion" value="4">
                                        <label for="star4" class="star">★</label>
                                        <input type="radio" id="star3" name="calificacion" value="3">
                                        <label for="star3" class="star">★</label>
                                        <input type="radio" id="star2" name="calificacion" value="2">
                                        <label for="star2" class="star">★</label>
                                        <input type="radio" id="star1" name="calificacion" value="1">
                                        <label for="star1" class="star">★</label>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="testimonio" class="form-label fw-semibold">Tu Testimonio</label>
                                    <textarea id="testimonio" name="testimonio" class="form-control" rows="5" 
                                              placeholder="Cuéntanos sobre tu experiencia con TechSolutions..." required></textarea>
                                </div>
                                
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-paper-plane me-2"></i>Enviar Testimonio
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="footer-brand">
                        <img src="images/TSLogo.png" alt="TechSolutions" height="40" class="mb-3">
                        <h5 class="text-white">TechSolutions</h5>
                        <p class="text-white-50">Tu aliado tecnológico de confianza desde 2014</p>
                        <div class="social-links">
                            <a href="https://www.facebook.com/share/16fYCfMcYm/" target="_blank" class="social-link">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-link">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="social-link">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="social-link">
                                <i class="fab fa-linkedin"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <h6 class="text-white mb-3">Servicios</h6>
                    <ul class="footer-links">
                        <li><a href="servicios.php">Mantenimiento</a></li>
                        <li><a href="servicios.php">Diagnóstico</a></li>
                        <li><a href="servicios.php">Armado de PC</a></li>
                        <li><a href="servicios.php">Soporte Técnico</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <h6 class="text-white mb-3">Empresa</h6>
                    <ul class="footer-links">
                        <li><a href="sobre-nosotros.php">Sobre Nosotros</a></li>
                        <li><a href="contacto.php">Contacto</a></li>
                        <li><a href="blog.php">Blog</a></li>
                        <li><a href="testimonios.php">Testimonios</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-4">
                    <h6 class="text-white mb-3">Contacto</h6>
                    <ul class="footer-contact">
                        <li>
                            <i class="fas fa-envelope me-2"></i>
                            <a href="mailto:techsolutionsindustries@gmail.com">techsolutionsindustries@gmail.com</a>
                        </li>
                        <li>
                            <i class="fas fa-phone me-2"></i>
                            <a href="tel:7352899793">735-289-9793</a>
                        </li>
                        <li>
                            <i class="fas fa-map-marker-alt me-2"></i>
                            <span>CDMX, México</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4 border-white-50">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-white-50 mb-0">© 2025 TechSolutions. Todos los derechos reservados.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-white-50 me-3">Política de Privacidad</a>
                    <a href="#" class="text-white-50">Términos de Servicio</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="js/main.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadTestimonials();
            setupTestimonialForm();
        });
        
        async function loadTestimonials() {
            try {
                const response = await fetch('php/testimonials/get_public.php');
                const data = await response.json();
                
                const container = document.getElementById('testimonials-container');
                const loading = document.getElementById('loading-testimonials');
                
                if (data.success && data.testimonios) {
                    displayTestimonials(data.testimonios);
                    loading.style.display = 'none';
                } else {
                    container.innerHTML = '<div class="col-12 text-center"><p>No hay testimonios disponibles</p></div>';
                    loading.style.display = 'none';
                }
            } catch (error) {
                console.error('Error cargando testimonios:', error);
                document.getElementById('loading-testimonials').style.display = 'none';
            }
        }
        
        function displayTestimonials(testimonios) {
            const container = document.getElementById('testimonials-container');
            container.innerHTML = '';
            
            testimonios.forEach(testimonio => {
                const stars = '★'.repeat(testimonio.calificacion) + '☆'.repeat(5 - testimonio.calificacion);
                
                const testimonialCard = document.createElement('div');
                testimonialCard.className = 'col-lg-4 col-md-6 mb-4';
                testimonialCard.innerHTML = `
                    <div class="testimonial-card h-100">
                        <div class="stars text-warning mb-3">${stars}</div>
                        <p class="testimonial-text">"${testimonio.testimonio}"</p>
                        <div class="testimonial-author">
                            <strong>${testimonio.nombre}</strong>
                            <div class="text-muted small">${new Date(testimonio.fecha_creacion).toLocaleDateString()}</div>
                        </div>
                    </div>
                `;
                
                container.appendChild(testimonialCard);
            });
        }
        
        function setupTestimonialForm() {
            document.getElementById('testimonialForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = new FormData(e.target);
                const submitBtn = e.target.querySelector('button[type="submit"]');
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enviando...';
                
                try {
                    const response = await fetch('php/testimonials/add.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        showTestimonialAlert('¡Gracias por tu testimonio! Será revisado y publicado pronto.', 'success');
                        e.target.reset();
                        // Reset rating
                        document.querySelectorAll('input[name="calificacion"]').forEach(input => input.checked = false);
                    } else {
                        showTestimonialAlert(data.message, 'danger');
                    }
                } catch (error) {
                    showTestimonialAlert('Error enviando testimonio. Intenta nuevamente.', 'danger');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Enviar Testimonio';
                }
            });
        }
        
        function showTestimonialAlert(message, type) {
            const alertContainer = document.getElementById('testimonial-alert');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            alertContainer.appendChild(alert);
            
            setTimeout(() => {
                alert.remove();
            }, 5000);
        }
    </script>
    
    <style>
        /* Paleta pastel azul */
        :root{
            --pastel-blue: #bfe9ff;
            --pastel-blue-600: #7ec9ff;
            --pastel-blue-700: #57b6ff;
            --text-on-pastel: #043a5b;
        }

        /* Sobrescribir colores "amarillos" de Bootstrap por azules pasteles */
        .text-warning {
            color: var(--pastel-blue-700) !important;
        }

        .btn-warning {
            background-color: var(--pastel-blue-600) !important;
            border-color: var(--pastel-blue-600) !important;
            color: var(--text-on-pastel) !important;
            box-shadow: none !important;
        }

        .btn-warning:hover, .btn-warning:focus {
            background-color: var(--pastel-blue-700) !important;
            border-color: var(--pastel-blue-700) !important;
            color: #fff !important;
        }

        /* Rating input stars: usar tonos pastel azules */
        .rating-input {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 5px;
        }
        
        .rating-input input[type="radio"] {
            display: none;
        }
        
        .rating-input .star {
            font-size: 2rem;
            color: #9bbfdc; /* color por defecto suave */
            cursor: pointer;
            transition: color 0.2s;
        }
        
        .rating-input input[type="radio"]:checked ~ .star,
        .rating-input .star:hover,
        .rating-input .star:hover ~ .star {
            color: var(--pastel-blue-700);
        }
        
        .testimonial-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary-color);
        }
        
        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
        }
        
        .testimonial-text {
            font-style: italic;
            margin-bottom: 1.5rem;
            color: #666;
            font-size: 1.1rem;
        }
        
        .testimonial-author {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .stars {
            font-size: 1.5rem;
        }
    </style>
</body>
</html>
