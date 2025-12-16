<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/models/Contenido.php';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Artículos - MetaHogar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/bs-navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
    <div class="row">
        <div class="col-lg-8">
            <h1 class="mb-4">Artículos</h1>

            <?php
            $items = Contenido::getAll($pdo);
            $shown = 0;
            foreach ($items as $it):
                if (empty($it['Activo'])) continue;
                $itemTipo = isset($it['Tipo']) ? strtoupper($it['Tipo']) : '';
                if ($itemTipo !== 'ARTICULO') continue;
                $shown++;
            ?>
            <article class="card mb-4">
                <div class="card-body">
                    <h3 class="card-title"><a href="blog_post.php?id=<?= $it['idContenido'] ?>"><?= htmlspecialchars($it['Titulo']) ?></a></h3>
                    <div class="text-muted small mb-2"><?= htmlspecialchars($it['FechaPublicacion']) ?></div>
                    <p class="card-text">
                        <?php
                        $plain = strip_tags($it['Cuerpo'] ?? '');
                        if (mb_strlen($plain) > 320) {
                            echo htmlspecialchars(mb_substr($plain, 0, 320)) . '...';
                        } else {
                            echo htmlspecialchars($plain);
                        }
                        ?>
                    </p>
                    <a href="blog_post.php?id=<?= $it['idContenido'] ?>" class="btn btn-sm btn-primary">Leer más</a>
                </div>
            </article>
            <?php endforeach; ?>

            <?php if ($shown === 0): ?>
                <div class="alert alert-info">No hay artículos publicados todavía.</div>
            <?php endif; ?>

        </div>
        <aside class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5>Últimos artículos</h5>
                    <ul class="list-unstyled mb-0">
                        <?php
                        $count = 0;
                        foreach ($items as $it) {
                            if (empty($it['Activo'])) continue;
                            $itemTipo = isset($it['Tipo']) ? strtoupper($it['Tipo']) : '';
                            if ($itemTipo !== 'ARTICULO') continue;
                            $count++;
                            if ($count > 5) break;
                            echo '<li><a href="blog_post.php?id=' . $it['idContenido'] . '">' . htmlspecialchars($it['Titulo']) . '</a></li>';
                        }
                        if ($count === 0) echo '<li class="text-muted">Sin artículos</li>';
                        ?>
                    </ul>
                </div>
            </div>
        </aside>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
