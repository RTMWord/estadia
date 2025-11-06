<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../../app/config/db.php';
require_once __DIR__ . '/../../../app/models/Testimonio.php';

try {
    $list = Testimonio::getPublic($pdo);
    echo json_encode(['success' => true, 'testimonios' => $list]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
