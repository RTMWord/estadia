<?php
require_once __DIR__ . '/_security_check.php';

$dataFile = __DIR__ . '/../../data/diagnosticos.json';
$entries = [];
if (is_file($dataFile)) {
    $raw = @file_get_contents($dataFile);
    $entries = $raw ? json_decode($raw, true) : [];
    if (!is_array($entries)) $entries = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Solicitudes de Diagnóstico - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/partials/admin_nav.php'; ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4">Solicitudes de Diagnóstico</h1>
        <div>
            <a href="index.php" class="btn btn-outline-secondary">Volver</a>
            <a href="../../data/diagnosticos.json" class="btn btn-outline-primary" download>Exportar JSON</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <p class="text-muted">Total: <strong><?= count($entries) ?></strong></p>
            <?php if (empty($entries)): ?>
                <div class="alert alert-info">No hay solicitudes registradas todavía.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Ciudad</th>
                                <th>Vivienda</th>
                                <th>Dificultades</th>
                                <th>Intereses</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        // Maps for human-friendly labels
                        $perfilMap = [
                            'adulto_mayor' => 'Adulto mayor',
                            'hijo_a' => 'Hijo/a',
                            'nieto_a' => 'Nieto/a',
                            'otro_familiar' => 'Otro familiar'
                        ];
                        $edadMap = [
                            'age_55_60' => '55-60',
                            'age_60_65' => '60-65',
                            'age_66_75' => '66-75',
                            'age_76_plus' => '76 o más'
                        ];
                        $viviendaMap = [
                            'casa_1_nivel' => 'Casa (1 nivel)',
                            'casa_2_mas' => 'Casa (2+ niveles)',
                            'departamento' => 'Departamento'
                        ];
                        $dificMap = [
                            'movilidad'=>'Movilidad','caidas'=>'Caídas','vista'=>'Vista','audicion'=>'Audición','preventivo'=>'Prevenir / Ninguna'
                        ];
                        $interesMap = [
                            'mejorar_caidas'=>'Caídas','mejorar_movilidad'=>'Facilidad de movimiento','mejorar_confort'=>'Confort','mejorar_monitoreo'=>'Monitoreo','mejorar_independencia'=>'Independencia'
                        ];
                        $espMap = [
                            'esp_bano'=>'Baño','esp_recamara'=>'Recámara','esp_cocina'=>'Cocina','esp_escaleras'=>'Escaleras','esp_toda'=>'Toda la vivienda'
                        ];

                        foreach (array_reverse($entries) as $e):
                            $id = htmlspecialchars($e['id'] ?? '');
                            $created = htmlspecialchars($e['created_at'] ?? '');
                            $name = htmlspecialchars($e['contact']['nombre'] ?? '');
                            $email = htmlspecialchars($e['contact']['email'] ?? '');
                            $ciudad = htmlspecialchars($e['contact']['ciudad'] ?? '');
                            $vivi = $viviendaMap[$e['tipo_vivienda'] ?? ''] ?? ($e['tipo_vivienda'] ?? '');
                            $difs = [];
                            foreach ($e['dificultades'] ?? [] as $d) { $difs[] = $dificMap[$d] ?? $d; }
                            $ints = [];
                            foreach ($e['intereses'] ?? [] as $i) { $ints[] = $interesMap[$i] ?? $i; }
                            $difsShort = htmlspecialchars(implode(', ', array_slice($difs,0,3)) ?: '-');
                            $intsShort = htmlspecialchars(implode(', ', array_slice($ints,0,3)) ?: '-');

                            // Build human readable detail HTML
                            $detailParts = [];
                            $detailParts[] = '<p><strong>ID:</strong> ' . $id . '</p>';
                            $detailParts[] = '<p><strong>Fecha:</strong> ' . $created . '</p>';
                            $detailParts[] = '<p><strong>Contacto:</strong> ' . $name . ' — ' . $email . ' — ' . $ciudad . '</p>';
                            $perfilLabel = $perfilMap[$e['perfil'] ?? ''] ?? ($e['perfil'] ?? '');
                            $edadLabel = $edadMap[$e['edad'] ?? ''] ?? ($e['edad'] ?? '');
                            $detailParts[] = '<p><strong>Perfil / Edad:</strong> ' . htmlspecialchars($perfilLabel . ' / ' . $edadLabel) . '</p>';
                            $detailParts[] = '<p><strong>Tipo de vivienda:</strong> ' . htmlspecialchars($vivi) . '</p>';
                            $detailParts[] = '<p><strong>Dificultades:</strong> ' . htmlspecialchars(implode(', ', $difs) ?: '-') . '</p>';
                            $detailParts[] = '<p><strong>Intereses:</strong> ' . htmlspecialchars(implode(', ', $ints) ?: '-') . '</p>';
                            $detailParts[] = '<p><strong>Espacios de interés:</strong> ' . htmlspecialchars(implode(', ', array_map(function($x) use ($espMap){ return $espMap[$x] ?? $x; }, $e['espacios'] ?? [])) ?: '-') . '</p>';
                            $detailParts[] = '<p><strong>Nivel tecnológico:</strong> ' . htmlspecialchars($e['tec_nivel'] ?? '-') . '</p>';
                            $detailParts[] = '<p><strong>Plazo:</strong> ' . htmlspecialchars($e['plazo'] ?? '-') . '</p>';
                            $detailHtml = implode("\n", $detailParts);

                        ?>
                            <tr>
                                <td><?= $id ?></td>
                                <td><?= $created ?></td>
                                <td><?= $name ?></td>
                                <td><?= $email ?></td>
                                <td><?= $ciudad ?></td>
                                <td><?= htmlspecialchars($vivi) ?></td>
                                <td><?= $difsShort ?></td>
                                <td><?= $intsShort ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-secondary btn-view" data-detail='<?= htmlspecialchars($detailHtml, ENT_QUOTES) ?>'>Ver</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="diagModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detalle de la solicitud</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <pre id="diagDetail" style="white-space:pre-wrap;">Cargando...</pre>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const modalEl = document.getElementById('diagModal');
    const diagDetail = document.getElementById('diagDetail');
    const bsModal = new bootstrap.Modal(modalEl);

    document.querySelectorAll('.btn-view').forEach(btn => {
        btn.addEventListener('click', function(){
            const html = this.getAttribute('data-detail') || 'Sin detalle disponible.';
            diagDetail.innerHTML = html;
            bsModal.show();
        });
    });
});
</script>
</body>
</html>
