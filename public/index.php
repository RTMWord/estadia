<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/helpers/auth.php';
$user = null;
if (isLogged()) {
    $stmt = $pdo->prepare('SELECT Nombre, ApellidoP FROM Usuario WHERE idUsuario = ? LIMIT 1');
    $stmt->execute([getUserId()]);
    $user = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MetaHogar - Longevitud Segura</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white text-center py-5 position-relative">
        <div class="position-absolute top-0 end-0 p-3">
            <?php if (!$user): ?>
                <a href="login.php?next=<?= urlencode('/Estadia/public/index.php') ?>" class="btn btn-light">Iniciar sesión</a>
            <?php else: ?>
                <div class="d-flex align-items-center">
                    <span class="me-2">Hola, <?= htmlspecialchars($user['Nombre']) ?></span>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'administrador'): ?>
                        <a href="admin/index.php" class="btn btn-outline-light btn-sm me-2">Panel</a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn btn-outline-light btn-sm">Salir</a>
                </div>
            <?php endif; ?>
        </div>
        <h1>MetaHogar</h1>
        <p class="lead">Diseñamos hogares para que tus adultos mayores vivan una longevidad segura e independiente en el hogar que atesoran.</p>
        <p>Tecnología que transforma tu hogar, seguridad que transforma tu vida.</p>
        <div class="mt-4">
            <a href="#nosotros" class="btn btn-light me-2">Leer Más</a>
            <a href="#contacto" class="btn btn-outline-light">Contáctanos</a>
        </div>
    </header>
    <main class="container my-5">
        <section id="nosotros" class="mb-5">
            <h2 class="text-primary">Nosotros</h2>
            <h3>MetaHogar</h3>
            <p class="fs-4">Vivir más, Vivir mejor.</p>
            <p>Es una empresa dedicada a brindar soluciones tecnológicas inteligentes para el hogar, con un enfoque especial en los adultos mayores. Nuestro objetivo principal es mejorar la seguridad, la comodidad y la eficiencia en la vida diaria de esta población. Nos enfocamos en transformar los hogares en espacios inteligentes, donde la tecnología se integra de manera intuitiva y fácil de usar, especialmente diseñada para satisfacer las necesidades y preferencias de los adultos mayores.</p>
            <a href="#servicios" class="btn btn-primary">Leer Más</a>
        </section>
        <section id="servicios" class="mb-5">
            <h2 class="text-primary">Servicios</h2>
            <h4>Asesoría</h4>
            <p>MetaHogar es una iniciativa vinculada con la Asociación Americana de Personas Retiradas (AARP) y con la Asociación Civil Desarrollo Aplicativo para la Movilidad (DAM A.C.) y nuestro personal está capacitado para ayudarle a que usted tome las decisiones correctas en el momento adecuado si es que ha decidido brindarle a sus adultos mayores una vivienda funcional, confortable y segura para una longevidad digna.</p>
            <h5>Proceso</h5>
            <ul>
                <li><strong>Valoración y diagnóstico:</strong> Realizamos un análisis integral del hogar a intervenir para identificar los puntos críticos que pudieran representar un eventual riesgo que vulnere la integridad física de sus queridos adultos mayores. Elaboramos una matriz de riesgos para calcular la posibilidad de que ocurra un accidente contra el impacto que pueda tener en el adulto mayor.</li>
                <li><strong>Propuesta técnica y económica:</strong> Se proponen adaptaciones físicas y soluciones tecnológicas que convertirán su hogar en una vivienda segura, funcional y confortable para una longevidad digna. Brindan independencia y monitoreo remoto.</li>
                <li><strong>Trabajamos sobre 4 ejes:</strong> Riesgo, Funcionalidad, Confort, Independencia/Monitoreo/Tranquilidad.</li>
                <li><strong>Ejecución:</strong> Áreas de arquitectura, mecatrónica, electrónica, electricidad y energías renovables realizan adaptaciones bajo norma. Al concluir, el cliente recibe manual y capacitación.</li>
            </ul>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

