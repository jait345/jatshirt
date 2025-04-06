<?php
require __DIR__ . '/../../vendor/autoload.php'; // Asegúrate de que la ruta sea correcta
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTHandler {
    private static $secretKey = 'tu_clave_secreta'; // Cambia esto por una clave segura

    // Generar un token JWT
    public static function generateToken($payload) {
        return JWT::encode($payload, self::$secretKey, 'HS256');
    }

    // Decodificar un token JWT
    public static function decodeToken($token) {
        try {
            return JWT::decode($token, new Key(self::$secretKey, 'HS256'));
        } catch (Exception $e) {
            error_log("Error decodificando token JWT: " . $e->getMessage());
            return null;
        }
    }

    // Validar un token JWT
    public static function validateToken($token) {
        try {
            $decoded = self::decodeToken($token);
            return $decoded && isset($decoded->exp) && $decoded->exp > time();
        } catch (Exception $e) {
            error_log("Error validando token JWT: " . $e->getMessage());
            return false;
        }
    }

    // Obtener el token JWT de una cookie
    public static function getTokenFromCookie() {
        return $_COOKIE['jwt_token'] ?? null;
    }
}
?>