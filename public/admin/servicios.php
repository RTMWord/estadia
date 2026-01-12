<?php
require_once '../../app/config/db.php';
require_once '../../app/models/Servicio.php';
require_once '../../app/helpers/auth.php';
requireRole($pdo, 'administrador');

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Usar PDO directamente para listar servicios (evita llamar a métodos inexistentes)
try {
    $sql = "SELECT s.idServicio, s.Nombre, s.Descripcion, s.Costo, a.Nombre AS Agencia, s.Activo
            FROM servicio s
            LEFT JOIN agencia a ON s.Agencia_idAgencia = a.idAgencia
            ORDER BY s.idServicio DESC";
    $stmt = $pdo->query($sql);
    $servicios = $stmt->fetchAll();
} catch (Exception $e) {
    $servicios = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Servicios - MetaHogar</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
<body>
<?php include __DIR__ . '/partials/admin_nav.php'; ?>
    <div class="container py-5">
        <h2 class="text-primary mb-4">Panel de Servicios</h2>
        <a href="servicio_nuevo.php" class="btn btn-success mb-3">Nuevo Servicio</a>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Costo</th>
                    <th>Agencia</th>
                    <th>Activo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servicios as $s): ?>
                <tr>
                    <td><?= $s['idServicio'] ?></td>
                    <td><?= $s['Nombre'] ?></td>
                    <td><?= $s['Descripcion'] ?></td>
                    <td>$<?= number_format($s['Costo'],2) ?></td>
                    <td><?= $s['Agencia'] ?></td>
                    <td><?= $s['Activo'] ? 'Sí' : 'No' ?></td>
                    <td>
                        <a href="servicio_editar.php?id=<?= $s['idServicio'] ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="servicio_eliminar.php?id=<?= $s['idServicio'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar servicio?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php if ($flashSuccess): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: '<?= addslashes($flashError) ?>',
    confirmButtonColor: '#d33',
});
</script>
<?php endif; ?>

