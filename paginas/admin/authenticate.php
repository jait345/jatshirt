<?php
session_start();
require './conexion/conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conexion = connectDB();
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($conexion) {
        try {
            $query = "SELECT id, email, password FROM admin WHERE email = :email LIMIT 1";
            $stmt = $conexion->prepare($query);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                // Datos de sesión
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_email'] = $admin['email'];
                
                // Mensaje de éxito (flotante verde)
                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => '¡Bienvenido al panel de administración!'
                ];
                header('Location: admin_panel.php');
                exit;
            } else {
                // Mensaje de error (flotante rojo)
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Credenciales incorrectas'
                ];
                header('Location: sesionadmin.php');
                exit;
            }
        } catch (PDOException $e) {
            error_log("Error en authenticate.php: " . $e->getMessage());
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Error del sistema. Intente nuevamente.'
            ];
            header('Location: sesionadmin.php');
            exit;
        }
    } else {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => 'Error de conexión a la base de datos'
        ];
        header('Location: sesionadmin.php');
        exit;
    }
} else {
    header('Location: sesionadmin.php');
    exit;
}
?>