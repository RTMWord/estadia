<?php
require_once '../../app/config/db.php';
require_once '../../app/helpers/auth.php';
require_once '../../app/controllers/ReporteController.php';
requireRole($pdo, 'administrador');

$reporteCtrl = new ReporteController($pdo);
$reporteActual = $_GET['reporte'] ?? 'citas'; 

$filtros = [
    'estado' => $_GET['estado'] ?? '',
    'rol' => $_GET['rol'] ?? '',
    'tipo' => $_GET['tipo'] ?? '',
    'activo' => $_GET['activo'] ?? '',
    'agencia' => $_GET['agencia'] ?? '',
    'existencia' => $_GET['existencia'] ?? '',
    'existencia_op' => $_GET['existencia_op'] ?? '>=',
    'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
    'fecha_fin' => $_GET['fecha_fin'] ?? '',
];

$resultados = [];
$filtros_data = [];
$titulo_reporte = "Reporte Desconocido";
$is_analitico = false;
$analitico_data = [];

switch ($reporteActual) {
    case 'citas':
        $resultados = $reporteCtrl->getCitasReporte($filtros);
        $filtros_data['estados'] = $reporteCtrl->getCitaEstados();
        $titulo_reporte = "Citas (CRUD)";
        break;
    case 'usuarios':
        $resultados = $reporteCtrl->getUsuariosReporte($filtros);
        $filtros_data['roles'] = $reporteCtrl->getRoles();
        $filtros_data['tipos'] = $reporteCtrl->getUsuarioTipos();
        $filtros_data['activos'] = $reporteCtrl->getActivoStatus();
        $titulo_reporte = "Usuarios (CRUD)";
        break;
    case 'servicios':
        $resultados = $reporteCtrl->getServiciosReporte($filtros);
        $filtros_data['activos'] = $reporteCtrl->getActivoStatus();
        $filtros_data['agencias'] = $reporteCtrl->getAgencias();
        $titulo_reporte = "Servicios (CRUD)";
        break;
    case 'productos':
        $resultados = $reporteCtrl->getProductosReporte($filtros);
        $filtros_data['activos'] = $reporteCtrl->getActivoStatus();
        $titulo_reporte = "Productos (CRUD)";
        break;
    case 'capacidad_operativa':
        $analitico_data = $reporteCtrl->getCapacidadOperativaReport($filtros);
        $is_analitico = true;
        $filtros_data['estados'] = $reporteCtrl->getCitaEstados(); // Para filtros de fechas
        $titulo_reporte = "Capacidad Operativa (Analítico)";
        break;
    case 'inventario_rendimiento':
        $analitico_data = $reporteCtrl->getInventarioRendimientoReport($filtros);
        $is_analitico = true;
        $filtros_data['activos'] = $reporteCtrl->getActivoStatus(); // Para filtros de activo
        $titulo_reporte = "Rendimiento Inventario (Analítico)";
        break;
    case 'usuario_riesgo':
         $is_analitico = true;
         $titulo_reporte = "Perfil de Riesgo de Usuario (Analítico)";
         $analitico_data = $reporteCtrl->getUsuarioPerfilRiesgoReport($filtros);
         break;
    case 'temas_criticos':
        $is_analitico = true;
        $titulo_reporte = "Detección de Temas Críticos (Analítico)";
        $analitico_data = $reporteCtrl->getTemasCriticosReport($filtros);
        break;
    default:
        $titulo_reporte = "Reporte Desconocido";
        break;
}

