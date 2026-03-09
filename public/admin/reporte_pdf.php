<?php
require_once '../../app/config/db.php';
require_once '../../app/helpers/auth.php';
require_once '../../app/controllers/ReporteController.php';
requireRole($pdo, 'administrador');

// Fijar zona horaria explícita para evitar inconsistencias (override si existe)
date_default_timezone_set('America/Mexico_City');

// Timestamp de generación para el PDF (en zona horaria fija)
$now = new DateTime('now', new DateTimeZone('America/Mexico_City'));

// Usar REQUEST porque el export analítico puede enviar imágenes por POST
$reporteCtrl = new ReporteController($pdo);
$reporteActual = $_REQUEST['reporte'] ?? 'citas';

$filtros = [
    'estado' => $_REQUEST['estado'] ?? '',
    'rol' => $_REQUEST['rol'] ?? '',
    'tipo' => $_REQUEST['tipo'] ?? '',
    'activo' => $_REQUEST['activo'] ?? '',
    'agencia' => $_REQUEST['agencia'] ?? '',
    'existencia' => $_REQUEST['existencia'] ?? '',
    'existencia_op' => $_REQUEST['existencia_op'] ?? '>=',
    'fecha_inicio' => $_REQUEST['fecha_inicio'] ?? '',
    'fecha_fin' => $_REQUEST['fecha_fin'] ?? '',
    'ciudad' => $_REQUEST['ciudad'] ?? '',
    'perfil' => $_REQUEST['perfil'] ?? '',
    'dificultad' => $_REQUEST['dificultad'] ?? '',
];

$tituloReporte = "Reporte Desconocido";
$htmlTabla = "";
$columnas = [];
$resultados = [];
$is_analitico = false;
$analitico_data = [];

