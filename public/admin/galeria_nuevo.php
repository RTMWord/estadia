<?php
session_start();
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/models/Multimedia.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
requireRole($pdo, 'administrador');

$errors = [];
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
                header('Location: galeria.php'); exit;
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
</head>
<body class="p-4">
<div class="container">
    <h1>Subir archivo a la galería</h1>
    <?php if (!empty($errors)): ?><div class="alert alert-danger"><?php foreach($errors as $e) echo htmlspecialchars($e).'<br>'; ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3"><label class="form-label">Archivo</label><input type="file" name="archivo" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Descripción</label><input name="descripcion" class="form-control"></div>
        <div class="d-flex gap-2"><button class="btn btn-success">Subir</button><a href="galeria.php" class="btn btn-secondary">Cancelar</a></div>
    </form>
</div>
</body>
</html>
