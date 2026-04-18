<?php
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/config/email.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function jsonResponse(int $statusCode, bool $success, string $message): void {
    http_response_code($statusCode);
    echo json_encode([
        'success' => $success,
        'message' => $message
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(405, false, 'Método no permitido.');
}

$rawBody = file_get_contents('php://input');
$data = json_decode($rawBody, true);

if (!is_array($data)) {
    jsonResponse(400, false, 'Solicitud inválida.');
}

$nombreCompleto = trim((string)($data['nombre_completo'] ?? ''));
$correoElectronico = trim((string)($data['correo_electronico'] ?? ''));
$idProducto = (int)($data['id_producto'] ?? 0);

if ($nombreCompleto === '' || mb_strlen($nombreCompleto) < 5) {
    jsonResponse(422, false, 'Ingresa un nombre completo válido.');
}

if (!filter_var($correoElectronico, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(422, false, 'Ingresa un correo electrónico válido.');
}

if ($idProducto <= 0) {
    jsonResponse(422, false, 'Selecciona un producto válido.');
}

try {
    $stmt = $pdo->prepare('SELECT idProducto, Nombre FROM producto WHERE idProducto = ? LIMIT 1');
    $stmt->execute([$idProducto]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        jsonResponse(404, false, 'El producto seleccionado no existe.');
    }

    $nombreProducto = (string)$producto['Nombre'];

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = MAIL_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = MAIL_USERNAME;
    $mail->Password = MAIL_PASSWORD;
    $mail->SMTPSecure = MAIL_SECURE;
    $mail->Port = MAIL_PORT;
    $mail->CharSet = 'UTF-8';

    $mail->setFrom(MAIL_FROM_EMAIL, 'MetaHogar - Información de productos');
    $mail->addAddress($correoElectronico, $nombreCompleto);
    $mail->addReplyTo('contacto@metahogar.com', 'MetaHogar Contacto');

    // Copia interna opcional para seguimiento de solicitudes.
    $mail->addCC(MAIL_USERNAME);

    $mail->isHTML(true);
    $mail->Subject = 'Información de producto solicitado - MetaHogar';

    $body = "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #2d3748; line-height: 1.6; }
            .container { max-width: 620px; margin: 0 auto; padding: 18px; background: #f7fafc; }
            .card { background: #ffffff; border-radius: 10px; padding: 22px; border: 1px solid #e2e8f0; }
            .title { color: #17466e; margin-top: 0; }
            .pill { display: inline-block; background: #e6fffa; color: #065f46; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; }
            .footer { color: #718096; font-size: 12px; margin-top: 18px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='card'>
                <h2 class='title'>Hola, " . htmlspecialchars($nombreCompleto, ENT_QUOTES, 'UTF-8') . "</h2>
                <p>Gracias por tu interés en el producto:</p>
                <p><span class='pill'>" . htmlspecialchars($nombreProducto, ENT_QUOTES, 'UTF-8') . "</span></p>
                <p>Por el momento, este producto no puede ser vendido directamente desde nuestro sitio web.</p>
                <p>Te ofrecemos una disculpa por cualquier inconveniente. Con gusto podemos brindarte más detalles y orientación por correo.</p>
                <p>Puedes ponerte en contacto con nosotros respondiendo a este mensaje o escribiendo a <strong>contacto@metahogar.com</strong>.</p>
                <p>Atentamente,<br><strong>Equipo MetaHogar</strong></p>
                <p class='footer'>Este correo fue generado automáticamente para atender tu solicitud de información.</p>
            </div>
        </div>
    </body>
    </html>
    ";

    $mail->Body = $body;
    $mail->AltBody = "Hola, {$nombreCompleto}.\n\n" .
        "Gracias por tu interés en el producto: {$nombreProducto}.\n" .
        "Por el momento, este producto no puede ser vendido directamente desde nuestro sitio web.\n" .
        "Te ofrecemos una disculpa por el inconveniente.\n" .
        "Para más información, contáctanos en contacto@metahogar.com o responde a este correo.\n\n" .
        "Equipo MetaHogar";

    $mail->send();

    jsonResponse(200, true, 'Te enviamos la información al correo registrado.');
} catch (Exception $e) {
    error_log('Error al enviar solicitud de producto: ' . $e->getMessage());
    jsonResponse(500, false, 'No fue posible enviar el correo en este momento. Inténtalo más tarde.');
}
