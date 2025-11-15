<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../../app/config/db.php';

try {
    // Si la tabla no existe, devolvemos vacío
    $check = $pdo->query("SHOW TABLES LIKE 'carousel_testimonios'")->fetchColumn();
    if (!$check) {
        echo json_encode(['success'=>true, 'images'=>[]]); exit;
    }

    $sql = "SELECT m.idMedia, m.Ruta, m.Descripcion, ct.orden
            FROM carousel_testimonios ct
            INNER JOIN multimedia m ON ct.multimedia_id = m.idMedia
            WHERE ct.activo = 1
            ORDER BY ct.orden ASC, ct.id ASC";
    $stmt = $pdo->query($sql);
    $images = [];
    foreach ($stmt->fetchAll() as $r) {
        $images[] = ['id' => (int)$r['idMedia'], 'ruta' => $r['Ruta'], 'descripcion' => $r['Descripcion'], 'orden' => (int)$r['orden']];
    }
    echo json_encode(['success'=>true, 'images' => $images]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false, 'error' => $e->getMessage()]);
}
