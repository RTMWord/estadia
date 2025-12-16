<?php
session_start();
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/models/Producto.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
requireRole($pdo, 'administrador');

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Eliminar (lógico)
// Procesar acciones por POST para evitar side-effects vía GET
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Eliminar (lógico)
    if (!empty($_POST['eliminar'])) {
        Producto::eliminar($pdo, $_POST['eliminar']);
        header('Location: productos.php'); exit;
    }

    // Toggle / actualizar activo desde la lista
    if (!empty($_POST['toggle_active']) && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $activo = isset($_POST['activo']) && (int)$_POST['activo'] === 1 ? 1 : 0;
        Producto::setActivo($pdo, $id, $activo);
        header('Location: productos.php'); exit;
    }
}

$items = Producto::getAll($pdo);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Productos - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
<body class="p-4">
<?php include __DIR__ . '/partials/admin_nav.php'; ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Productos</h1>
        <a href="producto_nuevo.php" class="btn btn-primary">Agregar producto</a>
    </div>

    <table class="table table-hover">
        <thead>
            <tr><th>#</th><th>Nombre</th><th>Precio</th><th>Existencia</th><th>Activo</th><th>Acciones</th></tr>
        </thead>
        <tbody>
            <?php foreach ($items as $it): ?>
                <tr>
                    <td><?= $it['idProducto'] ?></td>
                    <td><?= htmlspecialchars($it['Nombre']) ?></td>
                    <td><?= number_format($it['Precio'],2) ?></td>
                    <td><?= (int)$it['Existencia'] ?></td>
                    <td>
                        <form method="post" class="d-inline">
                            <input type="hidden" name="id" value="<?= $it['idProducto'] ?>">
                            <input type="hidden" name="activo" value="0">
                            <input type="checkbox" name="activo" value="1" <?= $it['Activo'] ? 'checked' : '' ?> onchange="this.form.submit()" aria-label="Activo">
                            <input type="hidden" name="toggle_active" value="1">
                        </form>
                    </td>
                    <td>
                        <a class="btn btn-sm btn-secondary" href="producto_editar.php?id=<?= $it['idProducto'] ?>">Editar</a>
                        <form method="post" class="d-inline" onsubmit="return confirm('Eliminar producto?')">
                            <input type="hidden" name="eliminar" value="<?= $it['idProducto'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="index.php" class="btn btn-secondary">Volver</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if ($flashSuccess): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Éxito',
    text: '<?= addslashes($flashSuccess) ?>',
    confirmButtonColor: '#3085d6',
});
</script>
<?php endif; ?>
<?php if ($flashError): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: '<?= addslashes($flashError) ?>',
    confirmButtonColor: '#d33',
});
</script>
<?php endif; ?>
</body>
</html>