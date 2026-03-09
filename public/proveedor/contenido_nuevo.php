<?php
require_once __DIR__ . '/_security_check.php';
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/models/Contenido.php';

$userId = getUserId();
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'tipo' => $_POST['tipo'] ?? 'ARTICULO',
        'titulo' => $_POST['titulo'] ?? '',
        'cuerpo' => $_POST['cuerpo'] ?? '',
        'autor' => $userId,
        'imagen_ruta' => $_POST['imagen_ruta'] ?? null,
        // Propuesto por proveedor debe ser revisado por admin, dejar Activo = 0
        'activo' => 0
    ];
    $id = Contenido::crear($pdo, $data);
    if ($id) {
        $success = true;
        header('Location: contenidos.php?created=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Proponer contenido - Proveedor</title>
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5 provider-panel">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4">Proponer nuevo contenido</h1>
            <a href="contenidos.php" class="btn btn-outline-secondary">Volver</a>
        </div>

        <?php if (isset($_GET['created'])): ?>
            <div class="alert alert-success">Contenido propuesto correctamente. Espera aprobación del administrador.</div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Tipo</label>
                        <select name="tipo" class="form-select">
                            <option value="NOTICIA">Noticia</option>
                            <option value="ARTICULO" selected>Artículo</option>
                            <option value="BLOG">Blog</option>
                            <option value="SITIO_INTERES">Sitio de interés</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="titulo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cuerpo</label>
                        <textarea name="cuerpo" rows="8" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL imagen (opcional)</label>
                        <input type="text" name="imagen_ruta" class="form-control">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Proponer contenido</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
