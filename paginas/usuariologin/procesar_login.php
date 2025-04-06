<?php
session_start();
require_once __DIR__ . '/utils/JWTHandler.php';

// Suponiendo que ya validaste las credenciales del usuario
if ($usuario_autenticado) {
    // Guarda el email en la sesión
    $_SESSION['email'] = $email;

    // Genera el token JWT
    $token = JWTHandler::generateToken(['email' => $email]);

    // Guarda el token en una cookie
    JWTHandler::setTokenCookie($token);

    // Redirige al usuario a la página de configuración
    header('Location: configuracion.php');
    exit;
} else {
    // Si las credenciales son incorrectas, redirige al inicio de sesión
    header('Location: iniciar_sesion.php');
    exit;
}
?>