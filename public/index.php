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
// Define constante para permitir includes seguros (footer y otros)
if (!defined('ESTADIA_INIT')) {
    define('ESTADIA_INIT', true);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MetaHogar - Longevitud Segura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/bs-navbar.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MetaHogar - Vivir más, Vivir mejor</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    

    <script src="https://cdn.userway.org/widget.js" data-account="kjnkkEfZy1"></script>
    <style>
        /* Hero image styling to match provided reference */
        .hero-section { background: linear-gradient(180deg,#17466e 0%,#4b96c3 100%); padding: 80px 0; position: relative; overflow: hidden; }
        .hero-title { font-size: 2.25rem; line-height: 1.05; font-weight: 700; }
        .hero-image-col { display:flex; align-items:center; justify-content:center; }
        .hero-image-container { position: relative; width:100%; max-width:700px; }
        .hero-image-wrapper { position: relative; }
        /* decorative shapes & white blob behind the photo */
        .hero-image-wrapper { position: relative; }
        .hero-image-wrapper::before {
            /* white organic blob behind the photo */
            content: '';
            position: absolute;
            right: -40px;
            top: -20px;
            width: 560px;
            height: 420px;
            background: #ffffff;
            border-radius: 48% 52% 46% 54% / 54% 46% 54% 46%;
            transform: rotate(-6deg);
            box-shadow: 0 20px 40px rgba(0,0,0,0.06);
            z-index: 1;
        }
        .hero-image-wrapper .accent-blob-a, .hero-image-wrapper .accent-blob-b, .hero-image-wrapper .accent-brush { position:absolute; z-index:2; }
        .accent-blob-a { width: 160px; height: 160px; background: rgba(168,223,215,0.9); left: -10px; bottom: -30px; border-radius: 40%; }
        .accent-blob-b { width: 120px; height: 120px; background: rgba(67,146,227,0.12); right: 10px; bottom: -10px; border-radius: 40%; }
        .accent-brush { width: 320px; height: 80px; background: rgba(67,146,227,0.95); right: 0px; bottom: -18px; border-radius: 24px; transform: rotate(-6deg); }
        .hero-image-wrapper img { position: relative; display:block; width:100%; max-width:600px; height:auto; border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.15); margin-top: -80px; z-index:3; }
        @media(max-width:991px){ .hero-image-wrapper img { margin-top: 0; max-width:420px; } }
        @media(max-width:767px){ .hero-image-col { display:none !important; } }
    </style>
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
    <div class="container hero-split-bg"> <div class="row align-items-center h-100">
            
            <div class="col-lg-7 text-center text-lg-start hero-text-area"> <h2 class="hero-title text-white">
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

            <div class="col-lg-5 d-none d-lg-flex hero-image-col">
                <div class="hero-image-container">
                    <div class="hero-image-wrapper">
                        <!-- white blob created by CSS ::before, accent shapes and brush below -->
                        <div class="accent-blob-a"></div>
                        <div class="accent-blob-b"></div>
                        <div class="accent-brush"></div>
                        <!-- Use the masked PNG (transparent background) named img_adultos.png placed under assets/css/images/ -->
                        <img class="img-fluid d-none d-sm-none d-md-block" style="max-width:600px; margin-top: -80px;" src="assets/css/images/img_adultos.png" alt="Familia multigeneracional usando tablet">
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

                <div id="servicesCarousel" class="carousel slide" data-bs-ride="false" data-bs-interval="false">
                    <div class="carousel-inner">
                        
                        <div class="carousel-item active">
                            <div class="row g-4 justify-content-center">
                                
                                <div class="col-lg-4 col-md-6 d-flex"> 
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

                                <div class="col-lg-4 col-md-6 d-flex"> 
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

                                <div class="col-lg-4 col-md-6 d-flex"> 
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

                        <div class="carousel-item">
                            <div class="row g-4 justify-content-center">
                                
                                <div class="col-lg-4 col-md-6 d-flex"> 
                                    <div class="service-step-card">
                                        <div class="service-icon-circle">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <h4>Infraestructura</h4>
                                        <p><strong>Diseño adaptativo</strong></p>
                                        <p>
                                            Implementamos rampas y pasamanos para movilidad asistida. Aseguramos que los pasillos, puertas, gabinetes, y áreas de baño (WC, regadera, lavamanos) estén adaptados en altura y amplitud para el confort y seguridad del adulto mayor.
                                        </p>
                                        <ul>
                                            <li>Rampas y barandillas</li>
                                            <li>Gabinetes y barras adaptadas</li>
                                            <li>Baños y pasillos amplios</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 d-flex"> 
                                    <div class="service-step-card">
                                        <div class="service-icon-circle">
                                            <i class="fas fa-microchip"></i>
                                        </div>
                                        <h4>Tecnología</h4>
                                        <p><strong>Hogar Inteligente</strong></p>
                                        <p>
                                            Integramos asistentes de voz (Amazon Echo, Google Home) e iluminación inteligente con sensores de movimiento y ajuste automático. Incluimos monitoreo de salud avanzado y recordatorios de medicación para independencia y tranquilidad.
                                        </p>
                                        <ul>
                                            <li>Asistentes de voz</li>
                                            <li>Iluminación con sensores</li>
                                            <li>Detector de caídas VAYYAR</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 d-flex"> 
                                    <div class="service-step-card">
                                        <div class="service-icon-circle">
                                            <i class="fas fa-bullseye"></i>
                                        </div>
                                        <h4>Objetivos</h4>
                                        <p><strong>Impacto Social</strong></p>
                                        <p>
                                            Nuestro enfoque es promover el desarrollo de nuevas tecnologías y reducir accidentes mediante sistemas de seguridad y automatización. Colaboramos activamente con ONGs para mejorar la calidad de vida de adultos mayores en situación de desventaja.
                                        </p>
                                        <ul>
                                            <li>Promover investigación</li>
                                            <li>Reducir accidentes</li>
                                            <li>Apoyo a causas sociales</li>
                                        </ul>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        
                    </div>
                    
                    <button class="carousel-control-prev" type="button" data-bs-target="#servicesCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#servicesCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                </div>
                </div>
        </section>
        
        <section id="infraestructura" class="mb-5 services-container">
            <div class="container">
                <h2 class="text-primary text-center">Soluciones de Infraestructura</h2>
                <h4 class="text-center mb-5">Adaptaciones físicas para mayor seguridad.</h4>

                <div id="infraCarousel" class="carousel slide" data-bs-ride="false" data-bs-interval="false">
                    <div class="carousel-inner">
                        
                        <div class="carousel-item active">
                            <div class="row g-4 justify-content-center">
                                
                                <div class="col-lg-4 col-md-6 d-flex">
                                    <div class="service-step-card">
                                        <div class="service-icon-circle">
                                            <i class="fas fa-wheelchair"></i> 
                                        </div>
                                        <h4>Rampas y Accesos</h4>
                                        <p>Facilitan la movilidad y reducen drásticamente los riesgos de caídas en desniveles o escalones. Diseñadas para ser seguras y antiderrapantes.</p>
                                        <ul>
                                            <li>Rampas móviles y fijas.</li>
                                            <li>Superficies antiderrapantes.</li>
                                            <li>Pendientes adecuadas a normativas.</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 d-flex">
                                    <div class="service-step-card">
                                        <div class="service-icon-circle">
                                            <i class="fas fa-hand-rock"></i>
                                        </div>
                                        <h4>Pasamanos y Barandillas</h4>
                                        <p>Obligados en rampas, escaleras, baños y pasillos largos para proporcionar un punto de apoyo constante y seguro al adulto mayor.</p>
                                        <ul>
                                            <li>Instalación bajo norma.</li>
                                            <li>Materiales resistentes y ergonómicos.</li>
                                            <li>Uso en zonas críticas (baños, escaleras).</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 d-flex">
                                    <div class="service-step-card">
                                        <div class="service-icon-circle">
                                            <i class="fas fa-kitchen-set"></i>
                                        </div>
                                        <h4>Cocina y Gabinetes</h4>
                                        <p>Gabinetes, barras y fregadero ajustados a alturas adaptadas, permitiendo el uso independiente de la cocina con mayor confort y seguridad.</p>
                                        <ul>
                                            <li>Alturas regulables.</li>
                                            <li>Fregaderos con espacio para sillas de ruedas.</li>
                                            <li>Electrodomésticos accesibles.</li>
                                        </ul>
                                    </div>
                                </div>
                                
                            </div>
                        </div>

                        <div class="carousel-item">
                            <div class="row g-4 justify-content-center">
                                
                                <div class="col-lg-4 col-md-6 d-flex">
                                    <div class="service-step-card">
                                        <div class="service-icon-circle">
                                            <i class="fas fa-toilet"></i>
                                        </div>
                                        <h4>Baños Adaptados</h4>
                                        <p>WC, regadera y lavamanos con adaptaciones específicas (barras de apoyo, asientos de ducha, pisos antiderrapantes) para la seguridad y confort en el área de mayor riesgo.</p>
                                        <ul>
                                            <li>Barras de apoyo fijas y móviles.</li>
                                            <li>Asientos y bancas en regaderas.</li>
                                            <li>Grifos de fácil operación.</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 d-flex">
                                    <div class="service-step-card">
                                        <div class="service-icon-circle">
                                            <i class="fas fa-door-open"></i>
                                        </div>
                                        <h4>Pasillos y Puertas</h4>
                                        <p>Ampliación de pasillos y puertas para permitir el paso cómodo de sillas de ruedas o andadores, mejorando la movilidad dentro del hogar.</p>
                                        <ul>
                                            <li>Ancho mínimo para movilidad.</li>
                                            <li>Manijas de palanca.</li>
                                            <li>Eliminación de umbrales.</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 d-flex">
                                    <div class="service-step-card">
                                        <div class="service-icon-circle">
                                            <i class="fas fa-plug"></i>
                                        </div>
                                        <h4>Apagadores y Enchufes</h4>
                                        <p>Reubicación de apagadores y enchufes a la vista y a la altura correcta (según normativa de accesibilidad) para un uso sencillo sin tener que agacharse o estirarse.</p>
                                        <ul>
                                            <li>Altura accesible.</li>
                                            <li>Control de iluminación centralizado.</li>
                                            <li>Enchufes con indicador de luz.</li>
                                        </ul>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        
                    </div>
                    
                    <button class="carousel-control-prev" type="button" data-bs-target="#infraCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#infraCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                </div>
                </div>
        </section>
        
        <section id="tecnologia" class="mb-5 services-container">
            <div class="container">
                <h2 class="text-primary text-center">Soluciones Tecnológicas</h2>
                <h4 class="text-center mb-5">Innovación para autonomía y seguridad.</h4>
                
                <div id="techCarousel" class="carousel slide" data-bs-ride="false" data-bs-interval="false">
                    <div class="carousel-inner">
                        
                        <div class="carousel-item active">
                            <div class="row g-4 justify-content-center">
                                
                                <div class="col-lg-4 col-md-6 d-flex"> 
                                    <div class="service-step-card">
                                        <div class="service-icon-circle">
                                            <i class="fas fa-microphone-alt"></i> 
                                        </div>
                                        <h4>Asistentes de Voz</h4>
                                        <p>Integración de Amazon Echo, Google Home y otros dispositivos para control por voz de luces, termostato y comunicación, simplificando las tareas diarias.</p>
                                        <ul>
                                            <li>Control manos libres.</li>
                                            <li>Comunicación rápida con familiares.</li>
                                            <li>Recordatorios verbales de medicinas.</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 d-flex"> 
                                    <div class="service-step-card">
                                        <div class="service-icon-circle">
                                            <i class="fas fa-lightbulb"></i>
                                        </div>
                                        <h4>Iluminación Inteligente</h4>
                                        <p>Instalación de sensores de movimiento y sistemas de iluminación automatizada para encendido/ajuste automático, previniendo caídas en la oscuridad.</p>
                                        <ul>
                                            <li>Sensores en pasillos y baños.</li>
                                            <li>Ajuste automático de intensidad.</li>
                                            <li>Simulación de presencia.</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 d-flex"> 
                                    <div class="service-step-card">
                                        <div class="service-icon-circle">
                                            <i class="fas fa-shield-halved"></i>
                                        </div>
                                        <h4>Detector de Caídas</h4>
                                        <p>Implementación de sistemas avanzados (como VAYYAR CARE u otros sensores 3D) para monitoreo constante y alertas inmediatas sin necesidad de usar wearables.</p>
                                        <ul>
                                            <li>Monitoreo sin cámara (privacidad).</li>
                                            <li>Alertas automáticas a contactos.</li>
                                            <li>Detección 24/7 en áreas clave.</li>
                                        </ul>
                                    </div>
                                </div>
                                
                            </div>
                        </div>

                        <div class="carousel-item">
                            <div class="row g-4 justify-content-center">
                                
                                <div class="col-lg-4 col-md-6 d-flex"> 
                                    <div class="service-step-card">
                                        <div class="service-icon-circle">
                                            <i class="fas fa-heartbeat"></i>
                                        </div>
                                        <h4>Monitoreo y Medicación</h4>
                                        <p>Integración de pulseras y dispositivos inteligentes para el monitoreo de salud (ritmo cardíaco, sueño) y dispensadores automatizados con recordatorios y alertas de medicación.</p>
                                        <ul>
                                            <li>Dispensadores inteligentes.</li>
                                            <li>Pulseras de alerta y monitoreo.</li>
                                            <li>Reportes de cumplimiento para familiares.</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 d-flex"> 
                                    <div class="service-step-card">
                                        <div class="service-icon-circle">
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        <h4>Seguridad y Acceso</h4>
                                        <p>Sistemas de cerraduras inteligentes, videoporteros y control de acceso remoto para asegurar el hogar y facilitar la entrada a personal de cuidado o familiares autorizados.</p>
                                        <ul>
                                            <li>Cerraduras con código/huella.</li>
                                            <li>Videoporteros con control remoto.</li>
                                            <li>Alarmas de intrusión sencillas.</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 d-flex"> 
                                    <div class="service-step-card">
                                        <div class="service-icon-circle">
                                            <i class="fas fa-fan"></i>
                                        </div>
                                        <h4>Climatización Inteligente</h4>
                                        <p>Control automatizado de temperatura y calidad del aire (termostatos inteligentes, purificadores) para mantener un ambiente confortable sin esfuerzo físico.</p>
                                        <ul>
                                            <li>Termostatos programables.</li>
                                            <li>Control por voz y app.</li>
                                            <li>Monitoreo de calidad del aire.</li>
                                        </ul>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        
                    </div>
                    
                    <button class="carousel-control-prev" type="button" data-bs-target="#techCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#techCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                </div>
                </div>
        </section>

        <section id="objetivos" class="mb-5 services-container">
            <div class="container">
                <h2 class="text-primary text-center">Objetivos</h2>
                <h4 class="text-center mb-5">Nuestra misión y visión de impacto social.</h4>

                <div class="row g-4 justify-content-center">
                    
                    <div class="col-lg-4 col-md-6 d-flex"> 
                        <div class="service-step-card">
                            <div class="service-icon-circle">
                                <i class="fas fa-seedling"></i>
                            </div>
                            <h4>Promover la Innovación</h4>
                            <p>Impulsar la investigación y el desarrollo de nuevas tecnologías y soluciones de diseño universal adaptadas a las necesidades cambiantes de los adultos mayores.</p>
                            <ul>
                                <li>Investigación continua.</li>
                                <li>Alianzas con desarrolladores.</li>
                                <li>Adaptación de tecnologías existentes.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 d-flex"> 
                        <div class="service-step-card">
                            <div class="service-icon-circle">
                                <i class="fas fa-head-side-mask"></i>
                            </div>
                            <h4>Reducir Riesgos</h4>
                            <p>Reducir la incidencia de accidentes y lesiones en el hogar mediante sistemas de seguridad proactivos, automatización y adaptaciones de infraestructura.</p>
                            <ul>
                                <li>Sistemas de detección temprana.</li>
                                <li>Implementación de barreras físicas.</li>
                                <li>Educación y capacitación en seguridad.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 d-flex"> 
                        <div class="service-step-card">
                            <div class="service-icon-circle">
                                <i class="fas fa-handshake-angle"></i>
                            </div>
                            <h4>Impacto Social</h4>
                            <p>Colaborar activamente con ONG's, instituciones y la comunidad para mejorar la calidad de vida de adultos mayores en situación de vulnerabilidad o desventaja.</p>
                            <ul>
                                <li>Colaboración con ONGs (DAM A.C., AARP).</li>
                                <li>Programas de accesibilidad social.</li>
                                <li>Concientización comunitaria.</li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
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

        <?php if (isLogged()):
            // cargar servicios activos para el select
            $stm = $pdo->prepare('SELECT idServicio, Nombre FROM servicio WHERE Activo = 1 ORDER BY Nombre ASC');
            $stm->execute();
            $servicios = $stm->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <?php /*
        <section id="pedir-cita" class="mb-5">
            <h2 class="text-primary">Pedir una cita</h2>
            <p>Como usuario registrado puedes solicitar una cita con nuestros servicios. Te contactaremos para confirmar.</p>

            <div id="cita-alert"></div>

            <form id="citaForm" class="row g-3">
                <div class="col-md-6">
                    <label for="servicio" class="form-label">Servicio</label>
                    <select id="servicio" name="servicio" class="form-select" required>
                        <option value="">-- Selecciona un servicio --</option>
                        <?php foreach ($servicios as $s): ?>
                            <option value="<?= htmlspecialchars($s['idServicio']) ?>"><?= htmlspecialchars($s['Nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="fecha_hora" class="form-label">Fecha y hora</label>
                    <input id="fecha_hora" name="fecha_hora" type="datetime-local" class="form-control" required>
                </div>

                <div class="col-12">
                    <label for="notas" class="form-label">Notas / Dirección / Preferencias</label>
                    <textarea id="notas" name="notas" class="form-control" rows="3" placeholder="Opcional: indica dirección, notas o preferencia" ></textarea>
                </div>

                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-success">Solicitar cita</button>
                </div>
            </form>
        </section>
        */ ?>
        <?php else: ?>
            <section class="mb-5">
                <h2 class="text-primary">Pedir una cita</h2>
                <p>Para solicitar una cita debes <a href="login.php">iniciar sesión</a> o crear una cuenta.</p>
            </section>
        <?php endif; ?>
    </main>
    

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

    <script>
    // Handle cita form submission via fetch
    document.addEventListener('DOMContentLoaded', function(){
        const citaForm = document.getElementById('citaForm');
        if (!citaForm) return;

        citaForm.addEventListener('submit', async function(e){
            e.preventDefault();
            const formData = new FormData(citaForm);
            const submit = citaForm.querySelector('button[type="submit"]');
            submit.disabled = true;
            submit.innerHTML = 'Enviando...';

            try {
                const resp = await fetch('php/citas/add.php', { method: 'POST', body: formData });
                const data = await resp.json();
                const alertBox = document.getElementById('cita-alert');
                alertBox.innerHTML = '';
                const div = document.createElement('div');
                if (data.success) {
                    div.className = 'alert alert-success';
                    div.textContent = data.message || 'Cita solicitada.';
                    citaForm.reset();
                } else {
                    div.className = 'alert alert-danger';
                    div.textContent = data.message || 'Error al solicitar.';
                }
                alertBox.appendChild(div);
            } catch (err) {
                const alertBox = document.getElementById('cita-alert');
                alertBox.innerHTML = '<div class="alert alert-danger">Error enviando la solicitud.</div>';
            } finally {
                submit.disabled = false;
                submit.innerHTML = 'Solicitar cita';
            }
        });
    });
    </script>

    <?php
    // Incluir footer local (corrige la ruta y evita duplicar cierre HTML)
    require_once __DIR__ . '/includes/footer.php';
    ?>