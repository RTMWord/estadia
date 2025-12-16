<?php
require_once '../../app/config/db.php';
require_once '../../app/models/Sugerencia.php';
require_once '../../app/helpers/auth.php';
requireRole($pdo, 'administrador');
$sugerencias = Sugerencia::getAll($pdo);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Sugerencias - MetaHogar</title>
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
        <h2 class="text-primary mb-4">Buzón de Sugerencias - Administrador</h2>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Usuario</th>
                    <th>Correo</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sugerencias as $s): ?>
                <tr>
                    <td><?= $s['idSugerencia'] ?></td>
                    <td><?= htmlspecialchars($s['Titulo']) ?></td>
                    <td><?= htmlspecialchars($s['UsuarioNombre'] ?? 'Anónimo') ?></td>
                    <td><?= htmlspecialchars($s['UsuarioEmail'] ?? '') ?></td>
                    <td><?= $s['Estado'] ?></td>
                    <td><?= $s['FechaRegistro'] ?></td>
                    <td>
                        <a href="sugerencia_responder.php?id=<?= $s['idSugerencia'] ?>" class="btn btn-sm btn-primary">Ver / Responder</a>
                        <a href="../../app/controllers/SugerenciaController.php?eliminar=<?= $s['idSugerencia'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar sugerencia?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
