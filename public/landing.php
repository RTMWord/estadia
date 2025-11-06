<?php 
// Define una constante para asegurar que los includes solo se carguen desde aquí
define('ESTADIA_INIT', true);

// Incluye la sesión y el encabezado
require_once 'includes/session.php'; 
$page_title = "Inicio - Estadía Segura e Independiente";
require_once 'includes/header.php'; 

// En una aplicación real, aquí podrías llamar a un controlador si fuera necesario:
// require_once '../app/controllers/LandingController.php';
// $controller = new LandingController();
// $data = $controller->getLandingData();
?>

    <main class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">
                Diseñamos hogares para que tus adultos mayores vivan una longevidad segura e independiente en el hogar que atesoran.
            </h1>
            <p class="hero-subtitle">
                Tecnología que transforma tu hogar, seguridad que transforma tu vida.
            </p>
            <div class="hero-actions">
                <!-- Botón "Leer Más" que lleva a la página de servicios/detalle -->
                <a href="servicios.php" class="btn btn-primary">
                    Leer Más
                </a>
                <!-- Botón "Contáctanos" que lleva a la página de citas/contacto -->
                <a href="cita_nueva.php" class="btn btn-secondary">
                    Contáctanos
                </a>
            </div>
        </div>

        <div class="hero-image-container">
            <!-- Capa de fondo para replicar la forma curva -->
            <div class="shape-overlay"></div>
            <!-- Imagen (Usando el archivo que proporcionaste como referencia) -->
            <img src="assets/img/hero_adultos_mayores.jpg" 
                 alt="Abuelos sonriendo y compartiendo una tableta con sus nietos, simbolizando tecnología y familia." 
                 class="hero-image"
                 onerror="this.onerror=null; this.src='https://placehold.co/600x400/1e3a8a/ffffff?text=Imagen+Familiar';"
            >
        </div>
    </main>

<?php 
// Incluye el pie de página
require_once 'includes/footer.php'; 
?>
