<?php
session_start();
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/models/Contenido.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
requireRole($pdo, 'administrador');

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: contenidos.php'); exit; }
$item = Contenido::getById($pdo, $id);
if (!$item) { header('Location: contenidos.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Contenido::editar($pdo, $id, [
        'tipo' => $_POST['tipo'] ?? 'ARTICULO',
        'titulo' => $_POST['titulo'] ?? '',
        'cuerpo' => $_POST['cuerpo'] ?? '',
        'autor' => $_POST['autor'] ?? null,
        'activo' => isset($_POST['activo'])
    ]);
    header('Location: contenidos.php'); exit;
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Editar contenido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<?php include __DIR__ . '/partials/admin_nav.php'; ?>
<div class="container">
    <h1>Editar contenido</h1>
    <form method="post">
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
        <div class="form-check mb-3"><input type="checkbox" name="activo" id="activo" class="form-check-input" <?= $item['Activo'] ? 'checked' : '' ?>><label for="activo" class="form-check-label">Activo</label></div>
        <div class="d-flex gap-2"><button class="btn btn-success">Guardar</button><a href="contenidos.php" class="btn btn-secondary">Cancelar</a></div>
    </form>
</div>
</body>
</html>
