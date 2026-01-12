<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/models/Contenido.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$item = Contenido::getById($pdo, $id);
if (!$item || empty($item['Activo'])) {
    http_response_code(404);
    echo "<h1>Noticia no encontrada</h1>";
    exit;
}
// Validar tipo
if (isset($item['Tipo']) && strtoupper($item['Tipo']) !== 'NOTICIA') {
    http_response_code(404);
    echo "<h1>Noticia no encontrada</h1>";
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($item['Titulo']) ?> - MetaHogar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
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
<body>
<?php include __DIR__ . '/partials/bs-navbar.php'; ?>

<main class="container py-5 mt-4">
    <article class="mx-auto" style="max-width:900px;">
        <h1 class="mb-3"><?= htmlspecialchars($item['Titulo']) ?></h1>
        <div class="text-muted mb-4 small"><?= htmlspecialchars($item['FechaPublicacion']) ?></div>
        <div class="mb-5">
            <?php
            // Mostrar el cuerpo tal cual (asumimos que el admin controla HTML)
            echo $item['Cuerpo'];
            ?>
        </div>
        <a href="noticias.php" class="btn btn-secondary">Volver a Noticias</a>
    </article>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
