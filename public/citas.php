<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/models/Cita.php';
// Obtener todas las citas para la vista pública
$citas = [];
try {
    $citas = Cita::getAll($pdo);
} catch (Exception $e) {
    $citas = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Citas - MetaHogar</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <div class="container py-5">
        <h2 class="text-primary mb-4">Citas Agendadas</h2>
        <a href="cita_nueva.php" class="btn btn-success mb-3">Agendar Nueva Cita</a>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Servicio</th>
                    <th>Fecha y Hora</th>
                    <th>Estado</th>
                    <th>Notas</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($citas as $c): ?>
                <tr>
                    <td><?= $c['idCita'] ?></td>
                    <td><?= $c['Usuario'] ?></td>
                    <td><?= $c['Servicio'] ?></td>
                    <td><?= $c['FechaHora'] ?></td>
                    <td><?= $c['Estado'] ?></td>
                    <td><?= $c['Notas'] ?></td>
                    <td>
                        <a href="cita_editar.php?id=<?= $c['idCita'] ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="citas.php?eliminar=<?= $c['idCita'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar cita?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
