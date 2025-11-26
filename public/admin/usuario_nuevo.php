<?php
require_once '../../app/config/db.php';
require_once '../../app/models/Usuario.php';
// Obtener roles
$roles = $pdo->query('SELECT * FROM Rol')->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Usuario - MetaHogar</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/partials/admin_nav.php'; ?>
    <div class="container py-5">
        <h2 class="text-primary mb-4">Crear Nuevo Usuario</h2>
        <form method="POST" action="../../app/controllers/UserController.php">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Apellido Paterno</label>
                    <input type="text" name="apellidop" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Apellido Materno</label>
                    <input type="text" name="apellidom" class="form-control">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Tipo</label>
                <select name="tipo" class="form-select">
                    <option value="externo">Externo</option>
                    <option value="interno">Interno</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Activo</label>
                <select name="activo" class="form-select">
                    <option value="1">Sí</option>
                    <option value="0">No</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Rol</label>
                <select name="rol" class="form-select" required>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?= $rol['idRol'] ?>"><?= $rol['Nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="crear" class="btn btn-success">Crear Usuario</button>
            <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>
