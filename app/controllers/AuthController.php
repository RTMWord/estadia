<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Usuario.php';

/***/
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Usuario.php';
session_start();
// NUEVO: Incluir la función de envío de correo
require_once __DIR__ . '/../config/email.php';


session_start();

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $usuario = Usuario::findByEmail($pdo, $email);
        // Verificar bloqueo
        if (isset($usuario['BloqueadoHasta']) && $usuario['BloqueadoHasta'] && strtotime($usuario['BloqueadoHasta']) > time()) {
            header('Location: ../../public/login.php?error=2');
            exit;
        }
        // Verificar credenciales
        if ($usuario && $usuario['Activo'] && password_verify($password, $usuario['PasswordHash'])) {
                Usuario::registrarIntento($pdo, $usuario['idUsuario'], false); // resetear intentos
                // cargar rol en sesión
                $stmt = $pdo->prepare('SELECT r.Nombre FROM UsuarioRol ur JOIN Rol r ON ur.Rol_idRol = r.idRol WHERE ur.Usuario_idUsuario = ? LIMIT 1');
                $stmt->execute([$usuario['idUsuario']]);
                $rol = $stmt->fetchColumn();
                $_SESSION['user_id'] = $usuario['idUsuario'];
                $_SESSION['role'] = $rol;
                // Redirección segura al origen si se proporcionó
                $next = $_POST['next'] ?? '';
                if ($next && strpos($next, '/') === 0) {
                    // prevenir redirecciones abiertas hacia otros hosts
                    header('Location: ' . $next);
                } else {
                    header('Location: ../../public/index.php');
                }
                exit;
            } else {
            if ($usuario) {
                Usuario::registrarIntento($pdo, $usuario['idUsuario'], true);
                // Si supera 5 intentos, bloquear
                if (isset($usuario['IntentosFallidos']) && $usuario['IntentosFallidos'] >= 4) {
                    Usuario::bloquearUsuario($pdo, $usuario['idUsuario']);
                }
            }
            header('Location: ../../public/login.php?error=1');
            exit;
        }
}

if (isset($_POST['forgot'])) {
    $email = $_POST['email'];
    $usuario = Usuario::findByEmail($pdo, $email);
    
    // NOTA DE SEGURIDAD: Siempre muestre un mensaje genérico para evitar revelar
    // si un correo existe o no en la base de datos.
    
    if ($usuario) {
        // 1. Generar token único (64 caracteres)
        $token = bin2hex(random_bytes(32)); 
        // 2. Establecer tiempo de expiración (1 hora)
        $expires = date("Y-m-d H:i:s", time() + 3600); 

        // 3. Guardar token y expiración en la base de datos
        $stmt = $pdo->prepare('UPDATE Usuario SET PasswordResetToken = ?, PasswordResetExpires = ? WHERE idUsuario = ?');
        $updated = $stmt->execute([$token, $expires, $usuario['idUsuario']]);

        // 4. Enviar correo electrónico
        if ($updated) {
            $sent = sendPasswordResetEmail($usuario['Email'], $usuario['Nombre'], $token);
            
            if ($sent) {
                header('Location: ../../public/forgot.php?sent=1');
                exit;
            } else {
                error_log("Fallo al enviar correo de restablecimiento a " . $email);
                // No mostrar error específico al usuario final por seguridad.
            }
        }
    }
    
    // Redirigir siempre a la página de éxito/mensaje genérico
    header('Location: ../../public/forgot.php?sent=1');
    exit;
}

// NUEVO: Lógica para procesar la nueva contraseña
if (isset($_POST['reset_password'])) {
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validaciones básicas del formulario
    if (empty($token) || $password !== $confirm_password || strlen($password) < 6) {
        header('Location: ../../public/reset_password.php?token=' . urlencode($token) . '&error=1');
        exit;
    }

    // Buscar usuario por token
    $stmt = $pdo->prepare('SELECT idUsuario, PasswordResetExpires FROM Usuario WHERE PasswordResetToken = ? LIMIT 1');
    $stmt->execute([$token]);
    $usuario = $stmt->fetch();

    if (!$usuario || strtotime($usuario['PasswordResetExpires']) < time()) {
        header('Location: ../../public/reset_password.php?error=2'); // Token inválido o caducado
        exit;
    }
    
    // 1. Hashear y actualizar contraseña
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    
    // 2. Limpiar token/expiración y restablecer intentos de login
    $stmt2 = $pdo->prepare('UPDATE Usuario SET PasswordHash = ?, PasswordResetToken = NULL, PasswordResetExpires = NULL, IntentosFallidos = 0, BloqueadoHasta = NULL WHERE idUsuario = ?');
    $stmt2->execute([$newHash, $usuario['idUsuario']]);

    // Redirigir al login con mensaje de éxito
    header('Location: ../../public/login.php?reset=1');
    exit;
}
?>
