<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Usuario.php';
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
    if ($usuario) {
        // Generar token temporal y enviar correo (implementación pendiente)
        // ...
        header('Location: ../../public/forgot.php?sent=1');
        exit;
    } else {
        header('Location: ../../public/forgot.php?error=1');
        exit;
    }
}
?>
