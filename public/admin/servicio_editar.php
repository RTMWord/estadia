<?php
// public/admin/servicio_editar.php
session_start();
require_once __DIR__ . '/../../app/controllers/ServicioController.php';
$ctrl = new ServicioController();

if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

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
<div class="container">
    <h1>Editar servicio</h1>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger"><?php foreach ($errors as $e) echo "<div>".htmlspecialchars($e)."</div>"; ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input class="form-control" name="titulo" required value="<?php echo htmlspecialchars($_POST['titulo'] ?? $servicio['titulo']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Descripción corta</label>
            <input class="form-control" name="descripcion_corta" value="<?php echo htmlspecialchars($_POST['descripcion_corta'] ?? $servicio['descripcion_corta']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion" rows="6"><?php echo htmlspecialchars($_POST['descripcion'] ?? $servicio['descripcion']); ?></textarea>
        </div>
        <div class="row g-2">
            <div class="col-md-4 mb-3">
                <label class="form-label">Categoría</label>
                <input class="form-control" name="categoria" value="<?php echo htmlspecialchars($_POST['categoria'] ?? $servicio['categoria']); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Ubicación</label>
                <input class="form-control" name="ubicacion" value="<?php echo htmlspecialchars($_POST['ubicacion'] ?? $servicio['ubicacion']); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Contacto</label>
                <input class="form-control" name="contacto" value="<?php echo htmlspecialchars($_POST['contacto'] ?? $servicio['contacto']); ?>">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Precio</label>
            <input class="form-control" name="precio" type="number" step="0.01" value="<?php echo htmlspecialchars($_POST['precio'] ?? $servicio['precio']); ?>">
        </div>

        <?php if (!empty($servicio['imagen'])): ?>
            <p>Imagen actual:</p>
            <img src="<?php echo htmlspecialchars($servicio['imagen']); ?>" alt="" style="max-width:200px;">
        <?php endif; ?>

        <div class="mb-3">
            <label class="form-label">Cambiar imagen (opcional)</label>
            <input class="form-control" name="imagen" type="file" accept="image/*">
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="status" id="status" <?php echo ($servicio['status'] == 1) ? 'checked' : ''; ?>>
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
