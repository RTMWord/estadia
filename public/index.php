<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/partials/bs-navbar.php';


// Contador de visitas: archivo visit_counter.php en la misma carpeta public/
// (asegúrate de crear visit_counter.php tal como se muestra abajo)
require_once __DIR__ . '/visit_counter.php';

$user = null;
if (isLogged()) {
    $stmt = $pdo->prepare('SELECT Nombre, ApellidoP FROM Usuario WHERE idUsuario = ? LIMIT 1');
    $stmt->execute([getUserId()]);
    $user = $stmt->fetch();
}

// Determine if current user is admin to show admin button in UI
$showAdminButton = false;
if (isLogged()) {
    $stmt = $pdo->prepare('SELECT r.Nombre FROM UsuarioRol ur JOIN Rol r ON ur.Rol_idRol = r.idRol WHERE ur.Usuario_idUsuario = ? LIMIT 1');
    $stmt->execute([getUserId()]);
    $rol = $stmt->fetchColumn();
    if ($rol === 'administrador') {
        $showAdminButton = true;
    }
}

// Incrementa el contador al cargar la página y obtiene el total actual
// Si prefieres NO incrementar en cada recarga (por sesión o IP), dímelo y lo cambio.
$visits = increment_and_get_visits();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MetaHogar - Longevitud Segura</title>
    <!-- Load Bootstrap first, then custom CSS to ensure overrides apply -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/bs-navbar.css">
    <!-- Page-specific CSS (moved from inline <style>) -->
    <link rel="stylesheet" href="assets/css/index.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MetaHogar - Vivir más, Vivir mejor</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    

    <!-- moved inline styles to assets/css/index.css -->
    <script src="https://cdn.userway.org/widget.js" data-account="kjnkkEfZy1"></script>
