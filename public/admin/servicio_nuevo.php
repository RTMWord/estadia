<?php
// public/admin/servicio_nuevo.php
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
requireRole($pdo, 'administrador');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['Nombre'] ?? '');
    $descripcion = trim($_POST['Descripcion'] ?? '');
    $costo = isset($_POST['Costo']) && $_POST['Costo'] !== '' ? (float)$_POST['Costo'] : 0.0;
    $agencia = !empty($_POST['Agencia_idAgencia']) ? (int)$_POST['Agencia_idAgencia'] : null;
    $activo = isset($_POST['Activo']) ? 1 : 0;

    if ($nombre === '') {
        $errors[] = 'El nombre es obligatorio';
    }

    // No manejamos imágenes (columna Imagen será eliminada)

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare('INSERT INTO Servicio (Nombre, Descripcion, Costo, Agencia_idAgencia, Activo) VALUES (:nombre, :descripcion, :costo, :agencia, :activo)');
            $stmt->execute([
                ':nombre' => $nombre,
                ':descripcion' => $descripcion,
                ':costo' => $costo,
                ':agencia' => $agencia,
                ':activo' => $activo
            ]);
            $_SESSION['flash_success'] = 'Servicio registrado exitosamente';
            header('Location: servicios.php');
            exit;
        } catch (Exception $e) {
            $errors[] = 'Error al guardar en la base de datos: ' . $e->getMessage();
        }
    }
}
// Obtener lista de agencias para el select
try {
    $stmtAg = $pdo->query('SELECT idAgencia, Nombre FROM agencia ORDER BY Nombre');
    $agencias = $stmtAg->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $agencias = [];
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Agregar Servicio - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <h1>Agregar nuevo servicio</h1>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger"><?php foreach ($errors as $e) echo "<div>".htmlspecialchars($e)."</div>"; ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input class="form-control" name="Nombre" required value="<?php echo htmlspecialchars($_POST['Nombre'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="Descripcion" rows="6"><?php echo htmlspecialchars($_POST['Descripcion'] ?? ''); ?></textarea>
        </div>
        <!-- contacto eliminado -->
        <div class="row g-2">
            <div class="col-md-4 mb-3">
                <label class="form-label">Costo</label>
                <input class="form-control" name="Costo" type="number" step="0.01" value="<?php echo htmlspecialchars($_POST['Costo'] ?? '0.00'); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Agencia</label>
                <select name="Agencia_idAgencia" class="form-select">
                    <option value="">-- Seleccionar agencia --</option>
                    <?php foreach ($agencias as $a): ?>
                        <option value="<?= $a['idAgencia'] ?>" <?= (isset($_POST['Agencia_idAgencia']) && $_POST['Agencia_idAgencia'] == $a['idAgencia']) ? 'selected' : ''?>><?= htmlspecialchars($a['Nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 mb-3"></div>
        </div>
        <!-- Imagen: eliminado del formulario (columna Imagen removida) -->
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="Activo" id="status" <?= isset($_POST['Activo']) || !isset($_POST['Activo']) ? 'checked' : '' ?>>
            <label class="form-check-label" for="status">Activo</label>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success" type="submit">Guardar</button>
            <a href="../servicios.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>
