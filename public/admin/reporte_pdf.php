<?php
require_once '../../app/config/db.php';
require_once '../../app/helpers/auth.php';
require_once '../../app/controllers/ReporteController.php';
requireRole($pdo, 'administrador');

// 1. Incluir autoload de Composer (para Dompdf)
// Se comprueba más abajo para mostrar un mensaje amigable si falta.

use Dompdf\Dompdf;
use Dompdf\Options;

// 2. Obtener datos del reporte
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

$tituloReporte = "Reporte Desconocido";
$htmlTabla = "";
$columnas = [];
$resultados = [];
$is_analitico = false;
$analitico_data = [];

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
}

$total_registros = 0;
$htmlTabla = '';

if ($is_analitico) {
    foreach ($analitico_data as $seccion_nombre => $datos_seccion) {
        $htmlTabla .= '<h3 style="color:#0056b3; margin-top:15px; border-bottom: 1px solid #eee;">' . htmlspecialchars(str_replace('_', ' ', $seccion_nombre)) . '</h3>';

        if (empty($datos_seccion)) {
            $htmlTabla .= '<p>No hay datos disponibles para esta sección.</p>';
            continue;
        }

        if (array_key_exists(0, $datos_seccion) && is_array($datos_seccion[0])) {
            // Es un array de arrays (tabla)
            $total_registros += count($datos_seccion);
            $keys = array_keys($datos_seccion[0]);
            
            $htmlTabla .= '<table border="1" width="100%" cellspacing="0" cellpadding="5">';
            $htmlTabla .= '<thead><tr>';
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

        } else {
            // Es un array asociativo simple (resumen de métricas)
            $htmlTabla .= '<table border="1" width="50%" cellspacing="0" cellpadding="5">';
            $htmlTabla .= '<tbody>';
            foreach ($datos_seccion as $key => $val) {
                $htmlTabla .= '<tr><td style="font-weight:bold;">' . htmlspecialchars($key) . '</td><td>' . htmlspecialchars($val) . '</td></tr>';
            }
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

    $htmlTabla .= '<table border="1" width="100%" cellspacing="0" cellpadding="5"><thead><tr>';
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
$filtrosStr = empty($filtrosAplicados) ? 'Ninguno' : implode(', ', $filtrosAplicados);

$html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>'.$tituloReporte.'</title>
        <style>
            body { font-family: sans-serif; }
            h1 { color: #007bff; }
            h3 { color: #0056b3; }
            table { border-collapse: collapse; width: 100%; margin-top: 5px; }
            th, td { border: 1px solid #ccc; padding: 8px; text-align: left; font-size: 10pt; }
            th { background-color: #f8f9fa; }
            .info { margin-bottom: 20px; }
        </style>
    </head>
    <body>
        <h1>'.$tituloReporte.' de MetaHogar</h1>
        <div class="info">
            <p><strong>Fecha de Generación:</strong> '.date('Y-m-d H:i:s').'</p>
            <p><strong>Filtros Aplicados:</strong> '. $filtrosStr .'</p>
            ' . ($is_analitico ? '' : '<p><strong>Total de Registros:</strong> '. $total_registros .'</p>') . '
        </div>
        '.$htmlTabla.'
    </body>
    </html>';

// 4. Configurar y generar PDF con Dompdf
$autoloadPath = dirname(__DIR__, 2) . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    http_response_code(500);
    echo '<h1>Dependencia faltante: Composer autoload</h1>';
    echo '<p>Ejecuta en la raíz del proyecto: <code>composer install</code> o <code>composer require dompdf/dompdf "^2.0"</code></p>';
    exit;
}

// Verificar que las clases de Dompdf estén disponibles y usar la API correcta de Options
require_once $autoloadPath;
if (!class_exists(\Dompdf\Dompdf::class) || !class_exists(\Dompdf\Options::class)) {
    http_response_code(500);
    echo '<h1>Dependencia faltante: Dompdf</h1>';
    echo '<p>Instala Dompdf ejecutando: <code>composer require dompdf/dompdf "^2.0"</code></p>';
    exit;
}

$options = new Options();
$options->setDefaultFont('Helvetica');
$options->setIsHtml5ParserEnabled(true);
$options->setIsRemoteEnabled(true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'landscape'); 

$dompdf->render();

// 5. Enviar el PDF al navegador
$dompdf->stream(str_replace(' ', '_', $tituloReporte) . '_' . date('Ymd') . '.pdf', ["Attachment" => false]);
exit(0);
?>