</head>
<body>
    <?php include __DIR__ . '/partials/bs-navbar.php'; ?>
    
    <div class="position-absolute top-0.5 end-0 p-0.5" style="z-index: 9999;">
        <div class="top-controls">
            <div class="visits-box">
                N° Visitas: <span id="visits-count"><?= htmlspecialchars($visits) ?></span>
            </div>
            <?php if (!empty($showAdminButton)): ?>
                <div style="margin-top:6px; text-align:right;">
                    <a href="admin/index.php" class="btn btn-sm btn-outline-light">Panel Admin</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            
            <div class="col-lg-7 text-center text-lg-start">
                <h2 class="hero-title text-white">
                    Diseñamos hogares para que tus adultos mayores vivan una longevidad segura e independiente en el hogar que atesoran.
                </h2>
                <p class="lead text-white-75 mb-4">
                    Tecnología que transforma tu hogar, seguridad que transforma tu vida.
                </p>
                <div class="d-flex justify-content-center justify-content-lg-start mt-4 gap-3">
                    <a href="#nosotros" class="btn btn-lg btn-custom-blue">
                        Leer Más
                    </a>
                    <a href="#contacto" class="btn btn-lg btn-custom-outline">
                        Contáctanos
                    </a>
                </div>
            </div>

            <div class="col-lg-5 d-none d-lg-block">
                <div class="hero-image-container">
                    <div class="hero-image-wrapper">
                        <div class="blob-shape-light"></div>
                        <div class="blob-shape-dark"></div>
                        <div class="blob-brush"></div>
                        
                        <img src="assets/css/images/hero.jpg" class="img-fluid" alt="Familia multigeneracional usando tablet">         
                    </div>
                </div>
            </div>
        </div>
    </div>

    </section>

    <main class="container my-5">
        <section id="nosotros" class="mb-5">
            <h2 class="text-primary">Nosotros</h2>

            <div class="row align-items-center">
            
            <div class="col-md-6 order-md-1">
                <h3>MetaHogar</h3>
                <p class="fs-4">Vivir más, Vivir mejor.</p>
                <p>Es una empresa dedicada a brindar soluciones tecnológicas inteligentes para el hogar, con un enfoque especial en los adultos mayores. Nuestro objetivo principal es mejorar la seguridad, la comodidad y la eficiencia en la vida diaria de esta población. Nos enfocamos en transformar los hogares en espacios inteligentes, donde la tecnología se integra de manera intuitiva y fácil de usar, especialmente diseñada para satisfacer las necesidades y preferencias de los adultos mayores.</p>
                <a href="#servicios" class="btn btn-lg btn-custom-blue">Leer Más</a>
            </div>

            <div class="col-md-6 order-md-2 text-center mt-4 mt-md-0">
                <img src="assets/css/images/LogoMeta.png" class="img-fluid rounded shadow-lg" alt="Hogar inteligente para adultos mayores" style="max-height: 400px; object-fit: cover;">
                </div>
        </div>

            </section>

        <section id="servicios" class="mb-5 services-container">
    <div class="container">
        <h2 class="text-primary text-center">Servicios</h2>
        <h4 class="text-center mb-5">Asesoría</h4>
        
        <p class="text-center lead mb-5 px-lg-5">
            MetaHogar es una iniciativa vinculada con la Asociación Americana de Personas Retiradas (AARP) y con la Asociación Civil Desarrollo Aplicativo para la Movilidad (DAM A.C.) y nuestro personal está capacitado para ayudarle a que usted tome las decisiones correctas en el momento adecuado si es que ha decidido brindarle a sus adultos mayores una vivienda funcional, confortable y segura para una longevidad digna.
        </p>

        <div class="row g-4 justify-content-center">
            
            <div class="col-lg-4 col-md-6">
                <div class="service-step-card">
                    <div class="service-icon-circle">
                        <i class="fas fa-file-alt"></i> 
                    </div>
                    <h4>Proceso</h4>
                    <p><strong>Valoración y diagnóstico</strong></p>
                    <p>
                        Realizamos un análisis integral del hogar a intervenir para identificar los puntos críticos que pudieran representar un eventual riesgo que vulnere la integridad física de sus queridos adultos mayores. Elaboramos una matriz de riesgos para calcular la posibilidad de que ocurra un accidente contra el impacto que pueda tener en el adulto mayor.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="service-step-card">
                    <div class="service-icon-circle">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h4>Propuesta técnica y económica</h4>
                    <p>
                        Teniendo ese resultado se proponen una serie de adaptaciones físicas y soluciones tecnológicas (específicas o complementarias) que convertirán su hogar en una vivienda segura, funcional y confortable para una longevidad digna. Tanto las adaptaciones físicas como las soluciones tecnológicas se orientan a lograr funcionalidad y confort, pero también brindan independencia a los adultos mayores así como a los familiares cercanos al poder tener monitoreo remoto de distintas actividades.
                    </p>
                    <p><strong>Trabajamos sobre 4 ejes:</strong></p>
                    <ul>
                        <li>Riesgo</li>
                        <li>Funcionalidad</li>
                        <li>Confort</li>
                        <li>Independencia / Monitoreo / Tranquilidad.</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="service-step-card">
                    <div class="service-icon-circle">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h4>Ejecución</h4>
                    <p>
                        Nuestra organización está integrada por áreas como:
                    </p>
                    <ul>
                        <li>Arquitectura y diseño</li>
                        <li>Mecatrónica</li>
                        <li>Electrónica y telecomunicaciones</li>
                        <li>Electricidad y energías renovables</li>
                    </ul>
                    <p>
                        quienes, junto con sus equipos de trabajo, realizan adaptaciones e instalaciones como si fueran artistas y además bajo norma. Al concluir el proyecto, el cliente se convierte en un miembro de la comunidad MetaHogar y recibe un manual de uso y operación de todos los dispositivos electrónicos instalados, así como la debida capacitación al adulto mayor y a los familiares.
                    </p>
                </div>
            </div>
            
        </div>
    </div>
