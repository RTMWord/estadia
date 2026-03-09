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

// Diagnóstico directo: comprobar que la fila existe en la BD y qué devuelve el controlador
try {
    if (!empty($pdo)) {
        $stmtD = $pdo->prepare('SELECT idServicio, Nombre, Descripcion, Imagen, Activo FROM servicio WHERE idServicio = ? LIMIT 1');
        $stmtD->execute([$id]);
        $direct = $stmtD->fetch(PDO::FETCH_ASSOC);
        echo "<!-- DIAGNOSTICO: consulta directa a BD -->\n";
        if ($direct) {
            echo "<!-- BD fila encontrada: " . htmlspecialchars(json_encode($direct)) . " -->\n";
        } else {
            echo "<!-- BD fila NO encontrada para id=$id -->\n";
        }
    } else {
        echo "<!-- DIAGNOSTICO: \$pdo no disponible -->\n";
    }
} catch (Exception $e) {
    echo "<!-- DIAGNOSTICO: error al consultar BD: " . htmlspecialchars($e->getMessage()) . " -->\n";
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
        header('Location: servicios.php');
        exit;
    } else {
        $errors[] = $res['error'] ?? 'Error actualizando servicio';
    }
}
$formVals = [];
$formVals['titulo'] = $_POST['titulo'] ?? ($servicio['titulo'] ?? '');
$formVals['descripcion'] = $_POST['descripcion'] ?? ($servicio['descripcion'] ?? '');
$formVals['precio'] = $_POST['precio'] ?? ($servicio['precio'] ?? '');
$formVals['agencia_id'] = $_POST['agencia'] ?? ($servicio['agencia_id'] ?? '');
$formVals['status'] = isset($_POST['status']) ? 1 : (isset($servicio['status']) ? (int)$servicio['status'] : 0);
$formVals['imagen'] = $_POST['imagen'] ?? ($servicio['imagen'] ?? '');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Editar Servicio - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.userway.org/widget.js" data-account="kjnkkEfZy1"></script>
    <style>
        .userway-icon {
            position: fixed !important;
            right: 20px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            z-index: 9999 !important;
        }
    </style>
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
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion" rows="6"><?php echo htmlspecialchars($formVals['descripcion']); ?></textarea>
        </div>
        <!-- contacto eliminado -->
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

        <div class="mb-3">
            <label class="form-label">Imagen actual</label>
            <div>
                <?php if (!empty($formVals['imagen'])): ?>
                    <img src="../assets/img/servicios/<?= htmlspecialchars($formVals['imagen']) ?>" alt="Imagen servicio" style="max-width:220px; display:block; margin-bottom:10px; border-radius:6px;">
                <?php else: ?>
                    <div class="text-muted">No hay imagen establecida.</div>
                <?php endif; ?>
            </div>
            <div class="form-text mb-2">Sube una nueva imagen para reemplazar la existente, o marca eliminar.</div>
            <input type="file" name="imagen" accept="image/*" class="form-control mb-2">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
                <label class="form-check-label" for="remove_image">Eliminar imagen actual</label>
            </div>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="status" id="status" <?php echo ($formVals['status'] == 1) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="status">Activo</label>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">Actualizar</button>
            <a href="servicios.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>
