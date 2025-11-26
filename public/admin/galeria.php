<?php
session_start();
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/models/Multimedia.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
requireRole($pdo, 'administrador');

if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    $row = Multimedia::getById($pdo, $id);
    if ($row && !empty($row['Ruta'])) {
        $path = __DIR__ . '/../' . $row['Ruta'];
        if (is_file($path)) @unlink($path);
    }
    Multimedia::eliminar($pdo, $id);
    header('Location: galeria.php'); exit;
}

$items = Multimedia::getAll($pdo);
?>
<!doctype html>
<html lang="es">
<head>
<body>
<?php include __DIR__ . '/partials/admin_nav.php'; ?>
    <div class="container py-5">
    <title>Galería - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h1>Galería</h1>
        <a href="galeria_nuevo.php" class="btn btn-primary">Subir archivo</a>
    </div>
    <div class="row g-3">
        <?php foreach($items as $it): ?>
            <div class="col-md-3">
                <div class="card">
                    <?php if ($it['Tipo'] === 'IMAGEN'): ?>
                        <img src="../<?= htmlspecialchars($it['Ruta']) ?>" class="card-img-top" style="height:160px;object-fit:cover">
                    <?php else: ?>
                        <div class="card-body">Tipo: <?= htmlspecialchars($it['Tipo']) ?></div>
                    <?php endif; ?>
                    <div class="card-footer d-flex justify-content-between">
                        <small><?= htmlspecialchars($it['Descripcion']) ?></small>
                        <a class="btn btn-sm btn-danger" href="?eliminar=<?= $it['idMedia'] ?>" onclick="return confirm('Eliminar?')">Eliminar</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <a href="index.php" class="btn btn-secondary mt-3">Volver</a>
</div>
</body>
</html>
