<?php
session_start();
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/models/Contenido.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
requireRole($pdo, 'administrador');

// Procesar acciones por POST: eliminar y toggle activo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['eliminar'])) {
        Contenido::eliminar($pdo, $_POST['eliminar']);
        header('Location: contenidos.php'); exit;
    }
    if (!empty($_POST['toggle_active']) && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $activo = isset($_POST['activo']) && (int)$_POST['activo'] === 1 ? 1 : 0;
        Contenido::setActivo($pdo, $id, $activo);
        header('Location: contenidos.php'); exit;
    }
}

$items = Contenido::getAll($pdo);
?>
<!doctype html>
<html lang="es">
</head>
<body>
<?php include __DIR__ . '/partials/admin_nav.php'; ?>
    <div class="container py-5">
    <div class="container py-5">
    <title>Contenidos - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Contenidos</h1>
        <a href="contenido_nuevo.php" class="btn btn-primary">Nuevo contenido</a>
    </div>
    <table class="table table-striped">
        <thead><tr><th>#</th><th>Tipo</th><th>Título</th><th>Fecha</th><th>Activo</th><th>Acciones</th></tr></thead>
        <tbody>
            <?php foreach($items as $r): ?>
            <tr>
                <td><?= $r['idContenido'] ?></td>
                <td><?= htmlspecialchars($r['Tipo']) ?></td>
                <td><?= htmlspecialchars($r['Titulo']) ?></td>
                <td><?= htmlspecialchars($r['FechaPublicacion']) ?></td>
                <td>
                    <form method="post" class="d-inline">
                        <input type="hidden" name="id" value="<?= $r['idContenido'] ?>">
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox" name="activo" value="1" <?= $r['Activo'] ? 'checked' : '' ?> onchange="this.form.submit()" aria-label="Activo">
                        <input type="hidden" name="toggle_active" value="1">
                    </form>
                </td>
                <td>
                    <a class="btn btn-sm btn-secondary" href="contenido_editar.php?id=<?= $r['idContenido'] ?>">Editar</a>
                    <form method="post" class="d-inline" onsubmit="return confirm('Eliminar contenido?')">
                        <input type="hidden" name="eliminar" value="<?= $r['idContenido'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="index.php" class="btn btn-secondary">Volver</a>
</div>
</body>
</html>
