<?php
// public/php/newsletter_subscribe.php
header('Content-Type: application/json');

// Permitir CORS si es necesario
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../app/config/email.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Por favor, ingresa un correo electrónico válido.';
        echo json_encode($response);
        exit;
    }

    // Enviar correo de confirmación
    $mail = new PHPMailer(true);
    
    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_SECURE;
        $mail->Port       = MAIL_PORT;
        $mail->CharSet    = 'UTF-8';
        
        // Remitente y destinatario
        $mail->setFrom(MAIL_FROM_EMAIL, 'MetaHogar - Boletín Informativo');
        $mail->addAddress($email);
        
        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = '¡Bienvenido al Boletín Informativo de MetaHogar!';
        
        $body = "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f5f7fb; }
                .header { background: linear-gradient(180deg, #17466e 0%, #4b96c3 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: white; padding: 30px; border-radius: 0 0 10px 10px; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
                .badge { background: #ffc107; color: #000; padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: bold; }
                h1 { margin: 0; font-size: 28px; }
                h2 { color: #17466e; }
                .highlight { background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎉 ¡Bienvenido!</h1>
                    <p>Gracias por suscribirte a nuestro boletín informativo</p>
                </div>
                <div class='content'>
                    <h2>Hola,</h2>
                    <p>Nos complace informarte que tu suscripción al <strong>Boletín Informativo de MetaHogar</strong> ha sido exitosa.</p>
                    
                    <div class='highlight'>
                        <p><span class='badge'>EN DESARROLLO</span></p>
                        <p><strong>Nota importante:</strong> Nuestro sistema de boletín informativo se encuentra actualmente en <strong>fase de desarrollo</strong>. Estamos trabajando arduamente para ofrecerte contenido de calidad sobre:</p>
                        <ul>
                            <li>✨ Novedades en tecnología para el hogar inteligente</li>
                            <li>🏠 Consejos para una longevidad digna y confortable</li>
                            <li>📢 Promociones y ofertas especiales</li>
                            <li>🔧 Actualizaciones de productos y servicios</li>
                        </ul>
                    </div>
                    
                    <p>Pronto comenzarás a recibir nuestras actualizaciones periódicas con información relevante, consejos útiles y las últimas novedades de MetaHogar.</p>
                    
                    <h3>¿Qué puedes esperar?</h3>
                    <ul>
                        <li>Artículos exclusivos sobre hogares inteligentes</li>
                        <li>Guías de uso de nuestros productos</li>
                        <li>Promociones especiales para suscriptores</li>
                        <li>Noticias del sector de domótica y tecnología</li>
                    </ul>
                    
                    <p>Mientras tanto, te invitamos a explorar nuestro sitio web y conocer más sobre nuestros servicios y productos.</p>
                    
                    <p style='margin-top: 30px;'>Si tienes alguna pregunta, no dudes en contactarnos.</p>
                    
                    <p><strong>¡Gracias por confiar en MetaHogar!</strong></p>
                    
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
        $mail->AltBody = "¡Bienvenido al Boletín Informativo de MetaHogar!\n\n" .
                         "Gracias por suscribirte. Nuestro boletín se encuentra en fase de desarrollo.\n\n" .
                         "Pronto recibirás actualizaciones sobre novedades, consejos y promociones especiales.\n\n" .
                         "Atentamente,\nEl equipo de MetaHogar";
        
        $mail->send();
        
        $response['success'] = true;
        $response['message'] = '¡Gracias! Te has suscrito exitosamente. Revisa tu correo.';
        
    } catch (Exception $e) {
        $response['message'] = 'Error al enviar el correo. Por favor, intenta más tarde.';
        error_log("Error al enviar boletín: " . $mail->ErrorInfo);
    }
} else {
    $response['message'] = 'Método no permitido.';
}

echo json_encode($response);
