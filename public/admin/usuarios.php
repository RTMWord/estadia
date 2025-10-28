<?php
require_once '../../app/config/db.php';
require_once '../../app/models/Usuario.php';
$usuarios = Usuario::getAll($pdo);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Usuarios - MetaHogar</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h2 class="text-primary mb-4">Gestión de Usuarios</h2>
        <a href="usuario_nuevo.php" class="btn btn-success mb-3">Nuevo Usuario</a>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Activo</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= $u['idUsuario'] ?></td>
                    <td><?= $u['Nombre'] ?> <?= $u['ApellidoP'] ?> <?= $u['ApellidoM'] ?></td>
                    <td><?= $u['Email'] ?></td>
                    <td><?= $u['Telefono'] ?></td>
                    <td><?= $u['Activo'] ? 'Sí' : 'No' ?></td>
                    <td><?= $u['Rol'] ?></td>
                    <td>
                        <a href="usuario_editar.php?id=<?= $u['idUsuario'] ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="usuario_eliminar.php?id=<?= $u['idUsuario'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar usuario?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
