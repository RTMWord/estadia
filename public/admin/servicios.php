<?php
require_once '../../app/config/db.php';
require_once '../../app/models/Servicio.php';

// Obtener la conexión expuesta por db.php ($conn para mysqli, $pdo para PDO)
global $conn, $pdo;
$db = $conn ?? $pdo ?? null;

if ($db === null) {
    error_log('servicios.php: No hay conexión a la base de datos disponible');
    $servicios = [];
} else {
    // Instanciar el modelo (constructor del modelo espera la conexión)
    $servicioModel = new Servicio($db);

    // Preferir el método que exista en tu modelo: probar obtenerActivos([]) o obtenerTodos()
    if (method_exists($servicioModel, 'obtenerActivos')) {
        $servicios = $servicioModel->obtenerActivos([]); // lista pública/activas
    } elseif (method_exists($servicioModel, 'obtenerTodos')) {
        $servicios = $servicioModel->obtenerTodos(); // lista completa (admin)
    } else {
        error_log('servicios.php: El modelo Servicio no tiene obtenerActivos ni obtenerTodos');
        $servicios = [];
    }
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
                        <a href="servicios.php?eliminar=<?= $s['idServicio'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar servicio?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

