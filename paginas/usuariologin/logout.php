<?php
session_start();
require __DIR__ . '/conexiondb/conexion.php'; // Conexión a la base de datos

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // Actualizar el campo is_verified a 0
    try {
        $conn = connectDB();
        $stmt = $conn->prepare("UPDATE usuarios_registrados SET is_verified = 0 WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    } catch (PDOException $e) {
        error_log('Error de base de datos durante el logout: ' . $e->getMessage());
    }

    // Destruir la sesión
    session_unset();
    session_destroy();
}

// Redirigir al usuario a la página de inicio de sesión
header('Location: login.php');
exit;
?>