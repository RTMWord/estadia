<?php
require_once __DIR__ . '/_security_check.php';
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/models/Servicio.php';

$svc = new Servicio($pdo);
$created = false;
$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Procesar imagen si se sube (mismo directorio que el admin)
    $imagenNombre = null;
    if (!empty($_FILES['Imagen']) && !empty($_FILES['Imagen']['name'])) {
        $f = $_FILES['Imagen'];
        if ($f['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','webp'];
            if (in_array($ext, $allowed)) {
                $imagenNombre = uniqid('srv_') . '.' . $ext;
                $destDir = __DIR__ . '/../assets/img/servicios/';
                if (!is_dir($destDir)) mkdir($destDir, 0755, true);
                $destPath = $destDir . $imagenNombre;
                if (!move_uploaded_file($f['tmp_name'], $destPath)) {
                    $imagenNombre = null;
                }
            }
        }
    }

    $data = [
        'titulo' => $_POST['titulo'] ?? '',
        'descripcion' => $_POST['descripcion'] ?? '',
        'categoria' => $_POST['categoria'] ?? '',
        'ubicacion' => $_POST['ubicacion'] ?? '',
        'contacto' => $_POST['contacto'] ?? '',
        'precio' => $_POST['precio'] ?? 0,
        'agencia' => !empty($_POST['Agencia_idAgencia']) ? (int)$_POST['Agencia_idAgencia'] : null,
        // marcar inactivo hasta aprobación del admin
        'status' => 0,
        'imagen' => $imagenNombre
    ];
    $result = $svc->crear($data);
    if (!empty($result['ok'])) {
        $created = true;
        header('Location: index.php?service_proposed=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Proponer servicio - Proveedor</title>
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5 provider-panel">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4">Proponer nuevo servicio</h1>
            <a href="index.php" class="btn btn-outline-secondary">Volver</a>
        </div>

        <?php if (isset($_GET['service_proposed'])): ?>
            <div class="alert alert-success">Servicio propuesto correctamente. Espera aprobación del administrador.</div>
        <?php endif; ?>
        <?php if (isset($result) && isset($result['ok']) && !$result['ok']): ?>
            <div class="alert alert-danger">Error: <?= htmlspecialchars($result['error']) ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="titulo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" rows="6" class="form-control"></textarea>
                    </div>
                    <!-- descripción corta eliminada -->
                    <div class="row g-2">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Categoría</label>
                            <input class="form-control" name="categoria" type="text">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Ubicación</label>
                            <input class="form-control" name="ubicacion" type="text">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Contacto</label>
                            <input class="form-control" name="contacto" type="text">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Precio</label>
                        <input type="number" step="0.01" name="precio" class="form-control" value="0.00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Agencia (opcional)</label>
                        <select name="Agencia_idAgencia" class="form-select">
                            <option value="">-- Seleccionar agencia --</option>
                            <?php
                            try {
                                $stmtAg = $pdo->query('SELECT idAgencia, Nombre FROM agencia ORDER BY Nombre');
                                $agencias = $stmtAg->fetchAll(PDO::FETCH_ASSOC);
                            } catch (Exception $e) {
                                $agencias = [];
                            }
                            foreach ($agencias as $a): ?>
                                <option value="<?= $a['idAgencia'] ?>"><?= htmlspecialchars($a['Nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Imagen (opcional)</label>
                        <input class="form-control" name="Imagen" type="file" accept="image/*">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Proponer servicio</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
