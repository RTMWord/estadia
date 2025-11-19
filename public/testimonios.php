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

    <!-- Carrusel de imágenes (opcional) -->
    <section class="py-4 bg-light">
        <div class="container">
            <div id="testimonials-carousel-wrapper" style="display:none">
                <div id="testimonialsCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner" id="carousel-inner">
                        <!-- Items dinámicos -->
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
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
                                    <!-- CSS radio-based star rating (accessible) -->
                                    <div id="full-stars-example">
                                        <div class="rating-group">
                                            <input class="rating__input rating__input--none" name="calificacion" id="rating-none" value="0" type="radio">
                                            <label aria-label="No rating" class="rating__label" for="rating-none"><i class="rating__icon rating__icon--none fa fa-ban"></i></label>
                                            <label aria-label="1 star" class="rating__label" for="rating-1"><i class="rating__icon rating__icon--star fa fa-star"></i></label>
                                            <input class="rating__input" name="calificacion" id="rating-1" value="1" type="radio">
                                            <label aria-label="2 stars" class="rating__label" for="rating-2"><i class="rating__icon rating__icon--star fa fa-star"></i></label>
                                            <input class="rating__input" name="calificacion" id="rating-2" value="2" type="radio">
                                            <label aria-label="3 stars" class="rating__label" for="rating-3"><i class="rating__icon rating__icon--star fa fa-star"></i></label>
                                            <input class="rating__input" name="calificacion" id="rating-3" value="3" type="radio">
                                            <label aria-label="4 stars" class="rating__label" for="rating-4"><i class="rating__icon rating__icon--star fa fa-star"></i></label>
                                            <input class="rating__input" name="calificacion" id="rating-4" value="4" type="radio">
                                            <label aria-label="5 stars" class="rating__label" for="rating-5"><i class="rating__icon rating__icon--star fa fa-star"></i></label>
                                            <input checked class="rating__input" name="calificacion" id="rating-5" value="5" type="radio">
                                        </div>
                                        <p class="desc" style="margin-bottom: 0.5rem; font-family: sans-serif; font-size:0.9rem">Selecciona las estrellas para puntuar</p>
                                    </div>
                                    <div class="form-text">Pulsa sobre una estrella para asignar la calificación. También puedes seleccionar "No rating".</div>
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
            loadCarousel();
            loadTestimonials();
            setupTestimonialForm();
        });
        
        async function loadCarousel() {
            try {
                const resp = await fetch('php/testimonials/get_carousel.php');
                const data = await resp.json();
                if (!data.success) return;
                const images = data.images || [];
                if (!images.length) return;

                const wrapper = document.getElementById('testimonials-carousel-wrapper');
                const inner = document.getElementById('carousel-inner');
                inner.innerHTML = '';

                images.forEach((img, idx) => {
                    const div = document.createElement('div');
                    div.className = 'carousel-item' + (idx === 0 ? ' active' : '');

                    // Normalize ruta: remove leading ../ if present
                    let ruta = (img.ruta || '').toString();
                    ruta = ruta.replace(/^\.\.\//, '');

                    // Create image element so we can handle load/error
                    const imgEl = document.createElement('img');
                    imgEl.className = 'd-block w-100';
                    imgEl.style.maxHeight = '420px';
                    imgEl.style.objectFit = 'cover';
                    imgEl.alt = img.descripcion || '';
                    imgEl.src = ruta;
                    imgEl.onerror = function(e) {
                        console.error('Error loading carousel image:', ruta, e);
                        // show a subtle placeholder (1x1 svg) to avoid broken-icon
                        imgEl.src = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="600" height="300"><rect width="100%" height="100%" fill="%23e9ecef"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="%23777" font-family="Arial, sans-serif" font-size="20">Imagen no disponible</text></svg>';
                    };

                    const caption = document.createElement('div');
                    caption.className = 'carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-2';
                    caption.innerHTML = `<p class="mb-0">${img.descripcion || ''}</p>`;

                    div.appendChild(imgEl);
                    div.appendChild(caption);
                    inner.appendChild(div);
                });

                wrapper.style.display = 'block';
            } catch (e) {
                console.error('Error cargando carousel:', e);
            }
        }

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
                const nombre = testimonio.nombre || testimonio.Nombre || 'Usuario';
                const cal = Number(testimonio.calificacion || testimonio.Calificacion || 0);
                const stars = '★'.repeat(Math.max(0, Math.min(5, cal))) + '☆'.repeat(Math.max(0, 5 - Math.max(0, Math.min(5, cal))));

                const dateRaw = testimonio.fecha_creacion || testimonio.FechaCreacion || '';
                let dateText = '';
                if (dateRaw) {
                    // Try to normalize MySQL DATETIME to ISO
                    const iso = dateRaw.replace(' ', 'T');
                    const d = new Date(iso);
                    if (!isNaN(d)) dateText = d.toLocaleDateString();
                }

                const testimonialCard = document.createElement('div');
                testimonialCard.className = 'col-lg-4 col-md-6 mb-4';
                const initials = (nombre.split(' ').map(n=>n[0]).slice(0,2).join('') || 'U').toUpperCase();
                testimonialCard.innerHTML = `
                    <div class="testimonial-card h-100">
                        <div class="d-flex align-items-start mb-3 gap-3">
                            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:56px;height:56px;font-weight:700;">${initials}</div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong class="testimonial-author">${escapeHtml(nombre)}</strong>
                                        <div class="text-muted small">${escapeHtml(dateText)}</div>
                                    </div>
                                    <div class="stars text-warning">${stars}</div>
                                </div>
                            </div>
                        </div>
                        <p class="testimonial-text">“${escapeHtml(testimonio.testimonio || testimonio.Testimonio || '')}”</p>
                    </div>
                `;

                container.appendChild(testimonialCard);
            });
        }

        // Small helper to escape HTML when inserting text
        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>\"'`]/g, function (s) {
                return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','`':'&#96;'})[s];
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
                        // Reset rating to default (5 stars)
                        const def = document.getElementById('rating-5'); if (def) def.checked = true;
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
    
    <!-- testimonios styles moved to public/assets/css/testimonios.css -->
</body>
</html>
