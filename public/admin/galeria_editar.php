<?php
session_start();
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/models/Multimedia.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
requireRole($pdo, 'administrador');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: galeria.php'); exit; }
$item = Multimedia::getById($pdo, $id);
if (!$item) { header('Location: galeria.php'); exit; }

$errors = [];
$successMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $descripcion = $_POST['descripcion'] ?? '';
        $ruta = $item['Ruta'];
        $tipo = $item['Tipo'];

        if (!empty($_FILES['archivo']['name'])) {
            $f = $_FILES['archivo'];
            if ($f['error'] !== UPLOAD_ERR_OK) { throw new Exception('Error al subir el archivo'); }
            $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','mp4','pdf','webp'];
            if (!in_array($ext, $allowed)) { throw new Exception('Formato no permitido'); }
            // delete old file
            $oldPath = __DIR__ . '/../' . $ruta;
            if (is_file($oldPath)) { @unlink($oldPath); }
            // save new file
            $dir = __DIR__ . '/../assets/media';
            if (!is_dir($dir)) mkdir($dir,0755,true);
            $new = 'media_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            $dest = $dir . '/' . $new;
            if (!move_uploaded_file($f['tmp_name'], $dest)) { throw new Exception('No se pudo mover el archivo'); }
            $ruta = 'assets/media/' . $new;
            $tipo = in_array($ext, ['mp4']) ? 'VIDEO' : (in_array($ext, ['pdf']) ? 'DOCUMENTO' : 'IMAGEN');
        }

        Multimedia::actualizar($pdo, $id, [
            'tipo' => $tipo,
            'ruta' => $ruta,
            'descripcion' => $descripcion
        ]);
        $successMsg = 'Archivo actualizado exitosamente';
    } catch (Exception $e) { $errors[] = $e->getMessage(); }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Editar multimedia</title>
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
    <h1>Editar archivo</h1>
    <?php if (!empty($errors)): ?><div class="alert alert-danger"><?php foreach($errors as $e) echo htmlspecialchars($e).'<br>'; ?></div><?php endif; ?>
    <?php if (!empty($successMsg)): ?><div class="alert alert-success"><?php echo htmlspecialchars($successMsg); ?></div><?php endif; ?>
    <div class="mb-3">
        <label class="form-label">Actual</label>
        <?php if ($item['Tipo'] === 'IMAGEN'): ?>
            <img src="../<?php echo htmlspecialchars($item['Ruta']); ?>" style="max-height:200px;object-fit:cover" class="border rounded">
        <?php else: ?>
            <div>Tipo: <?php echo htmlspecialchars($item['Tipo']); ?></div>
        <?php endif; ?>
    </div>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3"><label class="form-label">Reemplazar archivo (opcional)</label><input type="file" name="archivo" class="form-control" accept="image/*,video/mp4,application/pdf"></div>
        <div class="mb-3"><label class="form-label">Descripción</label><input name="descripcion" class="form-control" value="<?php echo htmlspecialchars($item['Descripcion']); ?>"></div>
        <div class="d-flex gap-2"><button class="btn btn-success">Guardar</button><a href="galeria.php" class="btn btn-secondary">Cancelar</a></div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (!empty($successMsg)): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Éxito',
    text: 'Archivo actualizado exitosamente',
    confirmButtonColor: '#3085d6'
}).then(() => { window.location.href = 'galeria.php'; });
</script>
<?php endif; ?>
<?php if (!empty($errors)): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: '<?php echo addslashes($errors[0]); ?>',
    confirmButtonColor: '#d33'
});
</script>
<?php endif; ?>
</body>
</html>
