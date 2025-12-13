<?php
require_once '../../app/config/db.php';
require_once '../../app/helpers/auth.php';
requireRole($pdo, 'administrador');

$agencias = $pdo->query('SELECT * FROM Agencia ORDER BY EstadoValidacion, Nombre')->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Agencias - MetaHogar</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
<?php include __DIR__ . '/partials/admin_nav.php'; ?>
    <div class="container py-5">
        <h2 class="text-primary mb-4">Validación de Agencias</h2>
        <a href="agencia_nueva.php" class="btn btn-success mb-3">Registrar Agencia</a>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Contacto</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($agencias as $a): ?>
                <tr>
                    <td><?= $a['idAgencia'] ?></td>
                    <td><?= htmlspecialchars($a['Nombre']) ?></td>
                    <td><?= htmlspecialchars($a['Contacto']) ?></td>
                    <td><?= htmlspecialchars($a['Telefono']) ?></td>
                    <td><?= htmlspecialchars($a['Email']) ?></td>
                    <td><?= $a['EstadoValidacion'] ?></td>
                    <td>
                        <a href="agencia_ver.php?id=<?= $a['idAgencia'] ?>" class="btn btn-sm btn-primary">Ver</a>
                        <a href="../../app/controllers/AgenciaController.php?accion=aprobar&id=<?= $a['idAgencia'] ?>" class="btn btn-sm btn-success">Aprobar</a>
                        <a href="../../app/controllers/AgenciaController.php?accion=rechazar&id=<?= $a['idAgencia'] ?>" class="btn btn-sm btn-danger">Rechazar</a>
                        <a href="../../app/controllers/AgenciaController.php?accion=pendiente&id=<?= $a['idAgencia'] ?>" class="btn btn-sm btn-secondary">Poner Pendiente</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>