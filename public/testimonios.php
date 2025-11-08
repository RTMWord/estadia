<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimonios - MetaHogar</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS (match index.php) -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/bs-navbar.css">
    <link rel="stylesheet" href="assets/css/testimonios.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Widget de Accesibilidad -->
    <script src="https://cdn.userway.org/widget.js" data-account="kjnkkEfZy1"></script>
</head>
<body>
    <!-- Navigation -->
    <?php include __DIR__ . '/partials/bs-navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section py-6 bg-hero">
        <div class="container">
            <div class="row align-items-center" style="min-height:40vh">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-5 fw-bold text-white mb-3">
                        <span class="text-warning">Testimonios</span>
                    </h1>
                    <p class="lead text-white-75 mb-4">
                        Historias reales de clientes que confiaron en MetaHogar. Lee sus experiencias y comparte la tuya.
                    </p>
                    <a href="#add-testimonial" class="btn btn-light btn-lg">Compartir mi experiencia</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-5">
        <div class="container">
            <div class="row gy-4" id="testimonials-container" aria-live="polite">
                <!-- Los testimonios se cargarán dinámicamente -->
            </div>

            <div id="loading-testimonials" class="text-center py-5">
                <div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
                <p class="mt-3 text-muted">Cargando testimonios...</p>
            </div>
        </div>
    </section>

    <!-- Add Testimonial Section -->
    <section id="add-testimonial" class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-lg border-0 border-radius-custom">
                        <div class="card-body p-5">
                            <h3 class="fw-bold text-primary mb-4 text-center">
                                <i class="fas fa-star me-2"></i>Comparte tu Experiencia
                            </h3>
                            
                            <div id="testimonial-alert"></div>
                            
                            <form id="testimonialForm" novalidate>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nombre" class="form-label fw-semibold">Nombre Completo</label>
                                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label fw-semibold">Correo Electrónico</label>
                                        <input type="email" id="email" name="email" class="form-control" placeholder="(opcional)" aria-describedby="emailHelp">
                                        <div id="emailHelp" class="form-text">No publicaremos tu correo.</div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="calificacion" class="form-label fw-semibold">Calificación</label>
                                    <div class="rating-input" role="radiogroup" aria-label="Calificación">
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
                                              placeholder="Cuéntanos sobre tu experiencia con MetaHogar..." required></textarea>
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
                        <img src="images/LogoMeta.png" alt="MetaHogar" height="40" class="mb-3">
                        <h5 class="text-white">MetaHogar</h5>
                        <p class="text-white-50">Soluciones para el hogar y servicios de confianza</p>
                        <div class="social-links">
                            <a href="https://www.facebook.com/MetaHogar" target="_blank" class="social-link">
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
                            <a href="mailto:contacto@metahogar.com">contacto@metahogar.com</a>
                        </li>
                        <li>
                            <i class="fas fa-phone me-2"></i>
                            <a href="tel:+525552223333">55 5222 3333</a>
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
    <!-- Navbar sticky script (same as index.php) -->
    <script src="assets/js/navbar-sticky.js"></script>
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
                
                if (data.success && data.testimonios && data.testimonios.length) {
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
                const initials = (testimonio.nombre || 'Usuario').split(' ').map(n=>n[0]).slice(0,2).join('').toUpperCase();
                testimonialCard.innerHTML = `
                    <div class="testimonial-card h-100">
                        <div class="d-flex align-items-start mb-3 gap-3">
                            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:56px;height:56px;font-weight:700;">${initials}</div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong class="testimonial-author">${testimonio.nombre}</strong>
                                        <div class="text-muted small">${new Date(testimonio.fecha_creacion).toLocaleDateString()}</div>
                                    </div>
                                    <div class="stars text-warning">${stars}</div>
                                </div>
                            </div>
                        </div>
                        <p class="testimonial-text">“${testimonio.testimonio}”</p>
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
                        showTestimonialAlert('¡Gracias! Tu testimonio se envió y será revisado pronto.', 'success');
                        e.target.reset();
                        // Reset rating
                        document.querySelectorAll('input[name="calificacion"]').forEach(input => input.checked = false);
                        // reload previews after a short delay so admin may approve
                        setTimeout(() => loadTestimonials(), 1200);
                    } else {
                        showTestimonialAlert(data.message || 'Error al enviar. Intenta nuevamente.', 'danger');
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
    
    <!-- testimonios styles moved to assets/css/testimonios.css -->
</body>
</html>
