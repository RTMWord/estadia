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
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($servicio['titulo']); ?> - Meto-Hogar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/catalogo.css">
</head>
<body>
<div class="container my-4">
    <a href="servicios.php" class="btn btn-link mb-3">&larr; Volver al catálogo</a>

    <div class="row g-4">
        <div class="col-md-5">
            <?php if (!empty($servicio['imagen'])): ?>
                <img src="<?php echo htmlspecialchars($servicio['imagen']); ?>" class="img-fluid rounded servicio-imagen" alt="<?php echo htmlspecialchars($servicio['titulo']); ?>">
            <?php endif; ?>
        </div>
        <div class="col-md-7">
            <h1><?php echo htmlspecialchars($servicio['titulo']); ?></h1>
            <p class="text-muted small"><?php echo htmlspecialchars($servicio['categoria']); ?> — <?php echo htmlspecialchars($servicio['ubicacion']); ?></p>
            <h4 class="text-primary"><?php echo (!empty($servicio['precio']) ? 'Precio: $' . number_format((float)$servicio['precio'], 2) : 'Precio: A convenir'); ?></h4>
            <hr>
            <div><?php echo nl2br(htmlspecialchars($servicio['descripcion'])); ?></div>
            <hr>
            <p><strong>Contacto:</strong> <?php echo htmlspecialchars($servicio['contacto']); ?></p>

            <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="admin/servicio_editar.php?id=<?php echo (int)$servicio['id']; ?>" class="btn btn-secondary">Editar</a>
                <a href="admin/servicio_eliminar.php?id=<?php echo (int)$servicio['id']; ?>" onclick="return confirm('Confirmar eliminación del servicio?');" class="btn btn-danger">Eliminar</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
?>