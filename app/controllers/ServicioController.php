<?php
require_once '../config/db.php';
require_once '../models/Servicio.php';

if (isset($_POST['crear'])) {
    Servicio::crear($pdo, $_POST);
    header('Location: ../../public/admin/servicios.php');
    exit;
}
if (isset($_POST['editar'])) {
    Servicio::editar($pdo, $_POST);
    header('Location: ../../public/admin/servicios.php');
    exit;
}
if (isset($_GET['eliminar'])) {
    Servicio::eliminar($pdo, $_GET['eliminar']);
    header('Location: ../../public/admin/servicios.php');
    exit;
}
?>