// Cargar datos según tipo de reporte
switch ($reporteActual) {
    case 'citas':
        $resultados = $reporteCtrl->getCitasReporte($filtros);
        $tituloReporte = "Reporte de Citas (CRUD)";
        $columnas = ['idCita', 'Usuario', 'Email', 'Servicio', 'FechaHora', 'Estado'];
        break;
    case 'usuarios':
        $resultados = $reporteCtrl->getUsuariosReporte($filtros);
        $tituloReporte = "Reporte de Usuarios (CRUD)";
        $columnas = ['idUsuario', 'NombreCompleto', 'Email', 'Telefono', 'Rol', 'Tipo', 'Activo', 'FechaRegistro'];
        break;
    case 'servicios':
        $resultados = $reporteCtrl->getServiciosReporte($filtros);
        $tituloReporte = "Reporte de Servicios (CRUD)";
        $columnas = ['idServicio', 'Nombre', 'Costo', 'Agencia', 'Activo'];
        break;
    case 'productos':
        $resultados = $reporteCtrl->getProductosReporte($filtros);
        $tituloReporte = "Reporte de Productos (CRUD)";
        $columnas = ['idProducto', 'Nombre', 'Precio', 'Existencia', 'Activo'];
        break;
    case 'capacidad_operativa':
        $analitico_data = $reporteCtrl->getCapacidadOperativaReport($filtros);
        $is_analitico = true;
        $tituloReporte = "Análisis de Capacidad Operativa y Eficiencia";
        break;
    case 'inventario_rendimiento':
        $analitico_data = $reporteCtrl->getInventarioRendimientoReport($filtros);
        $is_analitico = true;
        $tituloReporte = "Análisis de Rendimiento de Inventario";
        break;
    case 'usuario_riesgo':
        $is_analitico = true;
        $tituloReporte = "Análisis de Perfil de Riesgo de Usuario";
        $analitico_data = $reporteCtrl->getUsuarioPerfilRiesgoReport($filtros);
        break;
    case 'temas_criticos':
        $is_analitico = true;
        $tituloReporte = "Análisis de Detección de Temas Críticos";
        $analitico_data = $reporteCtrl->getTemasCriticosReport($filtros);
        break;
    case 'diagnosticos':
        // Cargar desde data/diagnosticos.json y aplicar filtros similares a la UI
        $dataFile = __DIR__ . '/../../data/diagnosticos.json';
        $diagnosticos = [];
        if (is_file($dataFile)) {
            $raw = @file_get_contents($dataFile);
            $diagnosticos = $raw ? json_decode($raw, true) : [];
            if (!is_array($diagnosticos)) $diagnosticos = [];
        }

        $resultados = [];
        foreach ($diagnosticos as $d) {
            // filtros por fecha
            $createdTs = isset($d['created_at']) ? strtotime($d['created_at']) : null;
            if (!empty($filtros['fecha_inicio'])) {
                $startTs = strtotime($filtros['fecha_inicio'] . ' 00:00:00');
                if ($createdTs === null || $createdTs < $startTs) continue;
            }
            if (!empty($filtros['fecha_fin'])) {
                $endTs = strtotime($filtros['fecha_fin'] . ' 23:59:59');
                if ($createdTs === null || $createdTs > $endTs) continue;
            }
            // filtro por ciudad
            if (!empty($_REQUEST['ciudad'])) {
                $ciudadFilter = mb_strtolower(trim($_REQUEST['ciudad']));
                $ciudadVal = mb_strtolower(trim($d['contact']['ciudad'] ?? ''));
                if ($ciudadFilter !== '' && strpos($ciudadVal, $ciudadFilter) === false) continue;
            }
            // filtro por perfil
            if (!empty($_REQUEST['perfil'])) {
                if (trim($d['perfil'] ?? '') !== trim($_REQUEST['perfil'])) continue;
            }
            // filtro por dificultad (buscar en array)
            if (!empty($_REQUEST['dificultad'])) {
                $dif = trim($_REQUEST['dificultad']);
                $has = false;
                if (!empty($d['dificultades']) && is_array($d['dificultades'])) {
                    foreach ($d['dificultades'] as $dd) {
                        if (trim($dd) === $dif) { $has = true; break; }
                    }
                }
                if (!$has) continue;
            }

            // Formatear created_at usando DateTime y la zona horaria objetivo
            $created_formatted = '';
            if (!empty($d['created_at'])) {
                try {
                    $dt = new DateTime($d['created_at']);
                    $dt->setTimezone(new DateTimeZone('America/Mexico_City'));
                    $created_formatted = $dt->format('d/m/Y H:i:s');
                } catch (Exception $e) {
                    $created_formatted = $d['created_at'];
                }
            }

            $resultados[] = [
                'id' => $d['id'] ?? '',
                'created_at' => $created_formatted,
                'perfil' => $d['perfil'] ?? '',
                'nombre' => $d['contact']['nombre'] ?? '',
                'email' => $d['contact']['email'] ?? '',
                'ciudad' => $d['contact']['ciudad'] ?? '',
                'dificultades' => !empty($d['dificultades']) ? implode(', ', $d['dificultades']) : '',
                'intereses' => !empty($d['intereses']) ? implode(', ', $d['intereses']) : '',
            ];
        }

        $tituloReporte = "Reporte de Diagnósticos (CRUD)";
        break;
    default:
        // deja $resultados vacío y título por defecto
        break;
}

$total_registros = 0;
$htmlTabla = '';

