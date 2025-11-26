<?php
require_once '../../app/config/db.php';
require_once '../../app/helpers/auth.php';
requireRole($pdo, 'administrador');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Registrar Agencia - MetaHogar</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/partials/admin_nav.php'; ?>
    <div class="container py-5">
        <h2 class="mb-4">Registrar Nueva Agencia</h2>
        <form method="post" action="../../app/controllers/AgenciaController.php">
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input name="nombre" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contacto</label>
                <input name="contacto" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Teléfono</label>
                <input name="telefono" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input name="email" type="email" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Dirección</label>
                <input name="direccion" class="form-control">
            </div>
            <button name="crear" class="btn btn-primary">Crear</button>
            <a href="agencias.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>