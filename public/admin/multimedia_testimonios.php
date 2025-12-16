<?php
session_start();
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/models/Multimedia.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
requireRole($pdo, 'administrador');

// Asegurar existencia de la tabla de asociación
$pdo->exec("CREATE TABLE IF NOT EXISTS carousel_testimonios (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    multimedia_id INT NOT NULL,
    orden INT DEFAULT 0,
    activo TINYINT(1) DEFAULT 1,
    UNIQUE KEY uq_multimedia (multimedia_id)
)");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Esperamos un array 'selected' con ids y opcional 'order_{id}'
    $selected = isset($_POST['selected']) && is_array($_POST['selected']) ? array_map('intval', $_POST['selected']) : [];

    // Simplificar: eliminamos todos y reinsertamos según el orden enviado
    $pdo->beginTransaction();
    $pdo->exec('DELETE FROM carousel_testimonios');
    $stmt = $pdo->prepare('INSERT INTO carousel_testimonios (multimedia_id, orden, activo) VALUES (?, ?, 1)');
    $pos = 0;
    foreach ($selected as $id) {
        $orden = isset($_POST['order_' . $id]) ? (int)$_POST['order_' . $id] : $pos;
        $stmt->execute([$id, $orden]);
        $pos++;
    }
    $pdo->commit();
    header('Location: multimedia_testimonios.php'); exit;
}

$all = Multimedia::getAll($pdo);

// Obtener seleccionados actuales
$selStmt = $pdo->query('SELECT multimedia_id, orden FROM carousel_testimonios WHERE activo = 1');
$selectedMap = [];
foreach ($selStmt->fetchAll() as $r) $selectedMap[$r['multimedia_id']] = $r['orden'];

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Multimedia para Testimonios - Admin</title>
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
    <div class="d-flex justify-content-between mb-3">
        <h1>Imágenes del Carrusel (Testimonios)</h1>
        <div>
            <a href="galeria.php" class="btn btn-outline-secondary">Ir a Galería</a>
            <a href="index.php" class="btn btn-secondary">Volver</a>
        </div>
    </div>

    <form method="post">
        <div class="row g-3">
            <?php foreach ($all as $it): $id = $it['idMedia']; ?>
                <div class="col-md-3">
                    <div class="card">
                        <?php if ($it['Tipo'] === 'IMAGEN'): ?>
                            <img src="../<?= htmlspecialchars($it['Ruta']) ?>" class="card-img-top" style="height:160px;object-fit:cover">
                        <?php else: ?>
                            <div class="card-body">Tipo: <?= htmlspecialchars($it['Tipo']) ?></div>
                        <?php endif; ?>
                        <div class="card-footer">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="selected[]" value="<?= $id ?>" id="sel_<?= $id ?>" <?= isset($selectedMap[$id])? 'checked':'' ?> >
                                <label class="form-check-label" for="sel_<?= $id ?>">Incluir</label>
                            </div>
                            <div class="mt-2">
                                <label class="form-label small">Orden</label>
                                <input class="form-control form-control-sm" type="number" name="order_<?= $id ?>" value="<?= htmlspecialchars($selectedMap[$id] ?? '') ?>">
                            </div>
                            <div class="mt-2"><small><?= htmlspecialchars($it['Descripcion']) ?></small></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-4">
            <button class="btn btn-primary">Guardar selección</button>
        </div>
    </form>

</div>
</body>
</html>
