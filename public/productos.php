<?php
require_once '../app/config/db.php';
require_once '../app/models/Producto.php';
require_once '../app/helpers/auth.php';

$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $stmt = $pdo->prepare("SELECT idProducto, Nombre, Descripcion, Precio, Existencia, Activo, RutaImagen FROM producto WHERE Activo=1 AND (Nombre LIKE ? OR Descripcion LIKE ?) ORDER BY Nombre");
    $like = "%" . $q . "%";
    $stmt->execute([$like, $like]);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $pdo->query("SELECT idProducto, Nombre, Descripcion, Precio, Existencia, Activo, RutaImagen FROM producto WHERE Activo=1 ORDER BY idProducto DESC");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Verificar si el usuario es administrador (para mostrar botón al panel)
$isAdmin = false;
if (isLogged()) {
    try {
        $stm = $pdo->prepare('SELECT r.Nombre FROM UsuarioRol ur JOIN Rol r ON ur.Rol_idRol = r.idRol WHERE ur.Usuario_idUsuario = ? LIMIT 1');
        $stm->execute([getUserId()]);
        $rol = $stm->fetchColumn();
        if ($rol === 'administrador' || $rol === 'admin') $isAdmin = true;
    } catch (Exception $e) {
        // ignore
    }
}

// Permitir que el footer detecte contexto
if (!defined('ESTADIA_INIT')) define('ESTADIA_INIT', true);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos - MetaHogar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pbVj8K9QkM+6v6b1K0qzQe8hVYqvZl+Q0Yb1uR2r6dQeXo1Kjv0oJq2FJXr6g3bKXJ3y0KqO1s0V3QZb4+5Qmw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/catalogo.css">
    <link rel="stylesheet" href="assets/css/bs-navbar.css">
    
    <!-- Widget de Accesibilidad -->
    <script src="https://cdn.userway.org/widget.js" data-account="kjnkkEfZy1"></script>
    
    <style>
        .userway-icon {
            position: fixed !important;
            right: 20px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            z-index: 9999 !important;
        }
        body { font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; }
        .product-card { border: none; overflow: hidden; border-radius: 12px; transition: transform .18s ease, box-shadow .18s ease; }
        .product-card:hover { transform: translateY(-6px); box-shadow: 0 10px 30px rgba(0,0,0,0.12); }
        .product-img-wrapper { position: relative; background: #ffffffff; display:flex; align-items:center; justify-content:center; height:220px; overflow:hidden; }
        .product-img-wrapper img { max-width:100%; max-height:100%; object-fit:cover; display:block; }
        .price-badge { position: absolute; top: 10px; right: 10px; background: linear-gradient(180deg,#ff8b00,#ff5a00); color: #fff; padding:6px 10px; border-radius:8px; font-weight:700; font-size:0.95rem; box-shadow: 0 6px 18px rgba(255,90,0,0.18); }
        .product-title { font-size:1.05rem; color:#0d6efd; margin-bottom:6px; font-weight:600; }
        .product-desc { color:#6c757d; font-size:0.92rem; line-height:1.25; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; }
        .product-meta i{ margin-right:6px; }
        .badge-inactive { position: absolute; left: 10px; top: 10px; background: rgba(0,0,0,0.6); color:#fff; border-radius:6px; padding:4px 8px; font-size:0.8rem; }
        .card-body .mt-auto { margin-top: 12px !important; }
        .price-old { text-decoration:line-through; color:#adb5bd; font-size:0.85rem; margin-right:8px; }
    </style>
</head>
<body>
    <?php
    // Forzar la navbar a azul sólido
    $navbarSolid = true;
    include __DIR__ . '/partials/bs-navbar.php';
    ?>

    <div class="container py-5">
        <?php if ($isAdmin): ?>
            <div class="mb-3 text-end">
                <a href="admin/productos.php" class="btn btn-sm btn-outline-primary">Panel Admin</a>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary">Productos Disponibles</h2>
            <form method="get" class="d-flex" role="search">
                <input name="q" class="form-control me-2" type="search" placeholder="Buscar producto..." aria-label="Buscar" value="<?= htmlspecialchars($q) ?>">
                <button class="btn btn-outline-secondary" type="submit">Buscar</button>
            </form>
        </div>

        <div class="alert alert-info d-flex align-items-center justify-content-between" role="note">
            <div>
                <strong>¿Buscas otra cosa?</strong> Te pueden interesar nuestros servicios especializados.
            </div>
            <a href="servicios.php" class="btn btn-sm btn-primary">Ver servicios</a>
        </div>

        <?php if (empty($productos)): ?>
            <div class="alert alert-warning">
                <?php if ($q !== ''): ?>
                    No encontramos productos que coincidan con "<?= htmlspecialchars($q) ?>".
                <?php else: ?>
                    No hay productos disponibles por el momento.
                <?php endif; ?>
            </div>
            <div class="text-center mb-4">
                <p class="mb-2">¿Necesitas soporte o una solución distinta?</p>
                <a href="servicios.php" class="btn btn-primary">Explorar servicios</a>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($productos as $p): ?>
                    <div class="col">
                        <div class="card product-card h-100 shadow-sm">
                            <div class="product-img-wrapper">
                                <?php
                                    $ruta = $p['RutaImagen'] ?? '';
                                    // Normalizar ruta: si viene como 'assets/...', prefijar 'admin/' para acceder desde public
                                    if ($ruta) {
                                        if (str_starts_with($ruta, 'assets/')) {
                                            $rutaWeb = 'admin/' . $ruta;
                                        } else {
                                            $rutaWeb = $ruta;
                                        }
                                    } else {
                                        $rutaWeb = 'assets/img/product-placeholder.png';
                                    }
                                ?>
                                <img src="<?= htmlspecialchars($rutaWeb) ?>" alt="<?= htmlspecialchars($p['Nombre']) ?>" onerror="this.onerror=null;this.src='assets/img/product-placeholder.png'">
                                <div class="price-badge">$<?= number_format((float)$p['Precio'], 2) ?> MXN</div>
                                <?php if (!(int)$p['Activo']): ?>
                                    <div class="badge-inactive">Inactivo</div>
                                <?php endif; ?>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="product-title"><?= htmlspecialchars($p['Nombre']) ?></h5>
                                <p class="product-desc"><?= nl2br(htmlspecialchars($p['Descripcion'])) ?></p>
                                <p class="mb-1 text-muted small">Existencia: <?= (int)$p['Existencia'] ?></p>
                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <a href="admin/producto_ver.php?id=<?= (int)$p['idProducto'] ?>" class="btn btn-sm btn-primary">Ver</a>
                                    <div class="product-meta text-muted small"><i class="fa fa-star text-warning"></i> 4.6</div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
