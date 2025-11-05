<?php
// app/init.php
// Define rutas globales (usar require_once __DIR__ . '/../app/init.php' desde public/*)
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));        // repo root
    define('APP_PATH', ROOT_PATH . '/app');
    define('PUBLIC_PATH', ROOT_PATH . '/public');
    define('VIEWS_PARTIALS', APP_PATH . '/views/partials');
}