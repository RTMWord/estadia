<?php
session_start();
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/models/Contenido.php';
require_once __DIR__ . '/../../app/models/Multimedia.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
requireRole($pdo, 'administrador');

$errors = [];
$successMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $tipo = $_POST['tipo'] ?? 'ARTICULO';

        // Validar y subir imagen (opcional)
        $rutaImagen = null;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Error al subir la imagen');
            }
            $allowed = ['jpg','jpeg','png','gif','webp'];
            $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                throw new Exception('Formato no permitido. Usa jpg, jpeg, png, gif o webp');
            }
            $targetDir = __DIR__ . '/assets/media/contents';
            if (!is_dir($targetDir)) { @mkdir($targetDir, 0755, true); }
            $safeName = preg_replace('/[^a-zA-Z0-9_\.-]/','_', basename($_FILES['imagen']['name']));
            $finalName = time() . '_' . $safeName;
            $targetPath = $targetDir . DIRECTORY_SEPARATOR . $finalName;
            if (!@move_uploaded_file($_FILES['imagen']['tmp_name'], $targetPath)) {
                throw new Exception('No se pudo guardar la imagen');
            }
            $rutaImagen = 'assets/media/contents/' . $finalName; // relativa a /admin
        }

        // Crear contenido
        $contenidoId = Contenido::crear($pdo, [
            'tipo' => $tipo,
            'titulo' => $_POST['titulo'] ?? '',
            'cuerpo' => $_POST['cuerpo'] ?? '',
            'autor' => $_POST['autor'] ?? null,
            'activo' => isset($_POST['activo']),
            'imagen_ruta' => $rutaImagen
        ]);

        // Asociar imagen en multimedia si hubo upload (opcional, mantiene consistencia)
        if ($contenidoId && $rutaImagen) {
            Multimedia::crear($pdo, [
                'contenido_id' => $contenidoId,
                'tipo' => 'IMAGEN',
                'ruta' => $rutaImagen,
                'descripcion' => 'Imagen de contenido #' . $contenidoId
            ]);
        }

        $successMsg = 'Contenido (' . $tipo . ') registrado exitosamente';
    } catch (Exception $e) { $errors[] = $e->getMessage(); }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Nuevo contenido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
    <h1>Nuevo contenido</h1>
    <?php if (!empty($errors)): ?><div class="alert alert-danger"><?php foreach($errors as $e) echo htmlspecialchars($e).'<br>'; ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3"><label class="form-label">Tipo</label>
            <select name="tipo" class="form-select">
                <option value="NOTICIA">NOTICIA</option>
                <option value="ARTICULO" selected>ARTICULO</option>
                <option value="BLOG">BLOG</option>
                <option value="SITIO_INTERES">SITIO_INTERES</option>
            </select>
        </div>
        <div class="mb-3"><label class="form-label">Título</label><input name="titulo" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Cuerpo</label><textarea name="cuerpo" rows="8" class="form-control"></textarea></div>
        <div class="mb-3">
            <label class="form-label">Imagen (opcional)</label>
            <input type="file" name="imagen" class="form-control" accept="image/*">
            <div class="form-text">Formatos permitidos: jpg, jpeg, png, gif, webp</div>
        </div>
        <div class="form-check mb-3"><input type="checkbox" name="activo" id="activo" class="form-check-input" checked><label for="activo" class="form-check-label">Activo</label></div>
        <div class="d-flex gap-2"><button class="btn btn-success">Guardar</button><a href="contenidos.php" class="btn btn-secondary">Cancelar</a></div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (!empty($successMsg)): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Éxito',
    text: '<?= addslashes($successMsg) ?>',
    confirmButtonColor: '#3085d6'
});
</script>
<?php endif; ?>

<?php if (!empty($errors)): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: '<?= addslashes($errors[0]) ?>',
    confirmButtonColor: '#d33'
});
</script>
<?php endif; ?>
</body>
</html>
