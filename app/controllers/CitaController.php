<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Cita.php';
require_once __DIR__ . '/../helpers/auth.php';

// Crear sesión/obtener usuario logueado
// requireRole/requireLogin no se invoca aquí porque la creación la harán usuarios logueados desde el frontend
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

function jsonResponse($ok, $message, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => $ok, 'message' => $message]);
    exit;
}

if (isset($_POST['crear'])) {
    // asignar usuario desde sesión (si existe)
    $userId = getUserId();
    if (!$userId) {
        // No hay usuario en sesión
        if ($isAjax) {
            jsonResponse(false, 'Tu sesion expiro. Inicia sesion nuevamente.', 401);
        }
        header('Location: ../../public/login.php');
        exit;
    }
    try {
        // Forzar datos esperados por el modelo: usuario, estado por defecto
        $_POST['usuario'] = $userId;
        $_POST['estado'] = 'AGENDADA';
        // Normalizar fecha tipo datetime-local (YYYY-MM-DDTHH:MM) a 'YYYY-MM-DD HH:MM:SS'
        if (!empty($_POST['fechahora'])) {
            $fh = str_replace('T', ' ', $_POST['fechahora']);
            // si viene sin segundos, agregar :00
            if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $fh)) {
                $fh .= ':00';
            }
            $_POST['fechahora'] = $fh;
        }
        Cita::crear($pdo, $_POST);
        // Si la petición es AJAX, devolver JSON y no redirigir
        if ($isAjax) {
            jsonResponse(true, 'Cita registrada correctamente. Sera procesada y te notificaremos por correo.');
        }
    } catch (Throwable $e) {
        if ($isAjax) {
            jsonResponse(false, 'No se pudo registrar la cita. Intenta nuevamente.', 500);
        }
        header('Location: ../../public/cita_nueva.php?error=1');
        exit;
    }

    // Redirección según tipo de usuario y parámetro redir
    if (isset($_GET['redir']) && $_GET['redir'] == '1') {
        // Comprobar rol
        $userId = getUserId();
        $rol = null;
        if ($userId) {
            $stmt = $pdo->prepare('SELECT r.Nombre FROM UsuarioRol ur JOIN Rol r ON ur.Rol_idRol = r.idRol WHERE ur.Usuario_idUsuario = ? LIMIT 1');
            $stmt->execute([$userId]);
            $rol = $stmt->fetchColumn();
        }
        if ($rol === 'administrador' || $rol === 'admin') {
            header('Location: ../../public/admin/citas.php');
            exit;
        } else {
            header('Location: ../../public/index.php');
            exit;
        }
    } else {
        header('Location: ../../public/index.php');
        exit;
    }
}
if (isset($_POST['editar'])) {
    Cita::editar($pdo, $_POST);
    // Si viene indicador de redirección (desde panel admin), redirigir al panel admin
    $redir = (isset($_POST['redir']) && $_POST['redir'] == '1') || (isset($_GET['redir']) && $_GET['redir'] == '1');
    if ($redir) {
        // comprobar rol
        $userId = getUserId();
        $rol = null;
        if ($userId) {
            $stmt = $pdo->prepare('SELECT r.Nombre FROM UsuarioRol ur JOIN Rol r ON ur.Rol_idRol = r.idRol WHERE ur.Usuario_idUsuario = ? LIMIT 1');
            $stmt->execute([$userId]);
            $rol = $stmt->fetchColumn();
        }
        if ($rol === 'administrador' || $rol === 'admin') {
            header('Location: ../../public/admin/citas.php');
            exit;
        }
    }
    header('Location: ../../public/index.php');
    exit;
}
if (isset($_GET['eliminar'])) {
    Cita::eliminar($pdo, $_GET['eliminar']);
    header('Location: ../../public/index.php');
    exit;
}
?>
