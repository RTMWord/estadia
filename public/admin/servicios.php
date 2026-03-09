<?php
session_start();
require_once '../../app/config/db.php';
require_once '../../app/models/Servicio.php';
require_once '../../app/helpers/auth.php';
requireRole($pdo, 'administrador');

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Procesar toggle activo enviado desde la tabla
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['toggle_active']) && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $activo = isset($_POST['activo']) && (int)$_POST['activo'] === 1 ? 1 : 0;
        try {
            $stm = $pdo->prepare('UPDATE servicio SET Activo = ? WHERE idServicio = ?');
            $stm->execute([$activo, $id]);
            $_SESSION['flash_success'] = 'Estado actualizado';
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Error actualizando estado: ' . $e->getMessage();
        }
        header('Location: servicios.php'); exit;
    }
}

// Listar servicios
try {
    $sql = "SELECT s.idServicio, s.Nombre, s.Descripcion, s.Costo, s.Imagen, a.Nombre AS Agencia, s.Activo
            FROM servicio s
            LEFT JOIN agencia a ON s.Agencia_idAgencia = a.idAgencia
            ORDER BY s.idServicio DESC";
    $stmt = $pdo->query($sql);
    $servicios = $stmt->fetchAll();
} catch (Exception $e) {
    $servicios = [];
}

$aprobados = array_filter($servicios, fn($r) => (int)$r['Activo'] === 1);
$pendientes = array_filter($servicios, fn($r) => (int)$r['Activo'] === 0);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Servicios - MetaHogar</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
<?php include __DIR__ . '/partials/admin_nav.php'; ?>
    <div class="container py-5">
        <h2 class="text-primary mb-4">Panel de Servicios</h2>
        <a href="servicio_nuevo.php" class="btn btn-success mb-3">Nuevo Servicio</a>

        <ul class="nav nav-tabs mb-3" id="servTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="aprobados-tab" data-bs-toggle="tab" data-bs-target="#aprobados" type="button" role="tab">Activos (<?= count($aprobados) ?>)</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pendientes-tab" data-bs-toggle="tab" data-bs-target="#pendientes" type="button" role="tab">Deshabilitados (<?= count($pendientes) ?>)</button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="aprobados" role="tabpanel">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Costo</th>
                            <th>Agencia</th>
                            <th>Activo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($aprobados as $s): ?>
                        <tr>
                            <td><?= $s['idServicio'] ?></td>
                            <td>
                                <?php if (!empty($s['Imagen'])): ?>
                                    <img src="../assets/img/servicios/<?= htmlspecialchars($s['Imagen']) ?>" alt="thumb" style="height:48px; width:auto; margin-right:8px; vertical-align:middle; border-radius:4px;">
                                <?php endif; ?>
                                <?= htmlspecialchars($s['Nombre']) ?>
                            </td>
                            <td style="max-width:360px;"><?= htmlspecialchars($s['Descripcion']) ?></td>
                            <td>$<?= number_format($s['Costo'],2) ?></td>
                            <td><?= htmlspecialchars($s['Agencia']) ?></td>
                            <td>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="id" value="<?= $s['idServicio'] ?>">
                                    <input type="hidden" name="activo" value="0">
                                    <input type="checkbox" name="activo" value="1" <?= $s['Activo'] ? 'checked' : '' ?> onchange="this.form.submit()" aria-label="Activo">
                                    <input type="hidden" name="toggle_active" value="1">
                                </form>
                            </td>
                            <td>
                                <a href="servicio_editar.php?id=<?= $s['idServicio'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                <a href="servicio_eliminar.php?id=<?= $s['idServicio'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar servicio?')">Deshabilitar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="pendientes" role="tabpanel">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Costo</th>
                            <th>Agencia</th>
                            <th>Activo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendientes as $s): ?>
                        <tr class="table-secondary">
                            <td><?= $s['idServicio'] ?></td>
                            <td>
                                <?php if (!empty($s['Imagen'])): ?>
                                    <img src="../assets/img/servicios/<?= htmlspecialchars($s['Imagen']) ?>" alt="thumb" style="height:48px; width:auto; margin-right:8px; vertical-align:middle; border-radius:4px;">
                                <?php endif; ?>
                                <?= htmlspecialchars($s['Nombre']) ?>
                            </td>
                            <td style="max-width:360px;"><?= htmlspecialchars($s['Descripcion']) ?></td>
                            <td>$<?= number_format($s['Costo'],2) ?></td>
                            <td><?= htmlspecialchars($s['Agencia']) ?></td>
                            <td>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="id" value="<?= $s['idServicio'] ?>">
                                    <input type="hidden" name="activo" value="0">
                                    <input type="checkbox" name="activo" value="1" <?= $s['Activo'] ? 'checked' : '' ?> onchange="this.form.submit()" aria-label="Activo">
                                    <input type="hidden" name="toggle_active" value="1">
                                </form>
                            </td>
                            <td>
                                <a href="servicio_editar.php?id=<?= $s['idServicio'] ?>" class="btn btn-sm btn-secondary">Editar</a>
                                <a href="servicio_eliminar.php?id=<?= $s['idServicio'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar servicio?')">Eliminar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
<?php if ($flashSuccess): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
    icon: 'success',
    title: 'Éxito',
    text: '<?= addslashes($flashSuccess) ?>',
    confirmButtonColor: '#3085d6',
});
</script>
<?php endif; ?>
<?php if ($flashError): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: '<?= addslashes($flashError) ?>',
    confirmButtonColor: '#d33'
});
</script>
<?php endif; ?>

