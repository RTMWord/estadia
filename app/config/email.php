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

function sendWelcomeEmail($recipientEmail, $recipientName) {
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
        $mail->SMTPDebug  = SMTP::DEBUG_OFF;
        
        // Destinatarios y Remitente
        $mail->setFrom(MAIL_FROM_EMAIL, 'MetaHogar - Bienvenida');
        $mail->addAddress($recipientEmail, $recipientName);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = '¡Bienvenido a MetaHogar!';
        
        $body = "
            <html>
            <head>
                <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f5f7fb; }
                    .header { background: linear-gradient(180deg, #17466e 0%, #4b96c3 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { background: white; padding: 30px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                    .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
                    h1 { margin: 0; font-size: 28px; }
                    h2 { color: #17466e; }
                    .highlight { background: #e3f2fd; padding: 15px; border-left: 4px solid #2196f3; margin: 20px 0; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>🏠 ¡Bienvenido a MetaHogar!</h1>
                        <p>Tu cuenta ha sido creada exitosamente</p>
                    </div>
                    <div class='content'>
                        <h2>Hola, " . htmlspecialchars($recipientName) . ":</h2>
                        <p>¡Gracias por registrarte en <strong>MetaHogar</strong>! Nos complace darte la bienvenida a nuestra comunidad.</p>
                        
                        <div class='highlight'>
                            <p><strong>Tu cuenta ya está activa</strong> y puedes comenzar a disfrutar de todos nuestros servicios:</p>
                            <ul>
                                <li>✨ Acceso a productos y servicios de domótica</li>
                                <li>🏠 Diagnóstico remoto personalizado</li>
                                <li>💬 Participación en nuestra comunidad</li>
                                <li>📞 Soporte técnico especializado</li>
                                <li>📰 Boletín informativo con novedades</li>
                            </ul>
                        </div>
                        
                        <p>Puedes <strong>iniciar sesión</strong> en cualquier momento con el correo y contraseña que registraste.</p>
                        
                        <p>Si tienes alguna pregunta o necesitas ayuda, no dudes en contactarnos. ¡Estamos aquí para ayudarte!</p>
                        
                        <p style='margin-top: 30px;'><strong>¡Gracias por confiar en MetaHogar!</strong></p>
                        
                        <p>Atentamente,<br>
                        <strong>El equipo de MetaHogar</strong><br>
                        <em>Diseñando hogares seguros e inteligentes</em></p>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date('Y') . " MetaHogar. Todos los derechos reservados.</p>
                        <p>Av. Par Vial 10, Atlacomulco, 62560 Jiutepec, Mor.</p>
                        <p>Email: contacto@metahogar.com | Tel: +52 1 777 129 4253</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        $mail->Body = $body;
        $mail->AltBody = "¡Bienvenido a MetaHogar, " . $recipientName . "!\n\n" .
                         "Gracias por registrarte. Tu cuenta ha sido creada exitosamente.\n\n" .
                         "Ya puedes iniciar sesión y disfrutar de todos nuestros servicios.\n\n" .
                         "Atentamente,\nEl equipo de MetaHogar";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("El correo de bienvenida no pudo ser enviado. Error: {$mail->ErrorInfo}");
        return false;
    }
}

function sendVerificationEmail($recipientEmail, $recipientName, $token) {
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
        $mail->SMTPDebug  = SMTP::DEBUG_OFF;
        
        // Destinatarios y Remitente
        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $mail->addAddress($recipientEmail, $recipientName);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Verificar tu cuenta - MetaHogar';
        
        // **Ajusta esta URL base a tu dominio/subdirectorio**
        $verifyLink = 'http://localhost/Estadia/public/verify_email.php?token=' . urlencode($token);
        
        $body = "
            <html>
            <body style='font-family: Segoe UI, Tahoma, Geneva, Verdana, sans-serif;'>
                <h2>Hola, " . htmlspecialchars($recipientName) . ":</h2>
                <p>¡Bienvenido a MetaHogar! Gracias por registrarte.</p>
                <p>Para completar tu registro y activar tu cuenta, por favor haz clic en el siguiente enlace de verificación:</p>
                <p><a href=\"$verifyLink\" style=\"padding: 12px 30px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;\">Verificar mi cuenta</a></p>
                <p>O copia y pega esta URL en tu navegador:</p>
                <p style=\"word-break: break-all; color: #666;\">$verifyLink</p>
                <p style=\"color: #999; font-size: 12px;\">Este enlace caduca en 24 horas.</p>
                <p>Si no creaste esta cuenta, ignora este correo.</p>
                <p>Saludos cordiales,<br><strong>El equipo de MetaHogar</strong></p>
            </body>
            </html>
        ";
        
        $mail->Body = $body;
        $mail->AltBody = "Para verificar tu cuenta, visita: $verifyLink\n\nEste enlace caduca en 24 horas.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("El correo de verificación no pudo ser enviado. Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>