<?php
require_once '../config/db.php';
require_once '../models/Usuario.php';

if (isset($_POST['crear'])) {
    Usuario::crear($pdo, $_POST);
    header('Location: ../../public/admin/usuarios.php');
    exit;
}
if (isset($_POST['editar'])) {
    Usuario::editar($pdo, $_POST);
    header('Location: ../../public/admin/usuarios.php');
    exit;
}
if (isset($_GET['eliminar'])) {
    Usuario::eliminar($pdo, $_GET['eliminar']);
    header('Location: ../../public/admin/usuarios.php');
    exit;
}
?>
