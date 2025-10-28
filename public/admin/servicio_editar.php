<?php
require_once '../../app/config/db.php';
require_once '../../app/models/Servicio.php';
$id = $_GET['id'] ?? null;
if (!$id) { header('Location: servicios.php'); exit; }
$stmt = $pdo->prepare('SELECT * FROM Servicio WHERE idServicio = ?');
$stmt->execute([$id]);
$servicio = $stmt->fetch();
$agencias = $pdo->query('SELECT idAgencia, Nombre FROM Agencia')->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Servicio - MetaHogar</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h2 class="text-primary mb-4">Editar Servicio</h2>
        <form method="POST" action="../../app/controllers/ServicioController.php">
            <input type="hidden" name="id" value="<?= $servicio['idServicio'] ?>">
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="<?= $servicio['Nombre'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3" required><?= $servicio['Descripcion'] ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Costo</label>
                <input type="number" step="0.01" name="costo" class="form-control" value="<?= $servicio['Costo'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Agencia</label>
                <select name="agencia" class="form-select">
                    <option value="">Sin agencia</option>
                    <?php foreach ($agencias as $a): ?>
                        <option value="<?= $a['idAgencia'] ?>" <?= $servicio['Agencia_idAgencia'] == $a['idAgencia'] ? 'selected' : '' ?>><?= $a['Nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Activo</label>
                <select name="activo" class="form-select">
                    <option value="1" <?= $servicio['Activo'] ? 'selected' : '' ?>>Sí</option>
                    <option value="0" <?= !$servicio['Activo'] ? 'selected' : '' ?>>No</option>
                </select>
            </div>
            <button type="submit" name="editar" class="btn btn-warning">Guardar Cambios</button>
            <a href="servicios.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>
