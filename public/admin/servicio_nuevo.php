<?php
// public/admin/servicio_nuevo.php
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
requireRole($pdo, 'administrador');
require_once __DIR__ . '/../../app/controllers/ServicioController.php';
$ctrl = new ServicioController();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $res = $ctrl->crear($_POST, $_FILES, $_SESSION);
    if (!empty($res) && !empty($res['ok'])) {
        header('Location: ../servicios.php');
        exit;
    } else {
        $errors[] = $res['error'] ?? 'Error al crear servicio';
    }
}
// Obtener lista de agencias para el select
try {
    $stmtAg = $pdo->query('SELECT idAgencia, Nombre FROM agencia ORDER BY Nombre');
    $agencias = $stmtAg->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $agencias = [];
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Agregar Servicio - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<?php include __DIR__ . '/partials/admin_nav.php'; ?>
<div class="container">
    <h1>Agregar nuevo servicio</h1>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger"><?php foreach ($errors as $e) echo "<div>".htmlspecialchars($e)."</div>"; ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input class="form-control" name="titulo" required value="<?php echo htmlspecialchars($_POST['titulo'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Descripción corta</label>
            <input class="form-control" name="descripcion_corta" value="<?php echo htmlspecialchars($_POST['descripcion_corta'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion" rows="6"><?php echo htmlspecialchars($_POST['descripcion'] ?? ''); ?></textarea>
        </div>
        <div class="row g-2">
            <div class="col-md-4 mb-3">
                <label class="form-label">Categoría</label>
                <input class="form-control" name="categoria" value="<?php echo htmlspecialchars($_POST['categoria'] ?? ''); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Ubicación</label>
                <input class="form-control" name="ubicacion" value="<?php echo htmlspecialchars($_POST['ubicacion'] ?? ''); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Contacto</label>
                <input class="form-control" name="contacto" value="<?php echo htmlspecialchars($_POST['contacto'] ?? ''); ?>">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Agencia</label>
            <select name="agencia" class="form-select">
                <option value="">-- Seleccionar agencia --</option>
                <?php foreach ($agencias as $a): ?>
                    <option value="<?= $a['idAgencia'] ?>" <?= (isset($_POST['agencia']) && $_POST['agencia'] == $a['idAgencia']) ? 'selected' : ''?>><?= htmlspecialchars($a['Nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Precio</label>
            <input class="form-control" name="precio" type="number" step="0.01" value="<?php echo htmlspecialchars($_POST['precio'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Imagen</label>
            <input class="form-control" name="imagen" type="file" accept="image/*">
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="status" id="status" checked>
            <label class="form-check-label" for="status">Activo</label>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success" type="submit">Guardar</button>
            <a href="../servicios.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>
