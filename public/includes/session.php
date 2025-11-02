<?php
/**
 * session.php
 * Inicia la sesión de PHP y proporciona funciones básicas de autenticación.
 */

// Si la sesión no ha sido iniciada, se inicia.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Función para verificar si un usuario ha iniciado sesión.
 * @return bool
 */
function is_logged_in() {
    // Verifica si la variable de sesión 'user_id' existe y es válida
    return isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id']);
}

/**
 * Redirige al usuario a la página de login si no está autenticado.
 * @param bool $redirectToLogin Si es true, redirige a login.php
 */
function require_login($redirectToLogin = true) {
    if (!is_logged_in()) {
        if ($redirectToLogin) {
            // Guardar la URL actual para redirigir al usuario después de iniciar sesión
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            header('Location: login.php');
            exit();
        }
        return false;
    }
    return true;
}

/**
 * Ejemplo de cómo usar require_login en una página protegida (ej. citas.php):
 * require_once 'includes/session.php';
 * require_login();
 * // El código de citas.php continúa solo si el usuario está logueado
 */
?>
