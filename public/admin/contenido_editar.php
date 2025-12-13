<?php
session_start();
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/models/Contenido.php';
require_once __DIR__ . '/../../app/models/Multimedia.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
requireRole($pdo, 'administrador');

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: contenidos.php'); exit; }
$item = Contenido::getById($pdo, $id);
if (!$item) { header('Location: contenidos.php'); exit; }

$errors = [];
$successMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
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

        Contenido::editar($pdo, $id, [
            'tipo' => $_POST['tipo'] ?? 'ARTICULO',
            'titulo' => $_POST['titulo'] ?? '',
            'cuerpo' => $_POST['cuerpo'] ?? '',
            'autor' => $_POST['autor'] ?? null,
            'activo' => isset($_POST['activo']),
            'imagen_ruta' => $rutaImagen
        ]);

        if ($rutaImagen) {
            Multimedia::crear($pdo, [
                'contenido_id' => $id,
                'tipo' => 'IMAGEN',
                'ruta' => $rutaImagen,
                'descripcion' => 'Imagen de contenido #' . $id
            ]);
        }

        $successMsg = 'Contenido actualizado correctamente';
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
    <title>Editar contenido</title>
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
    <h1>Editar contenido</h1>
    <?php if (!empty($errors)): ?><div class="alert alert-danger"><?= htmlspecialchars(implode(' | ', $errors)) ?></div><?php endif; ?>
    <?php if (!empty($successMsg)): ?><div class="alert alert-success"><?= htmlspecialchars($successMsg) ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3"><label class="form-label">Tipo</label>
            <select name="tipo" class="form-select">
                <option value="NOTICIA" <?= $item['Tipo']==='NOTICIA' ? 'selected' : '' ?>>NOTICIA</option>
                <option value="ARTICULO" <?= $item['Tipo']==='ARTICULO' ? 'selected' : '' ?>>ARTICULO</option>
                <option value="BLOG" <?= $item['Tipo']==='BLOG' ? 'selected' : '' ?>>BLOG</option>
                <option value="SITIO_INTERES" <?= $item['Tipo']==='SITIO_INTERES' ? 'selected' : '' ?>>SITIO_INTERES</option>
            </select>
        </div>
        <div class="mb-3"><label class="form-label">Título</label><input name="titulo" class="form-control" required value="<?= htmlspecialchars($item['Titulo']) ?>"></div>
        <div class="mb-3"><label class="form-label">Cuerpo</label><textarea name="cuerpo" rows="8" class="form-control"><?= htmlspecialchars($item['Cuerpo']) ?></textarea></div>
        <div class="mb-3">
            <label class="form-label">Imagen principal (opcional)</label>
            <?php if (!empty($item['ImagenPrincipalRuta'])): ?>
                <div class="mb-2"><small class="text-muted">Actual:</small><br><img src="<?= htmlspecialchars($item['ImagenPrincipalRuta']) ?>" alt="Imagen actual" style="max-height:150px" class="border rounded"></div>
            <?php endif; ?>
            <input type="file" name="imagen" class="form-control" accept="image/*">
            <div class="form-text">Formatos permitidos: jpg, jpeg, png, gif, webp</div>
        </div>
        <div class="form-check mb-3"><input type="checkbox" name="activo" id="activo" class="form-check-input" <?= $item['Activo'] ? 'checked' : '' ?>><label for="activo" class="form-check-label">Activo</label></div>
        <div class="d-flex gap-2"><button class="btn btn-success">Guardar</button><a href="contenidos.php" class="btn btn-secondary">Cancelar</a></div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (!empty($successMsg)): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: '<?= addslashes($successMsg) ?>',
        confirmButtonColor: '#3085d6'
    }).then(() => { window.location.href = 'contenidos.php'; });
</script>
<?php endif; ?>
</body>
</html>
