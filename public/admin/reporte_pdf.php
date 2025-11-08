<?php
require_once '../../app/config/db.php';
require_once '../../app/helpers/auth.php';
require_once '../../app/controllers/ReporteController.php';
requireRole($pdo, 'administrador');

// 1. Incluir autoload de Composer (para Dompdf)
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// 2. Obtener datos del reporte (misma lógica que reportes.php)
$reporteCtrl = new ReporteController($pdo);
$reporteActual = $_GET['reporte'] ?? 'citas'; 
$filtros = [
    'estado' => $_GET['estado'] ?? '',
    'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
    'fecha_fin' => $_GET['fecha_fin'] ?? '',
];

$tituloReporte = "Reporte Desconocido";
$htmlTabla = "";

if ($reporteActual === 'citas') {
    $resultados = $reporteCtrl->getCitasReporte($filtros);
    $tituloReporte = "Reporte de Citas";
    
    // Generar la tabla HTML
    $htmlTabla .= '
        <table border="1" width="100%" cellspacing="0" cellpadding="5">
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
            <tbody>';
    
    foreach ($resultados as $r) {
        $htmlTabla .= '
            <tr>
                <td>' . $r['idCita'] . '</td>
                <td>' . htmlspecialchars($r['Usuario']) . '</td>
                <td>' . htmlspecialchars($r['Email']) . '</td>
                <td>' . htmlspecialchars($r['Servicio']) . '</td>
                <td>' . $r['FechaHora'] . '</td>
                <td>' . $r['Estado'] . '</td>
            </tr>';
    }
    $htmlTabla .= '</tbody></table>';

}
// Aquí se añadiría la lógica similar para 'usuarios', 'servicios', 'productos'

// 3. Crear el HTML completo para Dompdf
$html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>'.$tituloReporte.'</title>
        <style>
            body { font-family: sans-serif; }
            h1 { color: #007bff; }
            table { border-collapse: collapse; width: 100%; margin-top: 20px; }
            th, td { border: 1px solid #ccc; padding: 8px; text-align: left; font-size: 10pt; }
            th { background-color: #f8f9fa; }
            .info { margin-bottom: 20px; }
        </style>
    </head>
    <body>
        <h1>'.$tituloReporte.' de MetaHogar</h1>
        <div class="info">
            <p><strong>Fecha de Generación:</strong> '.date('Y-m-d H:i:s').'</p>
            <p><strong>Filtros Aplicados:</strong> Estado: '.($filtros['estado'] ?? 'Todos').', Desde: '.($filtros['fecha_inicio'] ?? 'N/A').', Hasta: '.($filtros['fecha_fin'] ?? 'N/A').'</p>
            <p><strong>Total de Registros:</strong> '.count($resultados).'</p>
        </div>
        '.$htmlTabla.'
    </body>
    </html>';

// 4. Configurar y generar PDF con Dompdf
$options = new Options();
$options->set('defaultFont', 'Helvetica');
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape'); // Orientación horizontal para más columnas
$dompdf->render();

// 5. Enviar el PDF al navegador
$dompdf->stream(str_replace(' ', '_', $tituloReporte) . '_' . date('Ymd') . '.pdf', ["Attachment" => false]);
exit(0);
?>