<?php
session_start();
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/models/Testimonio.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
requireRole($pdo, 'administrador');

// Acciones: aprobar / eliminar
if (isset($_GET['aprobar'])) {
    Testimonio::aprobar($pdo, $_GET['aprobar'], 1);
    header('Location: testimonios.php'); exit;
}
if (isset($_GET['rechazar'])) {
    Testimonio::aprobar($pdo, $_GET['rechazar'], 0);
    header('Location: testimonios.php'); exit;
}
if (isset($_GET['eliminar'])) {
    Testimonio::eliminar($pdo, $_GET['eliminar']);
    header('Location: testimonios.php'); exit;
}

$list = Testimonio::getAll($pdo);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Administrar Testimonios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h1>Testimonios</h1>
    <p class="text-muted">Gestiona los testimonios enviados por usuarios. Aprueba para que aparezcan en el sitio público.</p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Calificación</th>
                <th>Testimonio</th>
                <th>Fecha</th>
                <th>Aprobado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['idTestimonio']) ?></td>
                    <td><?= htmlspecialchars($row['Nombre']) ?><br><small><?= htmlspecialchars($row['Email']) ?></small></td>
                    <td><?= (int)$row['Calificacion'] ?></td>
                    <td><?= nl2br(htmlspecialchars(substr($row['Testimonio'],0,200))) ?></td>
                    <td><?= htmlspecialchars($row['FechaCreacion']) ?></td>
                    <td><?= $row['Aprobado'] ? 'Sí' : 'No' ?></td>
                    <td>
                        <?php if (!$row['Aprobado']): ?>
                            <a class="btn btn-sm btn-success" href="?aprobar=<?= $row['idTestimonio'] ?>">Aprobar</a>
                        <?php else: ?>
                            <a class="btn btn-sm btn-warning" href="?rechazar=<?= $row['idTestimonio'] ?>">Rechazar</a>
                        <?php endif; ?>
                        <a class="btn btn-sm btn-danger" href="?eliminar=<?= $row['idTestimonio'] ?>" onclick="return confirm('Eliminar testimonio?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="index.php" class="btn btn-secondary">Volver</a>
</div>
</body>
</html>
