<?php
session_start();
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/models/Multimedia.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
requireRole($pdo, 'administrador');

$errors = [];
$successMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_FILES['archivo']['name'])) {
        $f = $_FILES['archivo'];
        $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','gif','mp4','pdf'];
        if (!in_array(strtolower($ext), $allowed)) $errors[] = 'Formato no permitido';
        else {
            $dir = __DIR__ . '/../assets/media';
            if (!is_dir($dir)) mkdir($dir,0755,true);
            $new = 'media_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            $dest = $dir . '/' . $new;
            if (move_uploaded_file($f['tmp_name'], $dest)) {
                $tipo = in_array(strtolower($ext), ['mp4']) ? 'VIDEO' : (in_array(strtolower($ext), ['pdf']) ? 'DOCUMENTO' : 'IMAGEN');
                $ruta = 'assets/media/' . $new;
                Multimedia::crear($pdo, ['contenido_id' => null, 'tipo' => $tipo, 'ruta' => $ruta, 'descripcion' => $_POST['descripcion'] ?? '']);
                $successMsg = 'Archivo subido exitosamente';
            } else {
                $errors[] = 'No se pudo mover el archivo';
            }
        }
    } else {
        $errors[] = 'Selecciona un archivo';
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Subir multimedia</title>
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
    <h1>Subir archivo a la galería</h1>
        <?php if (!empty($errors)): ?><div class="alert alert-danger"><?php foreach($errors as $e) echo htmlspecialchars($e).'<br>'; ?></div><?php endif; ?>
        <?php if (!empty($successMsg)): ?><div class="alert alert-success"><?= htmlspecialchars($successMsg) ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3"><label class="form-label">Archivo</label><input type="file" name="archivo" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Descripción</label><input name="descripcion" class="form-control"></div>
        <div class="d-flex gap-2"><button class="btn btn-success">Subir</button><a href="galeria.php" class="btn btn-secondary">Cancelar</a></div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (!empty($successMsg)): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: 'Archivo subido exitosamente',
        confirmButtonColor: '#3085d6'
    }).then(() => { window.location.href = 'galeria.php'; });
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
