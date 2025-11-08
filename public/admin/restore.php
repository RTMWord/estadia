<?php
// Reutilizar la configuración y helpers del proyecto
require_once __DIR__ . '/../../app/config/db.php'; // define $pdo, y variables $host,$db,$user,$pass
require_once __DIR__ . '/../../app/helpers/auth.php'; // inicia sesión y aporta requireRole()

// Asegurar que sólo administradores puedan acceder (usa la misma lógica que el resto)
requireRole($pdo, 'administrador');

// Si el resto del script necesita mysqli para importaciones multi_query, crear una conexión
// usando las mismas credenciales definidas en app/config/db.php (variables $host,$user,$pass,$db)
if (!isset($mysqli)) {
    $mysqli = new mysqli($host, $user, $pass, $db);
    if ($mysqli->connect_errno) {
        http_response_code(500);
        echo "Error de conexión a la base de datos: " . $mysqli->connect_error;
        exit;
    }
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
                            // Ejecutar el SQL de forma segura, respetando directivas DELIMITER
                            $okExec = true;
                            $execError = '';
                            // desactivar mysqli exceptions para manejar errores manualmente
                            mysqli_report(MYSQLI_REPORT_OFF);
                            $handle = @fopen($tmpPath, 'r');
                            if ($handle === false) {
                                $okExec = false;
                                $execError = 'No se pudo abrir el archivo temporal.';
                            } else {
                                $delimiter = ';';
                                $buffer = '';
                                while (($line = fgets($handle)) !== false) {
                                    $trimmed = rtrim($line, "\r\n");
                                    // ignorar comentarios de una sola línea
                                    if (preg_match('/^\s*(?:-- |#)/', $trimmed) || $trimmed === '') {
                                        continue;
                                    }
                                    // detectar cambio de DELIMITER (cliente)
                                    if (preg_match('/^\s*DELIMITER\s+(\S+)\s*$/i', $trimmed, $m)) {
                                        $delimiter = $m[1];
                                        continue;
                                    }
                                    $buffer .= $line;
                                    // Procesar sentencias completas en el buffer (buscando el delimitador)
                                    while (true) {
                                        $pos = strpos($buffer, $delimiter);
                                        if ($pos === false) break;

                                        // extraer la sentencia hasta el delimitador
                                        $stmt = substr($buffer, 0, $pos);
                                        // recortar el buffer dejando el resto después del delimitador
                                        $buffer = substr($buffer, $pos + strlen($delimiter));

                                        $stmt = trim($stmt);
                                        // eliminar declaraciones DELIMITER residuales y ; iniciales
                                        $stmt = preg_replace('/^\s*DELIMITER\s+\S+\s*/mi', '', $stmt);
                                        $stmt = preg_replace('/^[\s;]+/','', $stmt);

                                        if ($stmt === '') {
                                            continue;
                                        }

                                        if (!$mysqli->query($stmt)) {
                                            $okExec = false;
                                            $execError = $mysqli->error . ' | SQL: ' . substr($stmt, 0, 500);
                                            // salir de los bucles externos
                                            break 2;
                                        }
                                    }
                                }
                                fclose($handle);
                                // ejecutar cualquier resto pendiente
                                if ($okExec && trim($buffer) !== '') {
                                    $finalStmt = trim($buffer);
                                    $finalStmt = preg_replace('/^\s*DELIMITER\s+\S+\s*/mi', '', $finalStmt);
                                    $finalStmt = preg_replace('/^[\s;]+/','', $finalStmt);
                                    if ($finalStmt !== '') {
                                        if (!$mysqli->query($finalStmt)) {
                                            $okExec = false;
                                            $execError = $mysqli->error . ' | SQL: ' . substr($finalStmt, 0, 500);
                                        }
                                    }
                                }
                            }

                            if (!$okExec) {
                                $mysqli->rollback();
                                $messages[] = ['type' => 'error', 'text' => 'Error durante la importación: ' . $execError];
                            } else {
                                // Reactivar FK checks
                                $mysqli->query("SET FOREIGN_KEY_CHECKS=1");
                                $mysqli->commit();
                                $messages[] = ['type' => 'success', 'text' => 'Restauración completada correctamente.'];
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