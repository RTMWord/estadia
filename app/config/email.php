<?php
// app/config/email.php
// IMPORTANTE: AJUSTA ESTOS VALORES SMTP A TU CONFIGURACIÓN REAL
const MAIL_HOST = 'smtp.gmail.com';   // Servidor SMTP (ej. smtp.gmail.com)
const MAIL_USERNAME = 'solurssen@gmail.com'; // Tu correo (ej. tu_usuario@gmail.com)
const MAIL_PASSWORD = 'fxvz fmqy uqpc sglu'; // Contraseña de aplicación o del correo
const MAIL_PORT = 587; 
const MAIL_FROM_EMAIL = 'noreply@tudominio.com';
const MAIL_FROM_NAME = 'MetaHogar - Recuperación';
const MAIL_SECURE = 'tls'; // 'ssl' o 'tls'

// Definir la ruta raíz para incluir autoload.php de Composer
define('ROOT_PATH', dirname(__DIR__, 2)); 
require_once ROOT_PATH . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

function sendPasswordResetEmail($recipientEmail, $recipientName, $token) {
    $mail = new PHPMailer(true);
    try {
        // Configuración del servidor
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_SECURE;
        $mail->Port       = MAIL_PORT;
        $mail->CharSet    = 'UTF-8';
        $mail->SMTPDebug  = SMTP::DEBUG_OFF; // Cambia a SMTP::DEBUG_SERVER para depurar
        
        // Destinatarios y Remitente
        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $mail->addAddress($recipientEmail, $recipientName);

        // Contenido del correo (el enlace de reseteo)
        $mail->isHTML(true);
        $mail->Subject = 'Restablecer Contraseña - MetaHogar';
        
        // **Ajusta esta URL base a tu dominio/subdirectorio**
        $resetLink = 'http://localhost/Estadia/public/reset_password.php?token=' . urlencode($token);
        
        $body = "
            <html>
            <body>
                <h2>Hola, " . htmlspecialchars($recipientName) . ":</h2>
                <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta en MetaHogar.</p>
                <p>Para establecer una nueva contraseña, haz clic en el siguiente enlace. Este enlace caducará en 1 hora.</p>
                <p><a href=\"$resetLink\" style=\"padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; display: inline-block;\">Restablecer Contraseña</a></p>
                <p>Si no solicitaste este cambio, ignora este correo.</p>
                <p>Saludos cordiales,<br>El equipo de MetaHogar</p>
            </body>
            </html>
        ";
        
        $mail->Body = $body;
        $mail->AltBody = "Para restablecer tu contraseña, visita: $resetLink";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("El correo no pudo ser enviado. Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>