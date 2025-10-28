<?php
require_once '../config/db.php';
require_once '../models/Cita.php';

if (isset($_POST['crear'])) {
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
