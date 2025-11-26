<?php
// public/admin/servicio_editar.php
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
requireRole($pdo, 'administrador');
require_once __DIR__ . '/../../app/controllers/ServicioController.php';
$ctrl = new ServicioController();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: ../servicios.php');
    exit;
}

$servicio = $ctrl->detalle($id);
if (!$servicio) {
    echo "Servicio no encontrado";
    exit;
}

// Obtener agencias para el select
try {
    $stmtAg = $pdo->query('SELECT idAgencia, Nombre FROM agencia ORDER BY Nombre');
    $agencias = $stmtAg->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $agencias = [];
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $res = $ctrl->editar($id, $_POST, $_FILES, $_SESSION);
    if (!empty($res) && !empty($res['ok'])) {
        header('Location: ../detalle_servicio.php?id=' . $id);
        exit;
    } else {
        $errors[] = $res['error'] ?? 'Error actualizando servicio';
    }
}
// Prepare safe form values (avoid undefined index notices)
$formVals = [];
$formVals['titulo'] = $_POST['titulo'] ?? ($servicio['titulo'] ?? '');
$formVals['descripcion_corta'] = $_POST['descripcion_corta'] ?? ($servicio['descripcion_corta'] ?? '');
$formVals['descripcion'] = $_POST['descripcion'] ?? ($servicio['descripcion'] ?? '');
$formVals['categoria'] = $_POST['categoria'] ?? ($servicio['categoria'] ?? '');
$formVals['ubicacion'] = $_POST['ubicacion'] ?? ($servicio['ubicacion'] ?? '');
$formVals['contacto'] = $_POST['contacto'] ?? ($servicio['contacto'] ?? '');
$formVals['precio'] = $_POST['precio'] ?? ($servicio['precio'] ?? '');
$formVals['agencia_id'] = $_POST['agencia'] ?? ($servicio['agencia_id'] ?? '');
$formVals['status'] = isset($_POST['status']) ? 1 : (isset($servicio['status']) ? (int)$servicio['status'] : 0);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Editar Servicio - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<?php include __DIR__ . '/partials/admin_nav.php'; ?>
<div class="container">
    <h1>Editar servicio</h1>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger"><?php foreach ($errors as $e) echo "<div>".htmlspecialchars($e)."</div>"; ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input class="form-control" name="titulo" required value="<?php echo htmlspecialchars($formVals['titulo']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Descripción corta</label>
            <input class="form-control" name="descripcion_corta" value="<?php echo htmlspecialchars($formVals['descripcion_corta']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion" rows="6"><?php echo htmlspecialchars($formVals['descripcion']); ?></textarea>
        </div>
        <div class="row g-2">
            <div class="col-md-4 mb-3">
                <label class="form-label">Categoría</label>
                <input class="form-control" name="categoria" value="<?php echo htmlspecialchars($formVals['categoria']); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Ubicación</label>
                <input class="form-control" name="ubicacion" value="<?php echo htmlspecialchars($formVals['ubicacion']); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Contacto</label>
                <input class="form-control" name="contacto" value="<?php echo htmlspecialchars($formVals['contacto']); ?>">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Precio</label>
            <input class="form-control" name="precio" type="number" step="0.01" value="<?php echo htmlspecialchars($formVals['precio']); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Agencia</label>
            <select name="agencia" class="form-select">
                <option value="">-- Seleccionar agencia --</option>
                <?php foreach ($agencias as $a): ?>
                        <?php $selected = ($formVals['agencia_id'] == $a['idAgencia']); ?>
                        <option value="<?= $a['idAgencia'] ?>" <?= $selected ? 'selected' : ''?>><?= htmlspecialchars($a['Nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="status" id="status" <?php echo ($formVals['status'] == 1) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="status">Activo</label>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">Actualizar</button>
            <a href="../detalle_servicio.php?id=<?php echo $id; ?>" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>