if ($is_analitico) {
    foreach ($analitico_data as $seccion_nombre => $datos_seccion) {
        $htmlTabla .= '<h3>' . htmlspecialchars(str_replace('_', ' ', $seccion_nombre)) . '</h3>';

        if (empty($datos_seccion)) {
            $htmlTabla .= '<p><em>No hay datos disponibles para esta sección.</em></p>';
            continue;
        }

        if (is_array($datos_seccion) && array_key_exists(0, $datos_seccion) && is_array($datos_seccion[0])) {
            // Es un array de arrays (tabla)
            $total_registros += count($datos_seccion);
            $keys = array_keys($datos_seccion[0]);

            $htmlTabla .= '<table><thead><tr>';
            foreach ($keys as $k) {
                $htmlTabla .= '<th>' . htmlspecialchars(str_replace('_', ' ', $k)) . '</th>';
            }
            $htmlTabla .= '</tr></thead><tbody>';

            foreach ($datos_seccion as $r) {
                $htmlTabla .= '<tr>';
                foreach ($keys as $key) {
                    $htmlTabla .= '<td>' . htmlspecialchars($r[$key]) . '</td>';
                }
                $htmlTabla .= '</tr>';
            }
            $htmlTabla .= '</tbody></table>';

        } elseif (is_array($datos_seccion)) {
            // Es un array asociativo simple (resumen de métricas)
            $htmlTabla .= '<table class="metric-table"><tbody>';
            foreach ($datos_seccion as $key => $val) {
                $htmlTabla .= '<tr><td style="font-weight:bold;">' . htmlspecialchars($key) . '</td><td>' . htmlspecialchars($val) . '</td></tr>';
            }
            $htmlTabla .= '</tbody></table>';
        } else {
            // Es un valor simple
            $htmlTabla .= '<table class="metric-table"><tbody>';
            $htmlTabla .= '<tr><td style="font-weight:bold;">Valor</td><td>' . htmlspecialchars((string)$datos_seccion) . '</td></tr>';
            $htmlTabla .= '</tbody></table>';
        }
    }
} elseif (!empty($resultados)) {
    // Lógica para reportes CRUD
    $total_registros = count($resultados);
    $columnas_labels = [];
    if (!empty($resultados[0])) {
        $columnas_labels = array_keys($resultados[0]);
    }

    $htmlTabla .= '<table><thead><tr>';
    foreach ($columnas_labels as $k) {
        $htmlTabla .= '<th>' . htmlspecialchars($k) . '</th>';
    }
    $htmlTabla .= '</tr></thead><tbody>';

    foreach ($resultados as $r) {
        $htmlTabla .= '<tr>';
        foreach ($columnas_labels as $key) {
            $valor = $r[$key];
            if ($key === 'Activo') {
                $valor = $valor ? 'Sí' : 'No';
            } elseif (in_array($key, ['Costo', 'Precio'])) {
                $valor = '$' . number_format((float)$valor, 2);
            }
            $htmlTabla .= '<td>' . htmlspecialchars($valor) . '</td>';
        }
        $htmlTabla .= '</tr>';
    }
    $htmlTabla .= '</tbody></table>';
}

// 3. Crear el HTML completo para Dompdf
$filtrosAplicados = [];
foreach ($filtros as $key => $value) {
    if ($value !== '' && !in_array($key, ['reporte', 'pdf'])) {
        $filtrosAplicados[] = htmlspecialchars(ucfirst($key) . ': ' . $value);
    }
}
$extraFilterKeys = ['ciudad','perfil','dificultad'];
foreach ($extraFilterKeys as $k) {
    if (isset($_REQUEST[$k]) && $_REQUEST[$k] !== '') {
        $filtrosAplicados[] = htmlspecialchars(ucfirst($k) . ': ' . $_REQUEST[$k]);
    }
}
$filtrosStr = empty($filtrosAplicados) ? 'Ninguno' : implode(', ', $filtrosAplicados);

// Generar ruta del logo para Dompdf
// Usar ruta relativa al chroot establecido en Dompdf
$canRenderImages = (extension_loaded('gd') || extension_loaded('imagick') || class_exists('Imagick'));
$logoPath = '../assets/css/images/LogoMeta.png';
$logoFileExists = file_exists(__DIR__ . '/../assets/css/images/LogoMeta.png');
$logoHtml = '';

