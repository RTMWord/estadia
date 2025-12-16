<?php
$requirePathDb = '../../app/config/db.php';
require_once $requirePathDb;
require_once '../../app/models/Sugerencia.php';
require_once '../../app/helpers/auth.php';
require_once '../../app/config/email.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
requireRole($pdo, 'administrador');
$id = $_GET['id'] ?? null;
if (!$id) { header('Location: sugerencias.php'); exit; }
$s = Sugerencia::getById($pdo, $id);
if (!$s) { header('Location: sugerencias.php'); exit; }

if (isset($_POST['responder'])) {
    $mensaje = $_POST['mensaje'];
    // Guardar notificación para el usuario si existe
    if ($s['Usuario_idUsuario']) {
        $stmt = $pdo->prepare('INSERT INTO Notificacion (Usuario_idUsuario, Titulo, Mensaje) VALUES (?, ?, ?)');
        $stmt->execute([$s['Usuario_idUsuario'], 'Respuesta a tu sugerencia: '.substr($s['Titulo'],0,50), $mensaje]);
    }
    // Cambiar estado
    $estado = $_POST['estado'];
    $stmt2 = $pdo->prepare('UPDATE Sugerencia SET Estado = ? WHERE idSugerencia = ?');
    $stmt2->execute([$estado, $id]);

    // Enviar correo de respuesta si el usuario tiene email
    if (!empty($s['UsuarioEmail'])) {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USERNAME;
            $mail->Password   = MAIL_PASSWORD;
            $mail->SMTPSecure = MAIL_SECURE;
            $mail->Port       = MAIL_PORT;
            $mail->CharSet    = 'UTF-8';
            $mail->SMTPDebug  = SMTP::DEBUG_OFF;

            $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
            $mail->addAddress($s['UsuarioEmail'], $s['UsuarioNombre'] ?? '');

            $mail->isHTML(true);
            $mail->Subject = 'Respuesta a tu sugerencia';
            $body  = '<h3>Hola '.htmlspecialchars($s['UsuarioNombre'] ?? 'Usuario').',</h3>';
            $body .= '<p>Hemos revisado tu sugerencia:</p>';
            $body .= '<p><strong>Título:</strong> '.htmlspecialchars($s['Titulo']).'</p>';
            $body .= '<p><strong>Descripción:</strong><br>'.nl2br(htmlspecialchars($s['Descripcion'])).'</p>';
            $body .= '<hr>';
            $body .= '<p><strong>Respuesta del administrador:</strong><br>'.nl2br(htmlspecialchars($mensaje)).'</p>';
            $body .= '<p><strong>Estado actualizado:</strong> '.htmlspecialchars($estado).'</p>';
            $body .= '<p>Gracias por ayudarnos a mejorar.</p>';
            $mail->Body    = $body;
            $mail->AltBody = "Hemos respondido a tu sugerencia. Estado: $estado";

            $mail->send();
        } catch (Exception $e) {
            error_log('No se pudo enviar el correo de sugerencia: ' . $e->getMessage());
        }
    }

    header('Location: sugerencias.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Responder Sugerencia - MetaHogar</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.userway.org/widget.js" data-account="kjnkkEfZy1"></script>
    <style>
        .userway-icon {
            position: fixed !important;
            right: 20px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            z-index: 9999 !important;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <h2 class="text-primary mb-4">Responder Sugerencia</h2>
        <div class="card mb-3">
            <div class="card-body">
                <h5><?= htmlspecialchars($s['Titulo']) ?></h5>
                <p><?= nl2br(htmlspecialchars($s['Descripcion'])) ?></p>
                <p><strong>Usuario:</strong> <?= htmlspecialchars($s['UsuarioNombre'] ?? 'Anónimo') ?> (<?= htmlspecialchars($s['UsuarioEmail'] ?? '') ?>)</p>
            </div>
        </div>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Mensaje de respuesta</label>
                <textarea name="mensaje" class="form-control" rows="5" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="PENDIENTE" <?= $s['Estado']=='PENDIENTE' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="EN_PROCESO" <?= $s['Estado']=='EN_PROCESO' ? 'selected' : '' ?>>En proceso</option>
                    <option value="ATENDIDA" <?= $s['Estado']=='ATENDIDA' ? 'selected' : '' ?>>Atendida</option>
                    <option value="CERRADA" <?= $s['Estado']=='CERRADA' ? 'selected' : '' ?>>Cerrada</option>
                </select>
            </div>
            <button type="submit" name="responder" class="btn btn-primary">Enviar Respuesta</button>
            <a href="sugerencias.php" class="btn btn-secondary">Volver</a>
        </form>
    </div>
</body>
</html>
