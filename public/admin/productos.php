<?php
session_start();
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/models/Producto.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
requireRole($pdo, 'administrador');

// Eliminar (lógico)
if (isset($_GET['eliminar'])) {
    Producto::eliminar($pdo, $_GET['eliminar']);
    header('Location: productos.php'); exit;
}

$items = Producto::getAll($pdo);
?>
<!doctype html>
<html lang="es">
<head>
<body>
<?php include __DIR__ . '/partials/admin_nav.php'; ?>
    <div class="container py-5">
    <title>Productos - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
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
                    <td><?= $it['Activo'] ? 'Sí' : 'No' ?></td>
                    <td>
                        <a class="btn btn-sm btn-secondary" href="producto_editar.php?id=<?= $it['idProducto'] ?>">Editar</a>
                        <a class="btn btn-sm btn-danger" href="?eliminar=<?= $it['idProducto'] ?>" onclick="return confirm('Eliminar producto?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="index.php" class="btn btn-secondary">Volver</a>
</div>
</body>
</html>
