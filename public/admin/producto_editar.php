<?php
session_start();
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/models/Producto.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
requireRole($pdo, 'administrador');

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: productos.php'); exit; }
$item = Producto::getById($pdo, $id);
if (!$item) { header('Location: productos.php'); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Producto::editar($pdo, $id, [
            'nombre' => $_POST['nombre'] ?? '',
            'descripcion' => $_POST['descripcion'] ?? '',
            'precio' => $_POST['precio'] ?? 0,
            'existencia' => $_POST['existencia'] ?? 0,
            'activo' => isset($_POST['activo'])
        ]);
        header('Location: productos.php'); exit;
    } catch (Exception $e) { $errors[] = $e->getMessage(); }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Editar producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h1>Editar producto</h1>
    <?php if (!empty($errors)): ?><div class="alert alert-danger"><?php foreach($errors as $e) echo htmlspecialchars($e).'<br>'; ?></div><?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input name="nombre" class="form-control" required value="<?= htmlspecialchars($item['Nombre']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="4"><?= htmlspecialchars($item['Descripcion']) ?></textarea>
        </div>
        <div class="row g-2 mb-3">
            <div class="col-md-4"><label class="form-label">Precio</label><input name="precio" type="number" step="0.01" class="form-control" value="<?= htmlspecialchars($item['Precio']) ?>"></div>
            <div class="col-md-4"><label class="form-label">Existencia</label><input name="existencia" type="number" class="form-control" value="<?= htmlspecialchars($item['Existencia']) ?>"></div>
            <div class="col-md-4 d-flex align-items-end"><div class="form-check"><input type="checkbox" name="activo" id="activo" class="form-check-input" <?= $item['Activo'] ? 'checked' : '' ?>><label for="activo" class="form-check-label">Activo</label></div></div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success" type="submit">Guardar</button>
            <a href="productos.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>
