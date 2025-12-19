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
        $req = $_SERVER['REQUEST_URI'] ?? '/estadia/public/index.php';
        header('Location: /estadia/public/login.php?next=' . urlencode($req));
        exit;
    }
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function requireRole($pdo, $roleName) {
    if (!isLogged()) {
        $req = $_SERVER['REQUEST_URI'] ?? '/estadia/public/index.php';
        header('Location: /estadia/public/login.php?next=' . urlencode($req));
        exit;
    }
    $userId = getUserId();
    $stmt = $pdo->prepare('SELECT r.Nombre FROM UsuarioRol ur JOIN Rol r ON ur.Rol_idRol = r.idRol WHERE ur.Usuario_idUsuario = ? LIMIT 1');
    $stmt->execute([$userId]);
    $rol = $stmt->fetchColumn();
    if ($rol !== $roleName) {
        // Redirigir a página 404 personalizada por permisos insuficientes
        header('Location: /estadia/public/404.php?reason=insufficient_privileges&page=' . urlencode($_SERVER['REQUEST_URI'] ?? 'desconocida'));
        exit;
    }
}
?>