<?php
// public/admin/servicio_eliminar.php
session_start();
require_once __DIR__ . '/../../app/controllers/ServicioController.php';
$ctrl = new ServicioController();

if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: ../servicios.php');
    exit;
}

$res = $ctrl->eliminar($id, $_SESSION);
if ($res['ok']) {
    header('Location: ../servicios.php');
    exit;
} else {
    echo "Error al eliminar: " . htmlspecialchars($res['error'] ?? 'desconocido');
    exit;
}
?>