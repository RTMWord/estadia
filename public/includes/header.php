<?php
// Evita el acceso directo al include
if (!defined('ESTADIA_INIT')) {
    exit('Acceso denegado');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : "Estadía Segura"; ?></title>

    <!-- Enlace a la hoja de estilos principal -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Enlace a estilos específicos del catálogo si es necesario -->
    <?php if (basename($_SERVER['PHP_SELF']) == 'servicios.php'): ?>
        <link rel="stylesheet" href="assets/css/catalogo.css">
    <?php endif; ?>

    <!-- Fuente Inter de Google Fonts (opcional, pero recomendada para diseño moderno) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Widget de Accesibilidad UserWay -->
    <script src="https://cdn.userway.org/widget.js" data-account="kjnkkEfZy1"></script>
    <style>
        .userway-icon {
            position: fixed !important;
            right: 20px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            z-index: 9999 !important;
        }
    </style>
</head>
<body class="font-inter">
    <?php 
    // Incluye la barra de navegación aquí
    include 'navbar.php'; 
    ?>
    <!-- El contenido principal de la página comienza aquí -->