// Se necesita contar los resultados de las secciones de tablas para los analíticos.
$total_resultados = $is_analitico ? array_sum(array_map('count', array_filter($analitico_data, 'is_array'))) : count($resultados);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes Dinámicos - MetaHogar Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        .nav-tabs .nav-link {
            color: #495057; 
        }
        .nav-tabs .nav-link.active {
            color: #007bff; 
            background-color: #fff;
            border-color: #dee2e6 #dee2e6 #fff;
        }
    </style>
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
        <h2 class="text-primary mb-4">Generador de Reportes Dinámicos</h2>
        
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item"><a class="nav-link <?= $reporteActual == 'citas' ? 'active' : '' ?>" href="?reporte=citas">Citas (CRUD)</a></li>
            <li class="nav-item"><a class="nav-link <?= $reporteActual == 'usuarios' ? 'active' : '' ?>" href="?reporte=usuarios">Usuarios (CRUD)</a></li>
            <li class="nav-item"><a class="nav-link <?= $reporteActual == 'servicios' ? 'active' : '' ?>" href="?reporte=servicios">Servicios (CRUD)</a></li>
            <li class="nav-item"><a class="nav-link <?= $reporteActual == 'productos' ? 'active' : '' ?>" href="?reporte=productos">Productos (CRUD)</a></li>
            
            <li class="nav-item"><a class="nav-link <?= $reporteActual == 'capacidad_operativa' ? 'active' : '' ?> text-success" href="?reporte=capacidad_operativa">Capacidad Operativa</a></li>
            <li class="nav-item"><a class="nav-link <?= $reporteActual == 'inventario_rendimiento' ? 'active' : '' ?> text-success" href="?reporte=inventario_rendimiento">Rendimiento Inventario</a></li>
             <li class="nav-item"><a class="nav-link <?= $reporteActual == 'usuario_riesgo' ? 'active' : '' ?> text-info" href="?reporte=usuario_riesgo">Perfil de Riesgo</a></li>
            <li class="nav-item"><a class="nav-link <?= $reporteActual == 'temas_criticos' ? 'active' : '' ?> text-info" href="?reporte=temas_criticos">Temas Críticos</a></li>
        </ul>

        <div class="card mb-4">
            <div class="card-header">Filtros para Reporte de <?= $titulo_reporte ?></div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <input type="hidden" name="reporte" value="<?= htmlspecialchars($reporteActual) ?>">
                    
                    <?php if ($reporteActual == 'citas' || $reporteActual == 'capacidad_operativa'): ?>
                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select name="estado" class="form-select" <?= $reporteActual == 'capacidad_operativa' ? 'disabled' : '' ?>>
                                <option value="">Todos</option>
                                <?php foreach ($filtros_data['estados'] ?? [] as $e): ?>
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
                    <?php elseif ($reporteActual == 'usuarios'): ?>
                        <div class="col-md-2">
                            <label class="form-label">Rol</label>
                            <select name="rol" class="form-select">
                                <option value="">Todos</option>
                                <?php foreach ($filtros_data['roles'] ?? [] as $r): ?>
                                    <option value="<?= $r ?>" <?= $filtros['rol'] == $r ? 'selected' : '' ?>><?= $r ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                         <div class="col-md-2">
                            <label class="form-label">Tipo</label>
                            <select name="tipo" class="form-select">
                                <option value="">Todos</option>
                                <?php foreach ($filtros_data['tipos'] ?? [] as $t): ?>
                                    <option value="<?= $t ?>" <?= $filtros['tipo'] == $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Activo</label>
                            <select name="activo" class="form-select">
                                <option value="">Todos</option>
                                <?php foreach ($filtros_data['activos'] ?? [] as $val => $label): ?>
                                    <option value="<?= $val ?>" <?= (string)$filtros['activo'] === (string)$val ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                         <div class="col-md-3">
                            <label class="form-label">Fecha Registro (Desde)</label>
                            <input type="date" name="fecha_inicio" class="form-control" value="<?= htmlspecialchars($filtros['fecha_inicio']) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha Registro (Hasta)</label>
                            <input type="date" name="fecha_fin" class="form-control" value="<?= htmlspecialchars($filtros['fecha_fin']) ?>">
                        </div>
                    <?php elseif ($reporteActual == 'servicios'): ?>
                        <div class="col-md-3">
                            <label class="form-label">Activo</label>
                            <select name="activo" class="form-select">
                                <option value="">Todos</option>
                                <?php foreach ($filtros_data['activos'] ?? [] as $val => $label): ?>
                                    <option value="<?= $val ?>" <?= (string)$filtros['activo'] === (string)$val ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                             <label class="form-label">Agencia</label>
                            <select name="agencia" class="form-select">
                                <option value="">Todas</option>
                                <?php foreach ($filtros_data['agencias'] ?? [] as $a): ?>
                                    <option value="<?= $a ?>" <?= $filtros['agencia'] == $a ? 'selected' : '' ?>><?= $a ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php elseif ($reporteActual == 'productos' || $reporteActual == 'inventario_rendimiento'): ?>
                        <div class="col-md-3">
                            <label class="form-label">Activo</label>
                            <select name="activo" class="form-select">
                                <option value="">Todos</option>
                                <?php foreach ($filtros_data['activos'] ?? [] as $val => $label): ?>
                                    <option value="<?= $val ?>" <?= (string)$filtros['activo'] === (string)$val ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                         <div class="col-md-3">
                            <label class="form-label">Existencia</label>
                            <div class="input-group">
                                <select name="existencia_op" class="form-select" style="max-width: 100px;" <?= $reporteActual == 'inventario_rendimiento' ? 'disabled' : '' ?>>
                                    <option value=">=" <?= $filtros['existencia_op'] == '>=' ? 'selected' : '' ?>>&ge;</option>
                                    <option value="=" <?= $filtros['existencia_op'] == '=' ? 'selected' : '' ?>>=</option>
                                    <option value="<=" <?= $filtros['existencia_op'] == '<=' ? 'selected' : '' ?>>&le;</option>
                                </select>
                                <input type="number" name="existencia" class="form-control" value="<?= htmlspecialchars($filtros['existencia']) ?>" <?= $reporteActual == 'inventario_rendimiento' ? 'disabled' : '' ?>>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Aplicar Filtros</button>
                        <a href="reportes.php?reporte=<?= htmlspecialchars($reporteActual) ?>" class="btn btn-secondary">Limpiar</a>
                    </div>
                </form>
            </div>
        </div>

        <h3>Resultados (<?= $total_resultados ?>)</h3>
        
            <?php if (!empty($resultados) || $is_analitico): ?>
            <?php 
                $export_query = http_build_query(array_merge($_GET, ['pdf' => 1]));
            ?>
            <?php if ($is_analitico): ?>
                <!-- Export form that will receive chart images as POST -->
                <form id="exportPdfForm" method="POST" action="reporte_pdf.php" target="_blank" class="d-inline-block mb-3">
                    <?php foreach ($_GET as $k => $v): ?>
                        <input type="hidden" name="<?= htmlspecialchars($k) ?>" value="<?= htmlspecialchars($v) ?>">
                    <?php endforeach; ?>
                    <input type="hidden" name="pdf" value="1">
                    <input type="hidden" name="chart_image_demanda" id="chart_image_demanda">
                    <input type="hidden" name="chart_image_latencia" id="chart_image_latencia">
                    <button id="exportPdfBtn" type="button" class="btn btn-danger">Exportar a PDF (con gráficas)</button>
                </form>
            <?php else: ?>
                <a href="reporte_pdf.php?<?= $export_query ?>" target="_blank" class="btn btn-danger mb-3">Exportar a PDF</a>
            <?php endif; ?>

            <?php if ($is_analitico): ?>
                <?php foreach ($analitico_data as $seccion_nombre => $datos_seccion): ?>
                    <?php // Render charts for capacidad_operativa sections ?>
                    <h4 class="mt-4 mb-2 text-primary"><?= str_replace('_', ' ', htmlspecialchars($seccion_nombre)) ?></h4>
                    <?php if (empty($datos_seccion)): ?>
                        <div class="alert alert-info">No hay datos disponibles para esta sección.</div>
                    <?php elseif (array_key_exists(0, $datos_seccion) && is_array($datos_seccion[0])): 
                        // Es un array de arrays (tabla)
                        $keys = array_keys($datos_seccion[0]); ?>
                        <table class="table table-bordered table-hover table-sm">
                            <thead>
                                <tr>
                                    <?php foreach ($keys as $k): ?>
                                        <th><?= htmlspecialchars(str_replace('_', ' ', $k)) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($datos_seccion as $r): ?>
                                    <tr>
                                        <?php foreach ($r as $key => $val): ?>
                                            <td><?= htmlspecialchars($val) ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: 
                        // Es un array asociativo simple (resumen de métricas) ?>
                        <table class="table table-sm table-borderless w-50">
                            <tbody>
                                <?php foreach ($datos_seccion as $key => $val): ?>
                                    <tr>
                                        <td class="fw-bold"><?= htmlspecialchars($key) ?></td>
                                        <td><?= htmlspecialchars($val) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php if ($reporteActual == 'capacidad_operativa'): ?>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5>Demanda Próxima (Próximos 30 días)</h5>
                            <canvas id="chartDemanda"></canvas>
                        </div>
                        <div class="col-md-6">
                            <h5>Latencia Promedio de Agendamiento (horas)</h5>
                            <canvas id="chartLatencia"></canvas>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <?php 
                    // Renderizado de tablas CRUD (existente)
                    $keys = !empty($resultados) ? array_keys($resultados[0]) : [];
                ?>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <?php foreach ($keys as $k) echo '<th>' . htmlspecialchars($k) . '</th>'; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultados as $r): ?>
                        <tr>
                            <?php foreach ($r as $key => $val): ?>
                                <td>
                                    <?php
                                        if ($key === 'Activo') {
                                            echo $val ? 'Sí' : 'No';
                                        } elseif (in_array($key, ['Costo', 'Precio'])) {
                                            echo '$' . number_format((float)$val, 2);
                                        } else {
                                            echo htmlspecialchars($val);
                                        }
                                    ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-warning">No hay resultados para los filtros seleccionados.</div>
        <?php endif; ?>
        
        <a href="index.php" class="btn btn-outline-secondary mt-3">Volver al Panel</a>

    </div>
    <script>
    // Only run chart code when capacidad_operativa
    (function(){
        const reporte = <?= json_encode($reporteActual) ?>;
        if (reporte !== 'capacidad_operativa') return;

        // Prepare data from PHP for Demanda (if present)
        const analitico = <?= json_encode($analitico_data) ?>;

        // Demanda
        const demanda = analitico['Demanda_Proxima_(Proximos_30_Dias)'] || [];
        const demandaLabels = demanda.map(d => d.Agencia || 'Sin agencia');
        const demandaValues = demanda.map(d => parseInt(d.CitasProximas) || 0);

        // Latencia
        const latencia = analitico['Latencia_Promedio_de_Agendamiento'] || [];
        const latLabels = latencia.map(l => l.Servicio || 'Sin servicio');
        const latValues = latencia.map(l => parseFloat(l.LatenciaPromedioHoras) || 0);

        // Render charts
        const ctxD = document.getElementById('chartDemanda');
        if (ctxD) {
            new Chart(ctxD, {
                type: 'bar',
                data: { labels: demandaLabels, datasets: [{ label: 'Citas próximas', data: demandaValues, backgroundColor: 'rgba(54,162,235,0.6)' }] },
                options: { responsive: true, plugins: { legend: { display: false } } }
            });
        }

        const ctxL = document.getElementById('chartLatencia');
        if (ctxL) {
            new Chart(ctxL, {
                type: 'bar',
                data: { labels: latLabels, datasets: [{ label: 'Horas (avg)', data: latValues, backgroundColor: 'rgba(255,159,64,0.7)' }] },
                options: { responsive: true, plugins: { legend: { display: false } } }
            });
        }

        // Export handler: capture canvases and submit form
        const exportBtn = document.getElementById('exportPdfBtn');
        if (exportBtn) {
            exportBtn.addEventListener('click', function(){
                const form = document.getElementById('exportPdfForm');
                // capture demanda
                const cDem = document.getElementById('chartDemanda');
                if (cDem && cDem.toDataURL) {
                    try { document.getElementById('chart_image_demanda').value = cDem.toDataURL('image/png'); } catch(e) { console.warn(e); }
                }
                const cLat = document.getElementById('chartLatencia');
                if (cLat && cLat.toDataURL) {
                    try { document.getElementById('chart_image_latencia').value = cLat.toDataURL('image/png'); } catch(e) { console.warn(e); }
                }
                form.submit();
            });
        }
    })();
    </script>
</body>
</html>