<?php
require_once '../app/config/db.php';
require_once '../app/models/Cita.php';
$id = $_GET['id'] ?? null;
if (!$id) { header('Location: citas.php'); exit; }
$stmt = $pdo->prepare('SELECT * FROM Cita WHERE idCita = ?');
$stmt->execute([$id]);
$cita = $stmt->fetch();
$servicios = $pdo->query('SELECT idServicio, Nombre FROM Servicio WHERE Activo=1')->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cita - MetaHogar</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h2 class="text-primary mb-4">Editar Cita</h2>
        <form method="POST" action="../app/controllers/CitaController.php">
            <input type="hidden" name="id" value="<?= $cita['idCita'] ?>">
            <div class="mb-3">
                <label class="form-label">Servicio</label>
                <select name="servicio" class="form-select" required>
                    <?php foreach ($servicios as $s): ?>
                        <option value="<?= $s['idServicio'] ?>" <?= $cita['Servicio_idServicio'] == $s['idServicio'] ? 'selected' : '' ?>><?= $s['Nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Fecha y Hora</label>
                <input type="datetime-local" name="fechahora" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($cita['FechaHora'])) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="AGENDADA" <?= $cita['Estado'] == 'AGENDADA' ? 'selected' : '' ?>>Agendada</option>
                    <option value="CONFIRMADA" <?= $cita['Estado'] == 'CONFIRMADA' ? 'selected' : '' ?>>Confirmada</option>
                    <option value="CANCELADA" <?= $cita['Estado'] == 'CANCELADA' ? 'selected' : '' ?>>Cancelada</option>
                    <option value="REALIZADA" <?= $cita['Estado'] == 'REALIZADA' ? 'selected' : '' ?>>Realizada</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Notas</label>
                <textarea name="notas" class="form-control" rows="3"><?= $cita['Notas'] ?></textarea>
            </div>
            <button type="submit" name="editar" class="btn btn-warning">Guardar Cambios</button>
            <a href="citas.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
