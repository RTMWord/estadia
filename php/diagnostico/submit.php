<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

require_once __DIR__ . '/../../app/config/email.php'; // carga PHPMailer y constantes

function clean($v){
    if (is_array($v)) return array_map('clean', $v);
    return trim(strip_tags((string)$v));
}

// Recolectar campos
$perfil = clean($_POST['perfil_role'] ?? '');
$edad = clean($_POST['edad_persona'] ?? '');
$tipo_vivienda = clean($_POST['tipo_vivienda'] ?? '');
$dificultades = isset($_POST['dificultades']) && is_array($_POST['dificultades']) ? array_map('clean', $_POST['dificultades']) : [];
$intereses = isset($_POST['intereses']) && is_array($_POST['intereses']) ? array_map('clean', $_POST['intereses']) : [];
$espacios = isset($_POST['espacios']) && is_array($_POST['espacios']) ? array_map('clean', $_POST['espacios']) : [];
$tec_nivel = clean($_POST['tec_nivel'] ?? '');
$tranquilidad = clean($_POST['tranquilidad'] ?? '');
$plazo = clean($_POST['plazo'] ?? '');
$contact_nombre = clean($_POST['contact_nombre'] ?? '');
$contact_email = filter_var($_POST['contact_email'] ?? '', FILTER_SANITIZE_EMAIL);
$contact_whatsapp = clean($_POST['contact_whatsapp'] ?? '');
$contact_ciudad = clean($_POST['contact_ciudad'] ?? '');
$acepto = isset($_POST['acepto']) ? clean($_POST['acepto']) : '';

