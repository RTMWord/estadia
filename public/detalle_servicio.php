<?php
// public/detalle_servicio.php
session_start();
require_once __DIR__ . '/../app/controllers/ServicioController.php';
$ctrl = new ServicioController();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: servicios.php');
    exit;
}
$servicio = $ctrl->detalle($id);
if (!$servicio) {
    http_response_code(404);
    echo "<h1>Servicio no encontrado</h1>";
    exit;
}

// Prepare safe view values (some columns may be absent in the model result)
$view = [];
$view['id'] = $servicio['id'] ?? $servicio['idServicio'] ?? 0;
$view['titulo'] = $servicio['titulo'] ?? $servicio['Nombre'] ?? '';
$view['descripcion'] = $servicio['descripcion'] ?? $servicio['Descripcion'] ?? '';
$view['categoria'] = $servicio['categoria'] ?? $servicio['Categoria'] ?? '';
$view['ubicacion'] = $servicio['ubicacion'] ?? $servicio['Ubicacion'] ?? '';
$view['precio'] = $servicio['precio'] ?? $servicio['Costo'] ?? '';
$view['contacto'] = $servicio['contacto'] ?? $servicio['Contacto'] ?? '';
$view['imagen'] = $servicio['imagen'] ?? '';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($servicio['titulo']); ?> - Meto-Hogar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/catalogo.css">
    
    <!-- Widget de Accesibilidad -->
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
<body>
<div class="container my-4">
    <a href="servicios.php" class="btn btn-link mb-3">&larr; Volver al catálogo</a>

    <div class="row g-4">
        <div class="col-md-5">
                <?php if (!empty($view['imagen'])): ?>
                    <img src="<?php echo htmlspecialchars($view['imagen']); ?>" class="img-fluid rounded servicio-imagen" alt="<?php echo htmlspecialchars($view['titulo']); ?>">
                <?php endif; ?>
            </div>
            <div class="col-md-7">
                <h1><?php echo htmlspecialchars($view['titulo']); ?></h1>
                <p class="text-muted small"><?php echo htmlspecialchars($view['categoria']); ?> — <?php echo htmlspecialchars($view['ubicacion']); ?></p>
                <h4 class="text-primary"><?php echo (!empty($view['precio']) ? 'Precio: $' . number_format((float)$view['precio'], 2) : 'Precio: A convenir'); ?></h4>
                <hr>
                <div><?php echo nl2br(htmlspecialchars($view['descripcion'])); ?></div>
                <hr>
                <p><strong>Contacto:</strong> <?php echo htmlspecialchars($view['contacto']); ?></p>

            <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="admin/servicio_editar.php?id=<?php echo (int)$servicio['id']; ?>" class="btn btn-secondary">Editar</a>
                <a href="admin/servicio_eliminar.php?id=<?php echo (int)$servicio['id']; ?>" onclick="return confirm('Confirmar eliminación del servicio?');" class="btn btn-danger">Eliminar</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
?>