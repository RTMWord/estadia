<?php
require_once '../config/db.php';
require_once '../models/Sugerencia.php';

if (isset($_POST['enviar'])) {
    // intentar asociar usuario por email si se proporciona
    $email = trim($_POST['email'] ?? '');
    $usuarioId = null;
    if ($email !== '') {
        $stmt = $pdo->prepare('SELECT idUsuario FROM Usuario WHERE Email = ? LIMIT 1');
        $stmt->execute([$email]);
        $usuarioId = $stmt->fetchColumn();
    }
    Sugerencia::crear($pdo, [
        'usuario_id' => $usuarioId,
        'titulo' => $_POST['titulo'],
        'descripcion' => $_POST['descripcion']
    ]);
    header('Location: ../../public/sugerencias.php?sent=1');
    exit;
}

if (isset($_GET['eliminar'])) {
    Sugerencia::eliminar($pdo, $_GET['eliminar']);
    header('Location: ../../public/admin/sugerencias.php');
    exit;
}

if (isset($_POST['cambiar_estado'])) {
    Sugerencia::updateEstado($pdo, $_POST['id'], $_POST['estado']);
    header('Location: ../../public/admin/sugerencias.php');
    exit;
}
?>
