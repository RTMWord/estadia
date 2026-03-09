<?php
/**
 * Seguridad para panel de proveedores
 */
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

if (!isLogged()) {
    header('Location: /estadia/public/login.php?next=' . urlencode($_SERVER['REQUEST_URI'] ?? '/estadia/public/proveedor/'));
    exit;
}

$userId = getUserId();
$stmt = $pdo->prepare('SELECT r.Nombre FROM UsuarioRol ur JOIN Rol r ON ur.Rol_idRol = r.idRol WHERE ur.Usuario_idUsuario = ? LIMIT 1');
$stmt->execute([$userId]);
$userRole = $stmt->fetchColumn();

if ($userRole !== 'proveedor' && $userRole !== 'administrador') {
    header('Location: /estadia/public/404.php?reason=insufficient_privileges&page=' . urlencode($_SERVER['REQUEST_URI'] ?? 'desconocida'));
    exit;
}
?>
