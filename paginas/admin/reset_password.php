<?php
require './conexion/conexion.php';

$token = $_GET['token'] ?? '';
$conexion = connectDB();

if (empty($token)) {
    die("Token no proporcionado.");
}

try {
    // Verificar token válido y no expirado
    $stmt = $conexion->prepare("SELECT admin_id FROM password_reset_tokens 
                               WHERE token = :token 
                               AND usado = 0 
                               AND fecha_expiracion > NOW()");
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        die("El enlace no es válido o ha expirado.");
    }
    
    $token_data = $stmt->fetch();
    $admin_id = $token_data['admin_id'];
    
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $new_password = trim($_POST['new_password']);
        $confirm_password = trim($_POST['confirm_password']);
        
        if ($new_password !== $confirm_password) {
            $error = "Las contraseñas no coinciden.";
        } else {
            // Actualizar contraseña
            $hashed_new = hash('sha256', $new_password);
            
            $update = $conexion->prepare("UPDATE admin 
                                        SET password = :password, 
                                            ultimo_cambio_password = NOW() 
                                        WHERE id = :id");
            $update->bindParam(':password', $hashed_new, PDO::PARAM_STR);
            $update->bindParam(':id', $admin_id, PDO::PARAM_INT);
            $update->execute();
            
            // Registrar en historial
            $history = $conexion->prepare("INSERT INTO password_history 
                                        (admin_id, password) 
                                        VALUES (:id, :password)");
            $history->bindParam(':id', $admin_id, PDO::PARAM_INT);
            $history->bindParam(':password', $hashed_new, PDO::PARAM_STR);
            $history->execute();
            
            // Marcar token como usado
            $mark_used = $conexion->prepare("UPDATE password_reset_tokens 
                                            SET usado = 1 
                                            WHERE token = :token");
            $mark_used->bindParam(':token', $token, PDO::PARAM_STR);
            $mark_used->execute();
            
            $success = "Contraseña actualizada correctamente. Ahora puedes iniciar sesión.";
        }
    }
} catch (PDOException $e) {
    error_log("Error en reset: " . $e->getMessage());
    die("Ocurrió un error al procesar tu solicitud.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Restablecer Contraseña</title>
</head>
<body>
    <?php if (isset($error)) echo "<p>$error</p>"; ?>
    <?php if (isset($success)) echo "<p>$success</p>"; ?>
    
    <?php if (!isset($success)): ?>
    <form method="post">
        <div>
            <label>Nueva Contraseña:</label>
            <input type="password" name="new_password" required>
        </div>
        <div>
            <label>Confirmar Nueva Contraseña:</label>
            <input type="password" name="confirm_password" required>
        </div>
        <button type="submit">Restablecer Contraseña</button>
    </form>
    <?php endif; ?>
</body>
</html>