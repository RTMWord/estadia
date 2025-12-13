<?php
session_start();
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/models/Producto.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
requireRole($pdo, 'administrador');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $activoVal = isset($_POST['activo']) && $_POST['activo'] === '1' ? 1 : 0;

        // Manejo de subida de imagen
        $rutaImagen = null;
        if (isset($_FILES['ruta_imagen']) && $_FILES['ruta_imagen']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg','jpeg','png','gif','webp'];
            $ext = strtolower(pathinfo($_FILES['ruta_imagen']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                throw new Exception('Formato de imagen no permitido. Usa jpg, jpeg, png, gif o webp');
            }
            $targetDir = __DIR__ . '/assets/media/products';
            if (!is_dir($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }
            $safeName = preg_replace('/[^a-zA-Z0-9_\.-]/','_', basename($_FILES['ruta_imagen']['name']));
            $finalName = time() . '_' . $safeName;
            $targetPath = $targetDir . DIRECTORY_SEPARATOR . $finalName;
            if (!@move_uploaded_file($_FILES['ruta_imagen']['tmp_name'], $targetPath)) {
                throw new Exception('No se pudo guardar la imagen subida.');
            }
            // Guardar ruta relativa desde admin/
            $rutaImagen = 'assets/media/products/' . $finalName;
        }

        $id = Producto::crear($pdo, [
            'nombre' => $_POST['nombre'] ?? '',
            'descripcion' => $_POST['descripcion'] ?? '',
            'precio' => $_POST['precio'] ?? 0,
            'existencia' => $_POST['existencia'] ?? 0,
            'activo' => $activoVal,
            'ruta_imagen' => $rutaImagen
        ]);

        $_SESSION['flash_success'] = 'Producto registrado exitosamente';
        header('Location: productos.php'); exit;
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Nuevo producto</title>
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
    <h1>Agregar producto tecnológico</h1>
    <?php if (!empty($errors)): ?><div class="alert alert-danger"><?php foreach($errors as $e) echo htmlspecialchars($e).'<br>'; ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input name="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="4"></textarea>
        </div>
        <div class="row g-2 mb-3">
            <div class="col-md-4"><label class="form-label">Precio</label><input name="precio" type="number" step="0.01" class="form-control"></div>
            <div class="col-md-4"><label class="form-label">Existencia</label><input name="existencia" type="number" class="form-control"></div>
            <div class="col-md-4 d-flex align-items-end"><div class="form-check">
                <input type="hidden" name="activo" value="0">
                <input type="checkbox" name="activo" id="activo" value="1" class="form-check-input" checked><label for="activo" class="form-check-label">Activo</label>
            </div></div>
        </div>
        <div class="mb-3">
            <label class="form-label">Imagen del producto</label>
            <input type="file" name="ruta_imagen" class="form-control" accept="image/*">
            <div class="form-text">Se guardará la ruta en el campo RutaImagen.</div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success" type="submit">Guardar</button>
            <a href="productos.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>
