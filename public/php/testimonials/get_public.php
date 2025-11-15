<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../../app/config/db.php';
require_once __DIR__ . '/../../../app/models/Testimonio.php';

try {
    $list = Testimonio::getPublic($pdo);
    // Normalize keys to a predictable lowercase shape for the frontend
    $out = [];
    foreach ($list as $r) {
        $out[] = [
            'id' => isset($r['idTestimonio']) ? (int)$r['idTestimonio'] : (isset($r['id'])?(int)$r['id']:null),
            'nombre' => $r['Nombre'] ?? $r['nombre'] ?? '',
            'email' => $r['Email'] ?? $r['email'] ?? '',
            'calificacion' => isset($r['Calificacion']) ? (int)$r['Calificacion'] : (isset($r['calificacion'])?(int)$r['calificacion']:5),
            'testimonio' => $r['Testimonio'] ?? $r['testimonio'] ?? '',
            // use fecha_creacion to match frontend expectation
            'fecha_creacion' => $r['FechaCreacion'] ?? $r['fecha_creacion'] ?? null,
        ];
    }
    echo json_encode(['success' => true, 'testimonios' => $out]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
