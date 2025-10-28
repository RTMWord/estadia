<?php
require_once '../app/config/db.php';
// Obtener usuarios y servicios
$usuarios = $pdo->query('SELECT idUsuario, Nombre FROM Usuario WHERE Activo=1')->fetchAll();
$servicios = $pdo->query('SELECT idServicio, Nombre FROM Servicio WHERE Activo=1')->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agendar Nueva Cita - MetaHogar</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h2 class="text-primary mb-4">Agendar Nueva Cita</h2>
        <form method="POST" action="../app/controllers/CitaController.php">
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <select name="usuario" class="form-select" required>
                    <?php foreach ($usuarios as $u): ?>
                        <option value="<?= $u['idUsuario'] ?>"><?= $u['Nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Servicio</label>
                <select name="servicio" class="form-select" required>
                    <?php foreach ($servicios as $s): ?>
                        <option value="<?= $s['idServicio'] ?>"><?= $s['Nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Fecha y Hora</label>
                <input type="datetime-local" name="fechahora" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="AGENDADA">Agendada</option>
                    <option value="CONFIRMADA">Confirmada</option>
                    <option value="CANCELADA">Cancelada</option>
                    <option value="REALIZADA">Realizada</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Notas</label>
                <textarea name="notas" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" name="crear" class="btn btn-success">Agendar Cita</button>
            <a href="citas.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>
