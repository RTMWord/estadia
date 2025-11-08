<?php
require_once '../../app/config/db.php';
require_once '../../app/models/Servicio.php';
require_once '../../app/helpers/auth.php';
requireRole($pdo, 'administrador');

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
</head>
<body>
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
                        <a href="../../app/controllers/ServicioController.php?eliminar=<?= $s['idServicio'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar servicio?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

