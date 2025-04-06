<?php
session_start();
require_once __DIR__ . '/utils/JWTHandler.php';

// Verificar si el usuario ya tiene una sesión activa
if (isset($_SESSION['email'])) {
    // Obtener el token JWT de la cookie
    $token = JWTHandler::getTokenFromCookie();

    if ($token) {
        // Validar el token JWT
        $decoded = JWTHandler::validateToken($token);

        if ($decoded) {
            // Si el token es válido, redirigir al usuario a la página de configuración
            header('Location: configuracion.php');
            exit;
        } else {
            // Si el token no es válido, destruir la sesión y eliminar la cookie
            session_destroy();
            JWTHandler::removeTokenCookie();
            header('Location: login.php'); // Redirigir al inicio de sesión
            exit;
        }
    } else {
        // Si no hay token, destruir la sesión y redirigir al inicio de sesión
        session_destroy();
        header('Location: login.php');
        exit;
    }
} else {
    // Si no hay sesión activa, redirigir al inicio de sesión
    header('Location: login.php');
    exit;
}
?>