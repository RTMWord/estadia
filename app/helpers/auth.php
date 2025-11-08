<?php
// helper simple de autenticación y autorización
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLogged() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLogged()) {
        $req = $_SERVER['REQUEST_URI'] ?? '/Estadia/public/index.php';
        header('Location: /Estadia/public/login.php?next=' . urlencode($req));
        exit;
    }
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function requireRole($pdo, $roleName) {
    if (!isLogged()) {
        $req = $_SERVER['REQUEST_URI'] ?? '/Estadia/public/index.php';
        header('Location: /Estadia/public/login.php?next=' . urlencode($req));
        exit;
    }
    $userId = getUserId();
    $stmt = $pdo->prepare('SELECT r.Nombre FROM UsuarioRol ur JOIN Rol r ON ur.Rol_idRol = r.idRol WHERE ur.Usuario_idUsuario = ? LIMIT 1');
    $stmt->execute([$userId]);
    $rol = $stmt->fetchColumn();
    if ($rol !== $roleName) {
        header('HTTP/1.1 403 Forbidden');
        echo 'Acceso denegado';
        exit;
    }
}
?>