<?php
require_once '../../app/config/db.php';
require_once '../../app/models/Usuario.php';
$id = $_GET['id'] ?? null;
if (!$id) { header('Location: usuarios.php'); exit; }
$stmt = $pdo->prepare('SELECT * FROM Usuario WHERE idUsuario = ?');
$stmt->execute([$id]);
$usuario = $stmt->fetch();
$stmt2 = $pdo->prepare('SELECT Rol_idRol FROM UsuarioRol WHERE Usuario_idUsuario = ?');
$stmt2->execute([$id]);
$rolActual = $stmt2->fetchColumn();
$roles = $pdo->query('SELECT * FROM Rol')->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario - MetaHogar</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h2 class="text-primary mb-4">Editar Usuario</h2>
        <form method="POST" action="../../app/controllers/UserController.php">
            <input type="hidden" name="id" value="<?= $usuario['idUsuario'] ?>">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="<?= $usuario['Nombre'] ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Apellido Paterno</label>
                    <input type="text" name="apellidop" class="form-control" value="<?= $usuario['ApellidoP'] ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Apellido Materno</label>
                    <input type="text" name="apellidom" class="form-control" value="<?= $usuario['ApellidoM'] ?>">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="email" class="form-control" value="<?= $usuario['Email'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="<?= $usuario['Telefono'] ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Tipo</label>
                <select name="tipo" class="form-select">
                    <option value="externo" <?= $usuario['Tipo'] == 'externo' ? 'selected' : '' ?>>Externo</option>
                    <option value="interno" <?= $usuario['Tipo'] == 'interno' ? 'selected' : '' ?>>Interno</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Activo</label>
                <select name="activo" class="form-select">
                    <option value="1" <?= $usuario['Activo'] ? 'selected' : '' ?>>Sí</option>
                    <option value="0" <?= !$usuario['Activo'] ? 'selected' : '' ?>>No</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Rol</label>
                <select name="rol" class="form-select" required>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?= $rol['idRol'] ?>" <?= $rolActual == $rol['idRol'] ? 'selected' : '' ?>><?= $rol['Nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="editar" class="btn btn-warning">Guardar Cambios</button>
            <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>
