<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require './conexiondb/conexion.php';

    $email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        echo "Las contraseñas no coinciden.";
        exit;
    }

    // Encriptar la nueva contraseña
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    try {
        $pdo = connectDB();

        // Verificar si el correo electrónico existe
        $stmt = $pdo->prepare("SELECT * FROM usuarios_registrados WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() == 0) {
            echo "Correo electrónico no encontrado.";
            exit;
        }

        // Actualizar la contraseña
        $stmt = $pdo->prepare("UPDATE usuarios_registrados SET password = ? WHERE email = ?");
        $stmt->execute([$hashed_password, $email]);

        echo "Contraseña actualizada exitosamente.";
    } catch (PDOException $e) {
        echo "Error al actualizar la contraseña: " . $e->getMessage();
    }
}
?>