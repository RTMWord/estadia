<?php
require_once '../app/config/db.php';
require_once '../app/models/Servicio.php';
$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $stmt = $pdo->prepare("SELECT s.*, a.Nombre AS Agencia FROM Servicio s LEFT JOIN Agencia a ON s.Agencia_idAgencia = a.idAgencia WHERE s.Activo=1 AND (s.Nombre LIKE ? OR s.Descripcion LIKE ?) ORDER BY s.Nombre");
    $like = "%" . $q . "%";
    $stmt->execute([$like, $like]);
    $servicios = $stmt->fetchAll();
} else {
    $servicios = Servicio::getAll($pdo);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de Servicios - MetaHogar</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h2 class="text-primary mb-4">Servicios Disponibles</h2>
        <div class="row">
            <?php foreach ($servicios as $s): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title text-primary"><?= $s['Nombre'] ?></h5>
                        <p class="card-text"><?= $s['Descripcion'] ?></p>
                        <p><strong>Costo:</strong> $<?= number_format($s['Costo'],2) ?></p>
                        <p><strong>Agencia:</strong> <?= $s['Agencia'] ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
