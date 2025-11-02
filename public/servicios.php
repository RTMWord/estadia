<?php
// public/servicios.php
// Vista pública del catálogo — usa ServicioController::indexCatalogo()

session_start();
require_once __DIR__ . '/../app/controllers/ServicioController.php';

$ctrl = new ServicioController();
$data = $ctrl->indexCatalogo();
$servicios = $data['servicios'];
$categorias = $data['categorias'];
$ubicaciones = $data['ubicaciones'];
$filters = $data['filters'];

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Catálogo de Servicios - Meto-Hogar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/catalogo.css">
</head>
<body>
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Catálogo de Servicios para Adultos Mayores</h1>
        <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a class="btn btn-primary" href="admin/servicio_nuevo.php">Agregar servicio</a>
        <?php endif; ?>
    </div>

    <form id="filtrosForm" method="get" class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" name="q" class="form-control" placeholder="Buscar por nombre o descripción" value="<?php echo htmlspecialchars($filters['q'] ?? ''); ?>">
        </div>
        <div class="col-md-3">
            <select name="categoria" class="form-select">
                <option value="">Todas las categorías</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo (isset($filters['categoria']) && $filters['categoria'] === $cat) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="ubicacion" class="form-select">
                <option value="">Todas las ubicaciones</option>
                <?php foreach ($ubicaciones as $loc): ?>
                    <option value="<?php echo htmlspecialchars($loc); ?>" <?php echo (isset($filters['ubicacion']) && $filters['ubicacion'] === $loc) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($loc); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button class="btn btn-outline-primary" type="submit">Filtrar</button>
        </div>
    </form>

    <?php if (empty($servicios)): ?>
        <div class="alert alert-info">No se encontraron servicios con los filtros seleccionados.</div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 g-3">
            <?php foreach ($servicios as $s): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($s['imagen'])): ?>
                            <img src="<?php echo htmlspecialchars($s['imagen']); ?>" class="card-img-top servicio-imagen" alt="<?php echo htmlspecialchars($s['titulo']); ?>">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($s['titulo']); ?></h5>
                            <p class="card-text text-muted small mb-1"><?php echo htmlspecialchars($s['categoria']); ?> — <?php echo htmlspecialchars($s['ubicacion']); ?></p>
                            <p class="card-text"><?php echo htmlspecialchars($s['descripcion_corta']); ?></p>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <a href="detalle_servicio.php?id=<?php echo (int)$s['id']; ?>" class="btn btn-sm btn-primary">Ver detalle</a>
                                <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                    <div>
                                        <a href="admin/servicio_editar.php?id=<?php echo (int)$s['id']; ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                                        <a href="admin/servicio_eliminar.php?id=<?php echo (int)$s['id']; ?>" onclick="return confirm('Confirmar eliminación del servicio?');" class="btn btn-sm btn-outline-danger">Eliminar</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/catalogo.js"></script>
</body>
</html>
