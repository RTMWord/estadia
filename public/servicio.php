<?php
require_once 'app/config/db.php';
$id = $_GET['id'] ?? null;
if (!$id) { header('Location: servicios.php'); exit; }
$stmt = $pdo->prepare('SELECT s.*, a.Nombre AS Agencia FROM Servicio s LEFT JOIN Agencia a ON s.Agencia_idAgencia = a.idAgencia WHERE s.idServicio = ? LIMIT 1');
$stmt->execute([$id]);
$servicio = $stmt->fetch();
if (!$servicio) { header('Location: servicios.php'); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($servicio['Nombre']) ?> - MetaHogar</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Widget de Accesibilidad -->
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
    <div class="container py-5">
        <h2 class="text-primary mb-3"><?= htmlspecialchars($servicio['Nombre']) ?></h2>
        <p><strong>Agencia:</strong> <?= htmlspecialchars($servicio['Agencia']) ?></p>
        <p><strong>Costo:</strong> $<?= number_format($servicio['Costo'],2) ?></p>
        <div class="card mb-4">
            <div class="card-body">
                <p><?= nl2br(htmlspecialchars($servicio['Descripcion'])) ?></p>
            </div>
        </div>
        <a href="servicios.php" class="btn btn-secondary">Volver al catálogo</a>
    </div>
    <?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
