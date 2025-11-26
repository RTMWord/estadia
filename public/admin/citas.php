<?php
require_once '../../app/config/db.php';
require_once '../../app/models/Cita.php';
require_once '../../app/helpers/auth.php';
requireRole($pdo, 'administrador');
$citas = Cita::getAll($pdo);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Citas - MetaHogar</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/partials/admin_nav.php'; ?>
    <div class="container py-5">
        <h2 class="text-primary mb-4">Panel de Citas</h2>
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
                        <a href="../cita_editar.php?id=<?= $c['idCita'] ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="citas.php?eliminar=<?= $c['idCita'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar cita?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
