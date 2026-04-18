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
    $productosModal = array_map(function($item) {
        return [
            'id' => (int)($item['idProducto'] ?? 0),
            'nombre' => (string)($item['Nombre'] ?? '')
        ];
    }, $productos);
    ?>
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
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-primary js-producto-info-btn"
                                        data-producto-id="<?= (int)$p['idProducto'] ?>"
                                        data-producto-nombre="<?= htmlspecialchars($p['Nombre'], ENT_QUOTES) ?>"
                                    >Solictar más información</button>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    (function () {
        var productos = <?= json_encode($productosModal, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

        function escapeHtml(value) {
            if (value === null || value === undefined) return '';
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function buildProductOptions(selectedId) {
            return productos.map(function (p) {
                var isSelected = Number(selectedId) === Number(p.id) ? ' selected' : '';
                return '<option value="' + Number(p.id) + '"' + isSelected + '>' + escapeHtml(p.nombre) + '</option>';
            }).join('');
        }

        async function openInfoModal(productoId, productoNombre) {
            var result = await Swal.fire({
                title: 'Solicitar información',
                html: '' +
                    '<div style="text-align:left;">' +
                        '<label for="swal-nombre" style="display:block;margin-bottom:6px;font-weight:600;">Nombre completo</label>' +
                        '<input id="swal-nombre" class="swal2-input" placeholder="Tu nombre completo" style="margin:0 0 12px 0;">' +
                        '<label for="swal-correo" style="display:block;margin-bottom:6px;font-weight:600;">Correo electrónico</label>' +
                        '<input id="swal-correo" type="email" class="swal2-input" placeholder="tucorreo@ejemplo.com" style="margin:0 0 12px 0;">' +
                        '<label for="swal-producto" style="display:block;margin-bottom:6px;font-weight:600;">Producto de interés</label>' +
                        '<select id="swal-producto" class="swal2-select" style="margin:0;">' +
                            buildProductOptions(productoId) +
                        '</select>' +
                    '</div>',
                confirmButtonText: 'Enviar solicitud',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#17466e',
                focusConfirm: false,
                didOpen: function () {
                    var nombreInput = document.getElementById('swal-nombre');
                    if (nombreInput) {
                        nombreInput.focus();
                    }
                },
                preConfirm: function () {
                    var nombre = (document.getElementById('swal-nombre') || {}).value || '';
                    var correo = (document.getElementById('swal-correo') || {}).value || '';
                    var productoSeleccionado = (document.getElementById('swal-producto') || {}).value || '';

                    nombre = nombre.trim();
                    correo = correo.trim();

                    if (!nombre || nombre.length < 5) {
                        Swal.showValidationMessage('Ingresa tu nombre completo.');
                        return false;
                    }

                    var correoRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!correoRegex.test(correo)) {
                        Swal.showValidationMessage('Ingresa un correo electrónico válido.');
                        return false;
                    }

                    if (!productoSeleccionado) {
                        Swal.showValidationMessage('Selecciona un producto de interés.');
                        return false;
                    }

                    return {
                        nombre_completo: nombre,
                        correo_electronico: correo,
                        id_producto: productoSeleccionado,
                        producto_referencia: productoNombre || ''
                    };
                }
            });

            if (!result.isConfirmed || !result.value) {
                return;
            }

            Swal.fire({
                title: 'Enviando...',
                text: 'Estamos procesando tu solicitud.',
                allowOutsideClick: false,
                didOpen: function () {
                    Swal.showLoading();
                }
            });

            try {
                var response = await fetch('php/producto_info_request.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(result.value)
                });

                var data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'No se pudo enviar la solicitud.');
                }

                await Swal.fire({
                    icon: 'success',
                    title: 'Solicitud enviada',
                    text: data.message || 'Revisa tu correo para más detalles.',
                    confirmButtonColor: '#17466e'
                });
            } catch (error) {
                await Swal.fire({
                    icon: 'error',
                    title: 'Error al enviar',
                    text: error && error.message ? error.message : 'Ocurrió un error inesperado.',
                    confirmButtonColor: '#17466e'
                });
            }
        }

        document.addEventListener('click', function (event) {
            var btn = event.target.closest('.js-producto-info-btn');
            if (!btn) return;

            var productoId = btn.getAttribute('data-producto-id') || '';
            var productoNombre = btn.getAttribute('data-producto-nombre') || '';
            openInfoModal(productoId, productoNombre);
        });
    })();
    </script>

</body>
</html>
