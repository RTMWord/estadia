<?php
require_once __DIR__ . '/_security_check.php';
require_once __DIR__ . '/../../app/config/db.php';

$userId = getUserId();
$stmt = $pdo->prepare('SELECT idContenido, Tipo, Titulo, FechaPublicacion, Activo FROM Contenido WHERE AutorUsuario_id = ? ORDER BY FechaPublicacion DESC');
$stmt->execute([$userId]);
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Mis contenidos - Proveedor</title>
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5 provider-panel">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4">Mis contenidos propuestos</h1>
            <div>
                <a href="index.php" class="btn btn-outline-secondary">Volver</a>
                <a href="contenido_nuevo.php" class="btn btn-primary ms-2">Proponer nuevo</a>
            </div>
        </div>

        <?php if (empty($items)): ?>
            <div class="alert alert-info">No has propuesto contenidos aún.</div>
        <?php else: ?>
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="table-light">
                                <tr><th>ID</th><th>Tipo</th><th>Título</th><th>Fecha</th><th>Activo</th></tr>
                            </thead>
                            <tbody>
                            <?php foreach ($items as $it): ?>
                                <tr>
                                    <td><?= htmlspecialchars($it['idContenido']) ?></td>
                                    <td><?= htmlspecialchars($it['Tipo']) ?></td>
                                    <td><?= htmlspecialchars($it['Titulo']) ?></td>
                                    <td><?= htmlspecialchars($it['FechaPublicacion']) ?></td>
                                    <td><?= $it['Activo'] ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">Pendiente</span>' ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>
