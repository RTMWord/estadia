<?php
require_once '../../app/config/db.php';
require_once '../../app/helpers/auth.php';
requireRole($pdo, 'administrador');

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: agencias.php'); exit; }
$stmt = $pdo->prepare('SELECT * FROM Agencia WHERE idAgencia = ?');
$stmt->execute([$id]);
$a = $stmt->fetch();
if (!$a) { header('Location: agencias.php'); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Agencia - MetaHogar</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h2 class="text-primary mb-4">Agencia: <?= htmlspecialchars($a['Nombre']) ?></h2>
        <div class="card mb-3">
            <div class="card-body">
                <p><strong>Contacto:</strong> <?= htmlspecialchars($a['Contacto']) ?></p>
                <p><strong>Teléfono:</strong> <?= htmlspecialchars($a['Telefono']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($a['Email']) ?></p>
                <p><strong>Dirección:</strong> <?= htmlspecialchars($a['Direccion']) ?></p>
                <p><strong>Estado de validación:</strong> <?= $a['EstadoValidacion'] ?></p>
            </div>
        </div>
        <div class="mb-3">
            <a href="../../app/controllers/AgenciaController.php?accion=aprobar&id=<?= $a['idAgencia'] ?>" class="btn btn-success">Aprobar</a>
            <a href="../../app/controllers/AgenciaController.php?accion=rechazar&id=<?= $a['idAgencia'] ?>" class="btn btn-danger">Rechazar</a>
            <a href="agencias.php" class="btn btn-secondary">Volver</a>
        </div>
    </div>
</body>
</html>