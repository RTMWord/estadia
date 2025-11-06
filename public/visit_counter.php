<?php
// visit_counter.php
// Gestión simple y segura de contador de visitas usando un archivo.
// - Crea la carpeta data/ y el archivo data/visits.txt con valor "0" si no existen.
// - Para uso con index.php: include/require y llamar a increment_and_get_visits().
// - Si se llama con ?only_read=1 devuelve solo el número (útil para AJAX).

define('VISITS_DIR', __DIR__ . '/data');
define('VISITS_FILE', VISITS_DIR . '/visits.txt');

function ensure_visits_file() {
    if (!is_dir(VISITS_DIR)) {
        mkdir(VISITS_DIR, 0755, true);
    }
    if (!file_exists(VISITS_FILE)) {
        // inicializa con 0
        file_put_contents(VISITS_FILE, "0", LOCK_EX);
    }
}

function read_visits() {
    ensure_visits_file();
    $fp = fopen(VISITS_FILE, 'r');
    if (!$fp) return 0;
    flock($fp, LOCK_SH);
    $count = intval(trim(fread($fp, 20)));
    flock($fp, LOCK_UN);
    fclose($fp);
    return $count;
}

function increment_and_get_visits() {
    ensure_visits_file();
    $fp = fopen(VISITS_FILE, 'c+');
    if (!$fp) return 0;
    // bloqueo exclusivo mientras actualizamos
    flock($fp, LOCK_EX);
    rewind($fp);
    $content = stream_get_contents($fp);
    $count = intval(trim($content));
    $count++;
    // escribimos nuevo valor
    rewind($fp);
    ftruncate($fp, 0);
    fwrite($fp, (string)$count);
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    return $count;
}

// Modo lectura simple para peticiones AJAX: ?only_read=1
if (php_sapi_name() !== 'cli' && isset($_GET['only_read']) && $_GET['only_read'] == '1') {
    // No incrementa, solo lee
    header('Content-Type: text/plain; charset=utf-8');
    echo read_visits();
    exit;
}