<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

if (!isLogged()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado. Inicia sesión.']);
    exit;
}

$userId = getUserId();

$servicio = $_POST['servicio'] ?? null;
$fecha = $_POST['fecha_hora'] ?? null;
$notas = $_POST['notas'] ?? '';

if (!$servicio || !$fecha) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos.']);
    exit;
}

// Normalizar fecha-local (HTML5) a formato MySQL
$ts = strtotime(str_replace('T', ' ', $fecha));
if ($ts === false) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Formato de fecha inválido.']);
    exit;
}
$fecha_mysql = date('Y-m-d H:i:s', $ts);

try {
    // Validar que el servicio exista y esté activo
    $stm = $pdo->prepare('SELECT COUNT(*) FROM servicio WHERE idServicio = ? AND Activo = 1');
    $stm->execute([$servicio]);
    if (!$stm->fetchColumn()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Servicio inválido.']);
        exit;
    }

    $ins = $pdo->prepare('INSERT INTO cita (Usuario_idUsuario, Servicio_idServicio, FechaHora, Estado, Notas) VALUES (?, ?, ?, ?, ?)');
    $ins->execute([$userId, $servicio, $fecha_mysql, 'PENDIENTE', $notas]);

    echo json_encode(['success' => true, 'message' => 'Cita solicitada correctamente. Te contactaremos para confirmar.']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al guardar la cita.']);
}

?>
