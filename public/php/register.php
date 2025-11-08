<?php
// public/php/register.php
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/models/Usuario.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../signup.php'); exit;
    }

    $nombre = trim($_POST['nombres'] ?? '');
    $apellidop = trim($_POST['apellido_paterno'] ?? '');
    $apellidom = trim($_POST['apellido_materno'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($nombre === '' || $email === '' || $password === '') {
        header('Location: ../signup.php?error=' . urlencode('Faltan campos obligatorios'));
        exit;
    }
    if ($password !== $confirm) {
        header('Location: ../signup.php?error=' . urlencode('Las contraseñas no coinciden'));
        exit;
    }

    // Verificar email existente
    $existing = Usuario::findByEmail($pdo, $email);
    if ($existing) {
        header('Location: ../signup.php?error=' . urlencode('Ya existe una cuenta con ese correo'));
        exit;
    }

    // Obtener idRol para 'Usuario'. Si no existe, crear.
    $stmt = $pdo->prepare('SELECT idRol FROM rol WHERE Nombre = ? LIMIT 1');
    $stmt->execute(['Usuario']);
    $rolId = $stmt->fetchColumn();
    if (!$rolId) {
        $stmt2 = $pdo->prepare('INSERT INTO rol (Nombre, Descripcion) VALUES (?, ?)');
        $stmt2->execute(['Usuario', 'Ciudadano / Familiar / Adulto mayor']);
        $rolId = $pdo->lastInsertId();
    }

    // Crear usuario: Usuario::crear espera 'rol' en $data
    $data = [
        'nombre' => $nombre,
        'apellidop' => $apellidop,
        'apellidom' => $apellidom,
        'email' => $email,
        'password' => $password,
        'telefono' => $telefono,
        'activo' => 1,
        'tipo' => 'Usuario',
        'rol' => $rolId
    ];
    Usuario::crear($pdo, $data);

    header('Location: ../signup.php?registered=1');
    exit;

} catch (Exception $e) {
    header('Location: ../signup.php?error=' . urlencode('Error registrando usuario'));
    exit;
}
