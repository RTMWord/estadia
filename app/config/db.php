<?php
// Conexión a la base de datos usando variables de entorno (.env)

/**
 * Carga variables de entorno desde un archivo .env simple (KEY=VALUE).
 * No requiere dependencias externas y sobrescribe valores existentes.
 */
function loadEnvFile($envPath)
{
    if (!is_readable($envPath)) {
        return;
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }

        if (strpos($line, 'export ') === 0) {
            $line = trim(substr($line, 7));
        }

        $eqPos = strpos($line, '=');
        if ($eqPos === false) {
            continue;
        }

        $key = trim(substr($line, 0, $eqPos));
        $value = trim(substr($line, $eqPos + 1));

        if ($key === '') {
            continue;
        }

        // Quitar comillas simples o dobles si existen.
        $len = strlen($value);
        if ($len >= 2) {
            $first = $value[0];
            $last = $value[$len - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

function envValue($key, $default = null)
{
    if (isset($_ENV[$key])) {
        return $_ENV[$key];
    }
    if (isset($_SERVER[$key])) {
        return $_SERVER[$key];
    }
    $value = getenv($key);
    if ($value !== false) {
        return $value;
    }
    return $default;
}

$projectRoot = dirname(__DIR__, 2);
$envPath = $projectRoot . '/.env';
loadEnvFile($envPath);

$host = envValue('DB_HOST', 'localhost');
$db = envValue('DB_NAME', '');
$user = envValue('DB_USER', '');
$pass = envValue('DB_PASS', '');
$charset = envValue('DB_CHARSET', 'utf8mb4');

if ($db === '' || $user === '') {
    throw new RuntimeException('Falta configurar DB_NAME o DB_USER en el archivo .env');
}

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}
?>
