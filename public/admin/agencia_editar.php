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
    <meta charset="utf-8">
    <title>Editar Agencia - MetaHogar</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/partials/admin_nav.php'; ?>
    <div class="container py-5">
        <h2 class="mb-4">Editar Agencia</h2>
        <form method="post" action="../../app/controllers/AgenciaController.php">
            <input type="hidden" name="id" value="<?= $a['idAgencia'] ?>">
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input name="nombre" class="form-control" value="<?= htmlspecialchars($a['Nombre']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contacto</label>
                <input name="contacto" class="form-control" value="<?= htmlspecialchars($a['Contacto']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Teléfono</label>
                <input name="telefono" class="form-control" value="<?= htmlspecialchars($a['Telefono']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input name="email" type="email" class="form-control" value="<?= htmlspecialchars($a['Email']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Dirección</label>
                <input name="direccion" class="form-control" value="<?= htmlspecialchars($a['Direccion']) ?>">
            </div>
            <button name="editar" class="btn btn-primary">Guardar</button>
            <a href="agencias.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>