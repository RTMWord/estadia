<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../../app/config/db.php';
require_once __DIR__ . '/../../../app/models/Testimonio.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $calificacion = isset($_POST['calificacion']) ? (int)$_POST['calificacion'] : 5;
    $testimonio = trim($_POST['testimonio'] ?? '');

    if ($nombre === '' || $testimonio === '') {
        echo json_encode(['success' => false, 'message' => 'Nombre y testimonio son obligatorios']);
        exit;
    }

    $id = Testimonio::crear($pdo, [
        'nombre' => $nombre,
        'email' => $email,
        'calificacion' => $calificacion,
        'testimonio' => $testimonio,
        'aprobado' => 0 // se moderan desde admin
    ]);

    echo json_encode(['success' => true, 'id' => $id]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
