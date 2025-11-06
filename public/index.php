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
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/bs-navbar.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MetaHogar - Vivir más, Vivir mejor</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    

    <style>
        /* Estilos existentes para el contador */
        .top-controls { display:flex; gap:10px; align-items:center; }
        .visits-box {
            background: #fff18b;
            color: #144d7bff;
            padding: 6px 10px;
            font-weight: 700;
            border-radius: 4px;
            box-shadow: 0 1px 0 rgba(0,0,0,.08);
            white-space: nowrap;
            font-size: 0.95rem;
        }
        @media (max-width: 520px) {
            .visits-box { font-size: 0.85rem; padding: 4px 8px; }
        }
        
        /* Estilos de la IMAGEN HERO */
        
        .hero-section {
            /* Color de fondo degradado (Azul oscuro a azul medio) */
            background-color: #032e54;
            background-image: linear-gradient(135deg, #032e54 0%, #175e8d 100%);
            min-height: 80vh; /* Altura mínima de la sección */
            display: flex;
            align-items: center;
            padding: 100px 0 50px 0; /* Padding superior para separar del navbar fijo */
            color: white;
            position: relative;
            overflow: hidden; /* Oculta partes de la imagen que desborden */
        }
        .hero-image-container {
            position: relative;
            height: 100%;
        }
        /* Ajuste de la tipografía para el título */
        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3); /* Sombra suave */
        }
        /* Ajuste del botón 'Leer Más' */
        .btn-custom-blue {
            background-color: #5bb3d6; /* Azul más claro del botón */
            border-color: #5bb3d6;
            color: white;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        .btn-custom-blue:hover {
            background-color: #4da3c4;
            border-color: #4da3c4;
            color: white;
        }
        /* Ajuste del botón 'Contáctanos' */
        .btn-custom-outline {
            border-color: white;
            color: white;
            font-weight: 500;
        }
        .btn-custom-outline:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        /* Nuevos estilos para los blobs del Hero (Asumidos de la respuesta anterior) */
        .hero-image-wrapper {
            position: relative;
            width: 100%;
            height: 400px; /* Define una altura fija para contener las formas */
        }
        .blob-shape-light {
            position: absolute;
            width: 350px;
            height: 300px;
            background-color: #eaf3f5;
            clip-path: polygon(0 0, 100% 0, 100% 65%, 75% 100%, 25% 90%, 0 50%);
            border-radius: 60% 40% 50% 50% / 60% 40% 40% 60%;
            top: 0;
            right: 0;
            transform: rotate(-10deg);
            z-index: -1;
        }
        .blob-shape-dark {
            position: absolute;
            width: 250px;
            height: 200px;
            background-color: #5bb3d6;
            clip-path: polygon(25% 0%, 100% 0%, 75% 100%, 0% 100%);
            border-radius: 50% 50% 40% 60% / 50% 40% 60% 50%;
            bottom: -50px;
            left: 20px;
            transform: rotate(15deg);
            z-index: -1;
        }
        .blob-brush {
            position: absolute;
            width: 280px;
            height: 80px;
            background-color: #276e93;
            bottom: -20px;
            left: 20px;
            border-radius: 0 0 50% 50%;
            transform: skewX(-20deg);
            z-index: 0;
        }
        .hero-image-container img {
            position: absolute;
            max-width: 110%;
            height: auto;
            bottom: 0;
            left: -5%; 
        }

    </style>
    <script src="https://cdn.userway.org/widget.js" data-account="kjnkkEfZy1"></script>
</head>
<body>
    <?php include __DIR__ . '/partials/bs-navbar.php'; ?>
    
    <div class="position-absolute top-0.5 end-0 p-0.5" style="z-index: 9999;">
        <div class="top-controls">
            <div class="visits-box">
                N° Visitas: <span id="visits-count"><?= htmlspecialchars($visits) ?></span>
            </div>
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
    <footer class="bg-light text-center py-3">
        <small>&copy; 2025 MetaHogar. Todos los derechos reservados.</small>
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