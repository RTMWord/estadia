<?php
// public/admin/backup.php
require_once '../../app/config/db.php';
require_once '../../app/helpers/auth.php';
requireRole($pdo, 'administrador');

// Nombre del archivo de respaldo
$db_name = $db ?? 'database';
$filename = $db_name . '_' . date('Y-m-d_His') . '.sql';

// Intentar usar mysqldump si está disponible; si no, fallback a PHP puro
set_time_limit(0);

$candidates = [
    'C:\\xampp\\mysql\\bin\\mysqldump.exe',
    'C:\\xampp\\mysql\\bin\\mysqldump',
    'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
    'mysqldump'
];

$found = null;
foreach ($candidates as $c) {
    if ($c === 'mysqldump') {
        // pruebo si está en PATH
        @exec('mysqldump --version 2>&1', $tmpOut, $tmpCode);
        if (isset($tmpCode) && $tmpCode === 0) { $found = 'mysqldump'; break; }
    } else {
        if (file_exists($c)) { $found = $c; break; }
    }
}

// ruta temporal para el archivo (Windows o Unix)
$tmpDir = sys_get_temp_dir();
$dump_path = $tmpDir . DIRECTORY_SEPARATOR . $filename;

// Ruta para guardar backups permanentes en el servidor
$backupDir = __DIR__ . DIRECTORY_SEPARATOR . 'backups';
$backupEnabled = true;
if (!is_dir($backupDir)) {
    // intentar crear la carpeta; si falla, seguimos sin guardar en servidor
    if (!@mkdir($backupDir, 0755, true)) {
        $backupEnabled = false;
    }
}
if ($backupEnabled && !is_writable($backupDir)) {
    $backupEnabled = false;
}

if ($found) {
    // Construir comando (si la contraseña está vacía, no incluir -p)
    $passPart = ($pass !== '') ? ('-p' . $pass) : '';
    // escapeshellarg en Windows rodea con comillas; mysqldump acepta -pPASSWORD sin espacio
    $cmd = sprintf(
        '%s --opt -h %s -u %s %s %s > %s',
        escapeshellarg($found),
        escapeshellarg($host),
        escapeshellarg($user),
        $passPart,
        escapeshellarg($db),
        escapeshellarg($dump_path)
    );

    $output = [];
    $return_var = 1;
    @exec($cmd . ' 2>&1', $output, $return_var);

    if ($return_var === 0 && file_exists($dump_path) && filesize($dump_path) > 0) {
        // Copiar a carpeta de backups en servidor si está habilitada
        if ($backupEnabled) {
            $backupFile = $backupDir . DIRECTORY_SEPARATOR . $filename;
            @copy($dump_path, $backupFile);
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/sql; charset=utf-8');
        header('Content-Transfer-Encoding: binary');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($dump_path));
        readfile($dump_path);
        @unlink($dump_path);
        exit;
    }
    // si mysqldump falló, seguimos al fallback PHP
}

// FALLBACK: Generar SQL con PHP (sin depender de mysqldump)
// En lugar de imprimir directamente, escribimos a un archivo temporal y luego lo enviamos y copiamos a backups/
$fp = @fopen($dump_path, 'w');
if ($fp === false) {
    http_response_code(500);
    echo "No se pudo crear archivo temporal para el respaldo. Compruebe permisos de " . htmlspecialchars($tmpDir);
    exit;
}

fwrite($fp, "-- Backup generado por PHP en " . date('c') . "\n");
fwrite($fp, "SET FOREIGN_KEY_CHECKS=0;\n\n");

// Obtener tablas (incluye views)
$tablesStmt = $pdo->query("SHOW FULL TABLES WHERE Table_type='BASE TABLE' OR Table_type='VIEW'");
$tables = $tablesStmt ? $tablesStmt->fetchAll(PDO::FETCH_NUM) : [];

foreach ($tables as $trow) {
    $table = $trow[0];

    // Intentar obtener CREATE TABLE
    $createRow = $pdo->query("SHOW CREATE TABLE `" . str_replace('`','``',$table) . "`")->fetch(PDO::FETCH_ASSOC);
    if ($createRow && isset($createRow['Create Table'])) {
        fwrite($fp, "\n-- --------------------------------------------------------\n");
        fwrite($fp, "-- Estructura de la tabla `$table`\n");
        fwrite($fp, "DROP TABLE IF EXISTS `$table`;\n");
        fwrite($fp, $createRow['Create Table'] . ";\n\n");
    } else {
        // intentar como VIEW
        $createView = $pdo->query("SHOW CREATE VIEW `" . str_replace('`','``',$table) . "`")->fetch(PDO::FETCH_ASSOC);
        if ($createView && isset($createView['Create View'])) {
            fwrite($fp, "\n-- View `$table`\n");
            fwrite($fp, "DROP VIEW IF EXISTS `$table`;\n");
            fwrite($fp, $createView['Create View'] . ";\n\n");
        }
    }

    // Columnas
    $colStmt = $pdo->query("DESCRIBE `" . str_replace('`','``',$table) . "`");
    $cols = $colStmt ? $colStmt->fetchAll(PDO::FETCH_COLUMN) : [];
    if (!$cols) continue;
    $colList = array_map(function($c){ return "`$c`"; }, $cols);
    $colListStr = implode(', ', $colList);

    // Datos en batches
    $batchSize = 200;
    $stmt = $pdo->query("SELECT * FROM `" . str_replace('`','``',$table) . "`");
    if (!$stmt) continue;
    $rowsBuffer = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $vals = [];
        foreach ($cols as $c) {
            if (!array_key_exists($c, $row) || $row[$c] === null) {
                $vals[] = 'NULL';
            } else {
                $vals[] = $pdo->quote($row[$c]);
            }
        }
        $rowsBuffer[] = '(' . implode(', ', $vals) . ')';
        if (count($rowsBuffer) >= $batchSize) {
            fwrite($fp, "INSERT INTO `$table` ($colListStr) VALUES\n" . implode(",\n", $rowsBuffer) . ";\n\n");
            $rowsBuffer = [];
        }
    }
    if (count($rowsBuffer) > 0) {
        fwrite($fp, "INSERT INTO `$table` ($colListStr) VALUES\n" . implode(",\n", $rowsBuffer) . ";\n\n");
    }
}

fwrite($fp, "SET FOREIGN_KEY_CHECKS=1;\n");
fclose($fp);

// Copiar a carpeta de backups en servidor si está habilitada
if ($backupEnabled) {
    $backupFile = $backupDir . DIRECTORY_SEPARATOR . $filename;
    @copy($dump_path, $backupFile);
}

// Enviar archivo al navegador
header('Content-Type: application/sql; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($dump_path));
readfile($dump_path);
@unlink($dump_path);
exit;