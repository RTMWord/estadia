<?php
require_once '../../app/config/db.php';
require_once '../../app/helpers/auth.php';
require_once '../../app/controllers/ReporteController.php';
requireRole($pdo, 'administrador');

$reporteCtrl = new ReporteController($pdo);
$reporteActual = $_GET['reporte'] ?? 'citas'; // Reporte por defecto

$filtros = [
    'estado' => $_GET['estado'] ?? '',
    'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
    'fecha_fin' => $_GET['fecha_fin'] ?? '',
];

$resultados = [];
if ($reporteActual === 'citas') {
    $resultados = $reporteCtrl->getCitasReporte($filtros);
    $filtros_select = $reporteCtrl->getCitaEstados();
}
// Aquí se añadiría la lógica para 'usuarios', 'servicios', 'productos'

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes Dinámicos - MetaHogar Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h2 class="text-primary mb-4">Generador de Reportes Dinámicos</h2>
        
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link <?= $reporteActual == 'citas' ? 'active' : '' ?>" href="?reporte=citas">Citas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $reporteActual == 'usuarios' ? 'active' : '' ?>" href="?reporte=usuarios">Usuarios</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $reporteActual == 'servicios' ? 'active' : '' ?>" href="?reporte=servicios">Servicios</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $reporteActual == 'productos' ? 'active' : '' ?>" href="?reporte=productos">Productos</a>
            </li>
        </ul>

        <div class="card mb-4">
            <div class="card-header">Filtros para Reporte de Citas</div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <input type="hidden" name="reporte" value="<?= htmlspecialchars($reporteActual) ?>">
                    
                    <div class="col-md-3">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="">Todos</option>
                            <?php foreach ($filtros_select as $e): ?>
                                <option value="<?= $e ?>" <?= $filtros['estado'] == $e ? 'selected' : '' ?>><?= $e ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="<?= htmlspecialchars($filtros['fecha_inicio']) ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Fecha Fin</label>
                        <input type="date" name="fecha_fin" class="form-control" value="<?= htmlspecialchars($filtros['fecha_fin']) ?>">
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Aplicar Filtros</button>
                        <a href="reportes.php?reporte=<?= htmlspecialchars($reporteActual) ?>" class="btn btn-secondary">Limpiar</a>
                    </div>
                </form>
            </div>
        </div>

        <h3>Resultados (<?= count($resultados) ?>)</h3>
        <?php if ($reporteActual == 'citas' && !empty($resultados)): ?>
            <?php 
                // Construye la URL de exportación con los filtros actuales
                $export_query = http_build_query(array_merge($_GET, ['pdf' => 1]));
            ?>
            <a href="reporte_pdf.php?<?= $export_query ?>" target="_blank" class="btn btn-danger mb-3">Exportar a PDF</a>

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID Cita</th>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Servicio</th>
                        <th>Fecha y Hora</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultados as $r): ?>
                    <tr>
                        <td><?= $r['idCita'] ?></td>
                        <td><?= htmlspecialchars($r['Usuario']) ?></td>
                        <td><?= htmlspecialchars($r['Email']) ?></td>
                        <td><?= htmlspecialchars($r['Servicio']) ?></td>
                        <td><?= $r['FechaHora'] ?></td>
                        <td><?= $r['Estado'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif (empty($resultados)): ?>
            <div class="alert alert-warning">No hay resultados para los filtros seleccionados.</div>
        <?php endif; ?>
        
        <a href="index.php" class="btn btn-outline-secondary mt-3">Volver al Panel</a>

    </div>
</body>
</html>