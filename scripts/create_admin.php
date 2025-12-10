<?php
// Script para crear un usuario administrador inicial.
require_once __DIR__ . '/../app/config/db.php';

$nombre = 'Admin';
$apellidoP = 'Meta';
$apellidoM = 'Hogar';
$email = 'admin@example.com';
$password = 'Admin123!'; // Cambia esta contraseña después
$telefono = '';
$tipo = 'interno';

// Crear rol administrador si no existe
// Crear rol administrador si no existe
$pdo->exec("INSERT INTO Rol (Nombre, Descripcion) SELECT 'administrador','Administrador del sistema' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM Rol WHERE Nombre='administrador')");

// Insertar usuario
// Verificar si el usuario ya existe
$check = $pdo->prepare('SELECT idUsuario FROM Usuario WHERE Email = ? LIMIT 1');
$check->execute([$email]);
$existing = $check->fetchColumn();
if ($existing) {
    echo "El usuario con email $email ya existe. No se creó ninguno.\n";
    exit;
}

$passHash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO Usuario (Nombre, ApellidoP, ApellidoM, Email, PasswordHash, Telefono, Activo, Tipo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->execute([$nombre, $apellidoP, $apellidoM, $email, $passHash, $telefono, 1, $tipo]);
$userId = $pdo->lastInsertId();

// Obtener idRol del administrador
$stmtR = $pdo->prepare('SELECT idRol FROM Rol WHERE Nombre = ? LIMIT 1');
$stmtR->execute(['administrador']);
$idRol = $stmtR->fetchColumn();
if (!$idRol) {
    // debería haberse creado antes, pero por si acaso
    $pdo->exec("INSERT INTO Rol (Nombre, Descripcion) VALUES ('administrador','Administrador del sistema')");
    $idRol = $pdo->lastInsertId();
}

// Asignar rol
$stmt2 = $pdo->prepare('INSERT INTO UsuarioRol (Usuario_idUsuario, Rol_idRol) VALUES (?, ?)');
$stmt2->execute([$userId, $idRol]);

echo "Usuario administrador creado:\nEmail: $email\nPassword: $password\nPor seguridad, cambia esta contraseña después.\n";
?>
