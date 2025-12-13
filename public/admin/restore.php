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

// --- Render HTML ---
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Restaurar Base de Datos - MetaHogar Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="../assets/css/restore.css">
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
    <?php include __DIR__ . '/partials/admin_nav.php'; ?>

    <div class="container main-content">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="restore-card">
                    <div class="card-header-custom">
                        <h1><i class="fas fa-database"></i> Restaurar Base de Datos</h1>
                    </div>
                    <div class="card-body p-4">
                        <div class="warning-box">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                            <strong>Advertencia:</strong> Esta acción reemplazará los datos actuales de la base de datos. Asegúrate de tener un respaldo reciente antes de continuar.
                        </div>

                        <div class="info-box">
                            <i class="fas fa-info-circle text-info"></i>
                            <strong>Instrucciones:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Selecciona un archivo SQL válido (máximo 50MB)</li>
                                <li>El archivo debe contener la estructura y datos de la base de datos</li>
                                <li>Puedes usar respaldos generados desde el <a href="backup.php">módulo de backup</a></li>
                            </ul>
                        </div>

                        <form method="post" enctype="multipart/form-data" id="restoreForm">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                            
                            <div class="upload-area mb-4" onclick="document.getElementById('sqlfile').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <h5>Haz clic para seleccionar archivo SQL</h5>
                                <p class="text-muted mb-0">o arrastra y suelta aquí</p>
                                <p class="text-muted small">Tamaño máximo: 50MB</p>
                                <input type="file" name="sqlfile" id="sqlfile" accept=".sql" required style="display:none">
                                <p id="fileName" class="mt-3 fw-bold text-primary"></p>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-restore btn-primary btn-lg">
                                    <i class="fas fa-sync-alt me-2"></i>Restaurar Base de Datos
                                </button>
                                <a href="index.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Volver al Panel
                                </a>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt"></i> Conexión segura | 
                                <i class="fas fa-lock"></i> Protección CSRF activada
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="admin-footer">
        <div class="container">
            <p class="mb-0">&copy; <?= date('Y') ?> MetaHogar. Todos los derechos reservados.</p>
            <small>Panel de Administración - Sistema de Restauración</small>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- JavaScript Personalizado -->
    <script src="../assets/css/js/restore.js"></script>
    
    <?php if (!empty($messages)): ?>
    <script>
        // Mostrar mensajes con SweetAlert2
        <?php foreach ($messages as $m): ?>
            mostrarAlerta(
                '<?= $m['type'] === 'success' ? 'success' : 'error' ?>',
                '<?= $m['type'] === 'success' ? '¡Éxito!' : 'Error' ?>',
                '<?= addslashes($m['text']) ?>',
                <?= $m['type'] === 'success' ? '3000' : 'null' ?>
            );
        <?php endforeach; ?>
    </script>
    <?php endif; ?>
</body>
</html>