// Validaciones básicas
$errors = [];
if (empty($perfil)) $errors[] = 'Selecciona tu perfil.';
if (empty($edad)) $errors[] = 'Selecciona la edad.';
if (empty($tipo_vivienda)) $errors[] = 'Selecciona el tipo de vivienda.';
if (empty($contact_nombre)) $errors[] = 'Ingresa tu nombre.';
if (empty($contact_email) || !filter_var($contact_email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Ingresa un email válido.';
if (empty($contact_ciudad)) $errors[] = 'Ingresa la ciudad.';
if (empty($acepto)) $errors[] = 'Debes aceptar ser contactado.';

if (count($errors) > 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// Preparar entrada
$entry = [
    'id' => uniqid('diag_', true),
    'created_at' => date('c'),
    'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
    'perfil' => $perfil,
    'edad' => $edad,
    'tipo_vivienda' => $tipo_vivienda,
    'dificultades' => $dificultades,
    'intereses' => $intereses,
    'espacios' => $espacios,
    'tec_nivel' => $tec_nivel,
    'tranquilidad' => $tranquilidad,
    'plazo' => $plazo,
    'contact' => [
        'nombre' => $contact_nombre,
        'email' => $contact_email,
        'whatsapp' => $contact_whatsapp,
        'ciudad' => $contact_ciudad,
    ],
    'acepto' => $acepto
];

// Guardar en data/diagnosticos.json
$dataDir = __DIR__ . '/../../data';
if (!is_dir($dataDir)) {
    @mkdir($dataDir, 0755, true);
}
$file = $dataDir . '/diagnosticos.json';
$list = [];
if (is_file($file)) {
    $raw = @file_get_contents($file);
    $list = $raw ? json_decode($raw, true) : [];
    if (!is_array($list)) $list = [];
}
$list[] = $entry;
$okSave = @file_put_contents($file, json_encode($list, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);

// Enviar correo al administrador usando PHPMailer (configurado en app/config/email.php)
$sent_admin = false;
$sent_user = false;
$mailErrorAdmin = null;
$mailErrorUser = null;
// allow opt-in debug via GET or POST param (only returns error text when requested)
$debugMode = (isset($_GET['debug']) && $_GET['debug'] == '1') || (isset($_POST['debug']) && $_POST['debug'] == '1');
// Enviar correo al administrador
try {
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = MAIL_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_USERNAME;
    $mail->Password   = MAIL_PASSWORD;
    $mail->SMTPSecure = MAIL_SECURE;
    $mail->Port       = MAIL_PORT;
    $mail->CharSet    = 'UTF-8';
    $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
    $mail->addAddress(MAIL_USERNAME); // enviar al administrador configurado
    $mail->addReplyTo($contact_email, $contact_nombre);

    $mail->isHTML(true);
    $mail->Subject = 'Nuevo Diagnóstico MetaHogar - ' . $contact_nombre;

    $html = '<h2>Nuevo Diagnóstico MetaHogar</h2>';
    $html .= '<p><strong>ID:</strong> ' . htmlspecialchars($entry['id']) . '</p>';
    $html .= '<table border="0" cellpadding="4">';
    foreach ($entry as $k => $v) {
        if (in_array($k, ['contact', 'dificultades','intereses','espacios'])) continue;
        $html .= '<tr><td><strong>' . htmlspecialchars($k) . '</strong></td><td>' . htmlspecialchars(is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : $v) . '</td></tr>';
    }
    // Contact
    $html .= '<tr><td><strong>Contacto</strong></td><td>' . htmlspecialchars($contact_nombre) . ' / ' . htmlspecialchars($contact_email) . ' / ' . htmlspecialchars($contact_whatsapp) . ' / ' . htmlspecialchars($contact_ciudad) . '</td></tr>';
    $html .= '<tr><td><strong>Dificultades</strong></td><td>' . htmlspecialchars(implode(', ', $dificultades)) . '</td></tr>';
    $html .= '<tr><td><strong>Intereses</strong></td><td>' . htmlspecialchars(implode(', ', $intereses)) . '</td></tr>';
    $html .= '<tr><td><strong>Espacios</strong></td><td>' . htmlspecialchars(implode(', ', $espacios)) . '</td></tr>';
    $html .= '</table>';

    $mail->Body = $html;
    $mail->AltBody = strip_tags($html);

    $mail->send();
    $sent_admin = true;
} catch (\Exception $e) {
    $mailErrorAdmin = $e->getMessage();
    error_log('Diagnóstico: fallo al enviar correo al admin: ' . $mailErrorAdmin);
    $sent_admin = false;
}

// Intentar enviar correo de confirmación al usuario (si el email es válido)
if (!empty($contact_email) && filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
    try {
        $mail2 = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail2->isSMTP();
        $mail2->Host       = MAIL_HOST;
        $mail2->SMTPAuth   = true;
        $mail2->Username   = MAIL_USERNAME;
        $mail2->Password   = MAIL_PASSWORD;
        $mail2->SMTPSecure = MAIL_SECURE;
        $mail2->Port       = MAIL_PORT;
        $mail2->CharSet    = 'UTF-8';
        $mail2->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $mail2->addAddress($contact_email, $contact_nombre);

        $mail2->isHTML(true);
        $mail2->Subject = 'Recibimos tu Diagnóstico - MetaHogar';
        $bodyUser = '<p>Hola ' . htmlspecialchars($contact_nombre) . ',</p>';
        $bodyUser .= '<p>Hemos recibido tu solicitud de diagnóstico. Pronto nos pondremos en contacto contigo para compartir los resultados y recomendaciones.</p>';
        $bodyUser .= '<p>Si quieres contactarnos directamente, responde a este correo.</p>';
        $bodyUser .= '<p>Te Invitamos a que hagas tu registro en el sitio.</p>';
        $bodyUser .= '<p>Saludos,<br>Equipo MetaHogar</p>';

        $mail2->Body = $bodyUser;
        $mail2->AltBody = strip_tags($bodyUser);

        $mail2->send();
        $sent_user = true;
    } catch (\Exception $e) {
        $mailErrorUser = $e->getMessage();
        error_log('Diagnóstico: fallo al enviar correo al usuario: ' . $mailErrorUser);
        $sent_user = false;
    }
}

if ($okSave === false) {
    http_response_code(500);
    $out = [
        'success' => false,
        'message' => 'Error guardando el diagnóstico en el servidor.',
        'saved' => false,
        'email_sent_admin' => $sent_admin,
        'email_sent_user' => $sent_user
    ];
    if ($debugMode) {
        if ($mailErrorAdmin) $out['mail_error_admin'] = $mailErrorAdmin;
        if ($mailErrorUser) $out['mail_error_user'] = $mailErrorUser;
    }
    echo json_encode($out);
    exit;
}

$out = [
    'success' => true,
    'message' => 'Gracias — tu diagnóstico ha sido recibido. Te contactaremos pronto.',
    'saved' => true,
    'email_sent_admin' => $sent_admin,
    'email_sent_user' => $sent_user
];
if ($debugMode) {
    if ($mailErrorAdmin) $out['mail_error_admin'] = $mailErrorAdmin;
    if ($mailErrorUser) $out['mail_error_user'] = $mailErrorUser;
}
echo json_encode($out);

