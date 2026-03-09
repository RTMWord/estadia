<?php
// debug_servicio.php - breve diagnóstico
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/controllers/ServicioController.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
// No requireRole here — admin only use recommended but not enforced for debug

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "Provide ?id=...\n";
    exit;
}

$ctrl = new ServicioController();
$resCtrl = null;
try {
    $resCtrl = $ctrl->detalle($id);
} catch (Throwable $e) {
    $resCtrl = ['error' => $e->getMessage()];
}

// consulta directa con PDO
$direct = null;
try {
    global $pdo;
    if (!empty($pdo)) {
        $stmt = $pdo->prepare('SELECT * FROM servicio WHERE idServicio = ? LIMIT 1');
        $stmt->execute([$id]);
        $direct = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    $direct = ['error' => $e->getMessage()];
}

header('Content-Type: text/plain; charset=utf-8');
echo "=== controlador->detalle($id) ===\n";
var_export($resCtrl);
echo "\n\n=== consulta directa (servicio) ===\n";
var_export($direct);
echo "\n\n=== tabla servicios (últimos 20) ===\n";
try {
    if (!empty($pdo)) {
        $rows = $pdo->query('SELECT idServicio, Nombre, Activo FROM servicio ORDER BY idServicio DESC LIMIT 20')->fetchAll(PDO::FETCH_ASSOC);
        var_export($rows);
    }
} catch (Exception $e) {
    echo 'Error listing: ' . $e->getMessage();
}

?>