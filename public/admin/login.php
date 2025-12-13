<?php
session_start();

// --- Configuración rápida: ajusta según tu proyecto ---
// Intenta incluir el archivo de conexión/constantes del proyecto.
// Si no existe, descomenta y configura las constantes abajo.
if (file_exists(__DIR__ . '/../app/db.php')) {
    require_once __DIR__ . '/../app/db.php'; // ejemplo: define $mysqli ahí
} elseif (file_exists(__DIR__ . '/../app/config.php')) {
    require_once __DIR__ . '/../app/config.php'; // otro posible archivo
}

// Fallback: crea una conexión si no existe $mysqli
if (!isset($mysqli)) {
    // Ajusta estos valores a los de tu entorno (o usa constantes ya definidas por el proyecto)
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'metahogar');

    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_errno) {
        http_response_code(500);
        echo "Error de conexión a la base de datos: " . $mysqli->connect_error;
        exit;
    }
}

// --- Control de acceso: permitir solo admin (ajusta según tu sesión) ---
if (empty($_SESSION['user']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}

// --- CSRF token simple ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simple check CSRF
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $messages[] = ['type' => 'error', 'text' => 'Token inválido. Intenta nuevamente.'];
    } else {
        if (!isset($_FILES['sqlfile']) || $_FILES['sqlfile']['error'] !== UPLOAD_ERR_OK) {
            $messages[] = ['type' => 'error', 'text' => 'No se recibió el archivo o hubo un error en la subida.'];
        } else {
            $file = $_FILES['sqlfile'];

            // Validaciones básicas
            $maxBytes = 50 * 1024 * 1024; // 50 MB (ajusta)
            if ($file['size'] > $maxBytes) {
                $messages[] = ['type' => 'error', 'text' => 'Archivo demasiado grande. Máx 50MB.'];
            } else {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if ($ext !== 'sql') {
                    $messages[] = ['type' => 'error', 'text' => 'Solo se permiten archivos .sql'];
                } else {
                    // Mover a tmp y leer
                    $tmpPath = $file['tmp_name'];
                    $sql = file_get_contents($tmpPath);
                    if ($sql === false || trim($sql) === '') {
                        $messages[] = ['type' => 'error', 'text' => 'El archivo SQL está vacío o no se pudo leer.'];
                    } else {
                        // Precaución: hacer backup antes de aplicar
                        // Opcional: ejecutar dump automático aquí

                        // Ajustes para importación
                        set_time_limit(0);
                        $mysqli->autocommit(false);

                        // Desactivar checks FK temporalmente para evitar errores de orden
                        $ok = $mysqli->query("SET FOREIGN_KEY_CHECKS=0");
                        if ($ok === false) {
                            $messages[] = ['type' => 'error', 'text' => 'No se pudieron desactivar FOREIGN_KEY_CHECKS: ' . $mysqli->error];
                        } else {
                            // Ejecutar multi query
                            if ($mysqli->multi_query($sql)) {
                                // iterar hasta terminar todas las consultas
                                do {
                                    if ($res = $mysqli->store_result()) {
                                        $res->free();
                                    }
                                } while ($mysqli->more_results() && $mysqli->next_result());

                                if ($mysqli->errno) {
                                    $mysqli->rollback();
                                    $messages[] = ['type' => 'error', 'text' => 'Error durante la importación: ' . $mysqli->error];
                                } else {
                                    // Reactivar FK checks
                                    $mysqli->query("SET FOREIGN_KEY_CHECKS=1");
                                    $mysqli->commit();
                                    $messages[] = ['type' => 'success', 'text' => 'Restauración completada correctamente.'];
                                }
                            } else {
                                $mysqli->rollback();
                                $messages[] = ['type' => 'error', 'text' => 'Error ejecutando SQL: ' . $mysqli->error];
                            }
                        }
                        // restaurar autocommit a true
                        $mysqli->autocommit(true);
                    }
                }
            }
        }
    }
}

// --- Render simple HTML (ajusta markup para integrarlo con tus templates) ---
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Restaurar Base de Datos</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body{font-family: Arial, sans-serif; padding:20px}
        .msg-success{color:green}
        .msg-error{color:red}
        form{margin-top:20px}
    </style>
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
    <h1>Restaurar Base de Datos</h1>
    <p>Sube un archivo .sql para restaurar la base de datos. Esta acción reemplazará datos —asegúrate de tener un backup.</p>

    <?php foreach ($messages as $m): ?>
        <div class="<?= $m['type'] === 'success' ? 'msg-success' : 'msg-error' ?>">
            <?= htmlspecialchars($m['text']) ?>
        </div>
    <?php endforeach; ?>

    <form method="post" enctype="multipart/form-data" onsubmit="return confirm('¿Estás seguro de que quieres restaurar la base de datos? Esto sobrescribirá datos.');">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <label>Archivo .sql (máx 50MB):</label><br>
        <input type="file" name="sqlfile" accept=".sql" required><br><br>
        <button type="submit">Restaurar</button>
    </form>

    <p><a href="index.php">Volver al panel</a></p>
</body>
</html>