if ($canRenderImages && $logoFileExists) {
    $logoHtml = '<img src="' . $logoPath . '" alt="MetaHogar Logo" style="max-width: 120px; height: auto;">';
} elseif (!$canRenderImages) {
    $logoHtml = '<div class="logo-placeholder"><strong>MetaHogar</strong><div class="logo-note">Habilita GD o Imagick para mostrar el logo</div></div>';
} else {
    $logoHtml = '<div class="logo-placeholder"><strong>MetaHogar</strong><div class="logo-note">Logo no disponible</div></div>';
}

$html = '<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>' . htmlspecialchars($tituloReporte) . '</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif; 
            color: #333; 
            margin: 20px;
            padding: 0;
        }
        .header {
            display: table; 
            width: 100%; 
            margin-bottom: 30px; 
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
            table-layout: fixed;
        }
        .logo-section { 
            display: table-cell; 
            vertical-align: top; 
            width: 120px;
            padding-right: 20px;
        }
        .logo-section img { 
            max-width: 120px; 
            height: auto; 
            display: block;
        }
        .logo-placeholder {
            width: 120px;
            padding: 8px;
            border: 1px dashed #bbb;
            text-align: center;
            font-size: 9pt;
            color: #555;
        }
        .logo-note {
            margin-top: 4px;
            font-size: 7pt;
            color: #999;
        }
        .date-section { 
            display: table-cell; 
            vertical-align: top; 
            text-align: right;
            width: auto;
        }
        .date-section div { 
            margin: 5px 0; 
            font-size: 10pt; 
        }
        .date-label { 
            font-weight: bold; 
            color: #0056b3; 
        }
        h1 { 
            color: #007bff; 
            font-size: 20pt; 
            margin: 15px 0 10px 0; 
        }
        .info-box {
            background-color: #f8f9fa; 
            border-left: 4px solid #007bff; 
            padding: 12px;
            margin-bottom: 20px; 
            page-break-inside: avoid;
        }
        .info-box p { 
            margin: 6px 0; 
            font-size: 9pt; 
        }
        .info-box strong { 
            color: #0056b3; 
        }
        h3 { 
            color: #0056b3; 
            margin-top: 20px; 
            margin-bottom: 8px; 
            border-bottom: 1px solid #dee2e6; 
            padding-bottom: 5px;
            font-size: 12pt;
            page-break-inside: avoid;
        }
        table { 
            border-collapse: collapse; 
            width: 100%; 
            margin-top: 8px; 
            font-size: 8pt;
            page-break-inside: auto;
        }
        tr { page-break-inside: avoid; }
        th { 
            background-color: #007bff; 
            color: white; 
            padding: 8px; 
            text-align: left; 
            font-weight: bold;
        }
        td { 
            border: 1px solid #dee2e6; 
            padding: 6px; 
        }
        tr:nth-child(even) { 
            background-color: #f8f9fa; 
        }
        .footer { 
            margin-top: 30px; 
            text-align: center; 
            border-top: 1px solid #dee2e6; 
            padding-top: 10px; 
            font-size: 7pt; 
            color: #666; 
        }
        .metric-table th { 
            background-color: #f8f9fa; 
            color: #333; 
            font-weight: bold; 
        }
        .charts-section { 
            margin-top: 20px; 
            page-break-inside: avoid;
        }
        .charts-section img {
            max-width: 100%;
            height: auto;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-section">
            ' . $logoHtml . '
        </div>
        <div class="date-section">
            <div><span class="date-label">Fecha de Reporte:</span></div>
            <div>' . $now->format('d/m/Y') . '</div>
            <div><span class="date-label">Hora:</span></div>
            <div>' . $now->format('H:i:s') . '</div>
        </div>
    </div>

    <h1>' . htmlspecialchars($tituloReporte) . '</h1>
    
    <div class="info-box">
        <p><strong>Empresa:</strong> MetaHogar</p>
        <p><strong>Filtros Aplicados:</strong> ' . ($filtrosStr === 'Ninguno' ? '<em>' . $filtrosStr . '</em>' : $filtrosStr) . '</p>';


if (!$is_analitico) {
    $html .= '<p><strong>Total de Registros:</strong> ' . $total_registros . '</p>';
}

$html .= '    </div>';

// Charts handling (pueden venir por POST o REQUEST)
$chartHtml = '';

if (!$canRenderImages) {
    // Si enviaron imágenes pero el servidor no puede procesarlas, avisar en el PDF
    if (!empty($_REQUEST['chart_image_demanda']) || !empty($_REQUEST['chart_image_latencia'])) {
        $chartHtml .= '<h2 style="color:#dc3545;">Gráficas (no incluidas)</h2>';
        $chartHtml .= '<p>Las gráficas no se han incluido porque la extensión <strong>GD</strong> ni <strong>Imagick</strong> no están disponibles en PHP. Habilita GD o Imagick para incluir imágenes en el PDF.</p>';
    }
} else {
    if (!empty($_REQUEST['chart_image_demanda'])) {
        $img = $_REQUEST['chart_image_demanda'];
        if (strpos($img, 'data:') !== 0) {
            $img = 'data:image/png;base64,' . $img;
        }
        $chartHtml .= '<h2 style="color:#007bff;">Gráficas</h2>';
        $chartHtml .= '<div><img src="' . $img . '" style="max-width:100%; height:auto; margin-bottom: 8px;" /></div>';
    }
    if (!empty($_REQUEST['chart_image_latencia'])) {
        $img2 = $_REQUEST['chart_image_latencia'];
        if (strpos($img2, 'data:') !== 0) {
            $img2 = 'data:image/png;base64,' . $img2;
        }
        $chartHtml .= '<div><img src="' . $img2 . '" style="max-width:100%; height:auto; margin-bottom: 8px;" /></div>';
    }
}

// Añadir charts (si existen) y la tabla generada
if (!empty($chartHtml)) {
    $html .= '<div class="charts-section">' . $chartHtml . '</div>';
}
$html .= $htmlTabla;

$html .= '<div class="footer"><p>Documento generado automáticamente por MetaHogar Admin | © ' . $now->format('Y') . ' - Todos los derechos reservados</p></div>';

// Cerrar documento HTML
$html .= "\n</body>\n</html>";

// 4. Configurar y generar PDF con Dompdf
$autoloadPath = dirname(__DIR__, 2) . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    http_response_code(500);
    echo '<h1>Dependencia faltante: Composer autoload</h1>';
    echo '<p>Ejecuta en la raíz del proyecto: <code>composer install</code> o <code>composer require dompdf/dompdf "^2.0"</code></p>';
    exit;
}

require_once $autoloadPath;
if (!class_exists(\Dompdf\Dompdf::class) || !class_exists(\Dompdf\Options::class)) {
    http_response_code(500);
    echo '<h1>Dependencia faltante: Dompdf</h1>';
    echo '<p>Instala Dompdf ejecutando: <code>composer require dompdf/dompdf "^2.0"</code></p>';
    exit;
}

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->setDefaultFont('Helvetica');
$options->setIsHtml5ParserEnabled(true);
$options->setIsRemoteEnabled(true);
$options->setChroot(__DIR__ . '/../../'); // Raíz en raíz del proyecto para resolver rutas correctamente

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
// Establecer márgenes en milímetros
$dompdf->set_option('margin_top', 15);
$dompdf->set_option('margin_right', 10);
$dompdf->set_option('margin_bottom', 15);
$dompdf->set_option('margin_left', 10);
$dompdf->render();

// 5. Enviar el PDF al navegador
$fileName = str_replace(' ', '_', $tituloReporte) . '_' . date('Ymd') . '.pdf';
$dompdf->stream($fileName, ["Attachment" => false]);
exit(0);
?>