</section>
        <section id="infraestructura" class="mb-5">
            <h2 class="text-primary">Soluciones de Infraestructura</h2>
            <ul>
                <li><strong>Rampas:</strong> Facilitan movilidad y reducen riesgos de caídas.</li>
                <li><strong>Pasamanos y barandillas:</strong> Obligados en rampas, escaleras y baños.</li>
                <li><strong>Gabinetes, barra y fregadero:</strong> Alturas adaptadas para confort e independencia.</li>
                <li><strong>WC, Regadera y lavamanos:</strong> Adaptaciones para seguridad y confort.</li>
                <li><strong>Pasillos y puertas:</strong> Espacios amplios para mejor movilidad.</li>
                <li><strong>Apagadores y enchufes:</strong> A la vista y altura correcta.</li>
                <li><strong>Escalera para alberca:</strong> Diseñada para adultos mayores.</li>
            </ul>
        </section>
        <section id="tecnologia" class="mb-5">
            <h2 class="text-primary">Soluciones Tecnológicas</h2>
            <ul>
                <li><strong>Asistentes de voz:</strong> Amazon Echo, Google Home para control por voz.</li>
                <li><strong>Iluminación:</strong> Sensores para encendido/ajuste automático.</li>
                <li><strong>Detector de caídas VAYYAR CARE:</strong> Sensores 3D para monitoreo y alertas.</li>
                <li><strong>Monitoreo de salud y medicación:</strong> Pulseras inteligentes, recordatorios y alertas.</li>
            </ul>
        </section>
        <section id="objetivos" class="mb-5">
            <h2 class="text-primary">Objetivos</h2>
            <ul>
                <li><strong>Promover:</strong> Investigación y desarrollo de nuevas tecnologías para adultos mayores.</li>
                <li><strong>Reducir:</strong> Accidentes y lesiones mediante sistemas de seguridad y automatización.</li>
            </ul>
            <h4>Apoyamos Causas Sociales</h4>
            <p>Colaboramos con ONG's para mejorar la calidad de vida de adultos mayores en situación de desventaja.</p>
        </section>
        <section id="contacto" class="mb-5">
            <h2 class="text-primary">Contáctanos</h2>
            <p>¡DESCUBRE COMO, JUNTOS, PODEMOS HACER LA DIFERENCIA EN TU HOGAR. ...PARA UNA LONGEVIDAD MÁS SEGURA Y CONFORTABLE!!!!</p>
            <p>Uno de nuestros agentes se comunicará contigo para agendar una cita.</p>
            <form class="row g-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" placeholder="Nombre" required>
                </div>
                <div class="col-md-6">
                    <input type="email" class="form-control" placeholder="Correo electrónico" required>
                </div>
                <div class="col-12">
                    <textarea class="form-control" rows="3" placeholder="Mensaje" required></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </div>
            </form>
        </section>
    </main>
    <footer class="footer-gradient-blue">
        <div class="container footer-inner">
            <div class="footer-brand">
                <img src="assets/css/images/LogoMeta.png" alt="MetaHogar" />
                <p style="margin-top:12px; max-width:360px; color:rgba(255,255,255,0.95);">MetaHogar diseña hogares seguros e inteligentes para una longevidad más digna y confortable.</p>
                <div style="margin-top:16px; display:flex; gap:10px;">
                    <a class="social-link" href="#"><i class="fab fa-twitter fa-lg"></i></a>
                    <a class="social-link" href="#"><i class="fab fa-facebook-f fa-lg"></i></a>
                    <a class="social-link" href="#"><i class="fab fa-instagram fa-lg"></i></a>
                    <a class="social-link" href="#"><i class="fab fa-linkedin-in fa-lg"></i></a>
                </div>
            </div>

            <div class="footer-col">
                <h5 style="color:#fff; margin-bottom:14px;">Dirección</h5>
                <ul class="footer-contact" style="list-style:none; padding:0; margin:0; color:rgba(255,255,255,0.95);">
                    <li><i class="fas fa-map-marker-alt"></i> Av. Par Vial 10, Atlacomulco, 62560 Jiutepec, Mor.</li>
                    <li style="margin-top:8px;"><i class="fas fa-phone"></i> +52 1 777 129 4253</li>
                    <li style="margin-top:6px;"><i class="fas fa-envelope"></i> contacto@metahogar.com</li>
                </ul>
            </div>

            <div class="footer-col">
                <h5 style="color:#fff; margin-bottom:14px;">Boletín informativo</h5>
                <p style="color:rgba(255,255,255,0.95);">¡Mantente informado con nuestro boletín!</p>
                <div class="footer-newsletter" style="margin-top:10px;">
                    <input type="email" placeholder="Ingresa tu Email" aria-label="Ingresa tu Email">
                    <button class="send-btn" aria-label="Enviar boletín"><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>

            <div class="footer-col" style="display:flex; align-items:center; justify-content:center;">
                <!-- Decorative image / icon similar to reference -->
                <img src="assets/css/images/hero-ico-footer.png" alt="icono" style="max-height:160px; opacity:0.95;" />
            </div>
        </div>

        <div style="border-top:1px solid rgba(255,255,255,0.12); margin-top:32px;">
            <div class="container d-flex align-items-center justify-content-between py-3">
                <div style="color:rgba(255,255,255,0.85);">&copy; <?= date('Y') ?> MetaHogar. Todos los derechos reservados.</div>
                <div style="color:rgba(255,255,255,0.85);">
                    <a href="#" style="color:rgba(255,255,255,0.85); margin-right:14px;">Home</a>
                    <a href="#" style="color:rgba(255,255,255,0.85); margin-right:14px;">Cookies</a>
                    <a href="#" style="color:rgba(255,255,255,0.85); margin-right:14px;">Help</a>
                    <a href="#" style="color:rgba(255,255,255,0.85);">FAQs</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
    (function(){
      const el = document.getElementById('visits-count');
      if (!el) return;
      const url = 'visit_counter.php?only_read=1';
      // refresca cada 30 segundos (puedes cambiar el intervalo)
      setInterval(() => {
        fetch(url).then(r => r.text()).then(txt => {
          el.textContent = txt.trim();
        }).catch(()=>{});
      }, 30000);
    })();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/navbar-sticky.js"></script>
    </body>
</html>