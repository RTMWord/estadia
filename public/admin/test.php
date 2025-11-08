<?php
// test.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Intenta cargar la base de datos para ver si ahí falla
require_once '../../app/config/db.php'; 

echo "¡El servidor web funciona y la base de datos conecta!";
exit;
?>