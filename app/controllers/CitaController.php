<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Cita.php';
require_once __DIR__ . '/../helpers/auth.php';

// Crear sesión/obtener usuario logueado
// requireRole/requireLogin no se invoca aquí porque la creación la harán usuarios logueados desde el frontend

if (isset($_POST['crear'])) {
    // asignar usuario desde sesión (si existe)
    $userId = getUserId();
    if (!$userId) {
        // No hay usuario en sesión: redirigimos al login
        header('Location: ../../public/login.php');
        exit;
    }
    // Forzar datos esperados por el modelo: usuario, estado por defecto
    $_POST['usuario'] = $userId;
    $_POST['estado'] = 'AGENDADA';
    // Normalizar fecha tipo datetime-local (YYYY-MM-DDTHH:MM) a 'YYYY-MM-DD HH:MM:SS'
    if (!empty($_POST['fechahora'])) {
        $fh = str_replace('T', ' ', $_POST['fechahora']);
        // si viene sin segundos, agregar :00
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $fh)) {
            $fh .= ':00';
        }
        $_POST['fechahora'] = $fh;
    }
    Cita::crear($pdo, $_POST);
    header('Location: ../../public/citas.php');
    exit;
}
if (isset($_POST['editar'])) {
    Cita::editar($pdo, $_POST);
    header('Location: ../../public/citas.php');
    exit;
}
if (isset($_GET['eliminar'])) {
    Cita::eliminar($pdo, $_GET['eliminar']);
    header('Location: ../../public/citas.php');
    exit;
}
?>
