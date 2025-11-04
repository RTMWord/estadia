<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/auth.php';
// proteger que solo administradores puedan ejecutar estas acciones
requireRole($pdo, 'administrador');

// Cambiar estado de validación de agencia (GET)
if (isset($_GET['accion']) && isset($_GET['id'])) {
    $accion = $_GET['accion'];
    $id = (int)$_GET['id'];
    $estado = null;
    if ($accion === 'aprobar') $estado = 'APROBADA';
    if ($accion === 'rechazar') $estado = 'RECHAZADA';
    if ($accion === 'pendiente') $estado = 'PENDIENTE';
    if ($estado) {
        $stmt = $pdo->prepare('UPDATE Agencia SET EstadoValidacion = ? WHERE idAgencia = ?');
        $stmt->execute([$estado, $id]);
    }
    header('Location: ../../public/admin/agencias.php');
    exit;
}

// Crear o editar (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear'])) {
        $stmt = $pdo->prepare('INSERT INTO Agencia (Nombre, Contacto, Telefono, Email, Direccion, EstadoValidacion) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$_POST['nombre'], $_POST['contacto'], $_POST['telefono'], $_POST['email'], $_POST['direccion'], 'PENDIENTE']);
        header('Location: ../../public/admin/agencias.php');
        exit;
    }
    if (isset($_POST['editar'])) {
        $stmt = $pdo->prepare('UPDATE Agencia SET Nombre=?, Contacto=?, Telefono=?, Email=?, Direccion=? WHERE idAgencia=?');
        $stmt->execute([$_POST['nombre'], $_POST['contacto'], $_POST['telefono'], $_POST['email'], $_POST['direccion'], $_POST['id']]);
        header('Location: ../../public/admin/agencias.php');
        exit;
    }
}

?>