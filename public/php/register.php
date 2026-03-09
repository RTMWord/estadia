<?php
// public/php/register.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/models/Usuario.php';
require_once __DIR__ . '/../../app/config/email.php';

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $response['message'] = 'Método no permitido';
        echo json_encode($response);
        exit;
    }

    $nombre = trim($_POST['nombres'] ?? '');
    $apellidop = trim($_POST['apellido_paterno'] ?? '');
    $apellidom = trim($_POST['apellido_materno'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // Validaciones
    if ($nombre === '' || $email === '' || $password === '') {
        $response['message'] = 'Faltan campos obligatorios';
        echo json_encode($response);
        exit;
    }
    
    if ($password !== $confirm) {
        $response['message'] = 'Las contraseñas no coinciden';
        echo json_encode($response);
        exit;
    }
    
    if (strlen($password) < 6) {
        $response['message'] = 'La contraseña debe tener mínimo 6 caracteres';
        echo json_encode($response);
        exit;
    }

    // Verificar email existente
    $existing = Usuario::findByEmail($pdo, $email);
    if ($existing) {
        $response['message'] = 'Ya existe una cuenta con ese correo';
        echo json_encode($response);
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

    // Crear usuario
    $data = [
        'nombre' => $nombre,
        'apellidop' => $apellidop,
        'apellidom' => $apellidom,
        'email' => $email,
        'password' => $password,
        'telefono' => $telefono,
        'activo' => 1,  // La cuenta se activa inmediatamente
        'tipo' => 'Usuario',
        'rol' => $rolId
    ];
    Usuario::crear($pdo, $data);
    
    // Enviar email de bienvenida
    $nombreCompleto = $nombre . ' ' . $apellidop . ' ' . $apellidom;
    $emailSent = sendWelcomeEmail($email, $nombreCompleto);
    
    $response['success'] = true;
    $response['message'] = 'Tu cuenta ha sido creada exitosamente. ¡Bienvenido a MetaHogar!';
    
    echo json_encode($response);
    exit;

} catch (Exception $e) {
    $response['message'] = 'Error al registrar el usuario: ' . $e->getMessage();
    echo json_encode($response);
    exit;
}
