<?php
// public/admin/servicio_eliminar.php
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';
require_once __DIR__ . '/../../app/controllers/ServicioController.php';

// Verificar sesión/rol utilizando el helper centralizado
requireRole($pdo, 'administrador');

$ctrl = new ServicioController();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: ../servicios.php');
    exit;
}

$res = $ctrl->eliminar($id, $_SESSION);
if (!empty($res['ok'])) {
    header('Location: ../servicios.php');
    exit;
} else {
    echo "Error al eliminar: " . htmlspecialchars($res['error'] ?? 'desconocido');
    exit;
}
?>