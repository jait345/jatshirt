<?php
require './conexion/conexion.php';

// Cargar PHPMailer desde la carpeta vendor
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $conexion = connectDB();

    try {
        // Verificar si el email existe y está activo
        $stmt = $conexion->prepare("SELECT id, nombre FROM admin WHERE email = :email AND activo = 1");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $admin = $stmt->fetch();
            $admin_id = $admin['id'];
            $admin_name = $admin['nombre'];
            
            // Generar token único
            $token = bin2hex(random_bytes(32));
            $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Guardar token en la base de datos
            $insert = $conexion->prepare("INSERT INTO password_reset_tokens 
                                       (admin_id, token, fecha_expiracion) 
                                       VALUES (:admin_id, :token, :expiration)");
            $insert->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
            $insert->bindParam(':token', $token, PDO::PARAM_STR);
            $insert->bindParam(':expiration', $expiration, PDO::PARAM_STR);
            $insert->execute();
            
            // Configurar PHPMailer
            $mail = new PHPMailer(true);
            
            try {
                // Configuración del servidor SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'jesusibarra31104@gmail.com'; // Tu correo de Gmail
                $mail->Password = 'cbwq kduy drfl liwl'; // Contraseña de aplicación
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Usar STARTTLS
                $mail->Port = 587; // Puerto para STARTTLS
                $mail->CharSet = 'UTF-8';
                
                // Remitente
                $mail->setFrom('no-reply@tudominio.com', 'Sistema YouMultimedia');
                $mail->addAddress($email, $admin_name); // Destinatario
                
                // Contenido del correo
                $mail->isHTML(true);
                $mail->Subject = 'Restablecer tu contraseña - YouMultimedia';
                
                // Obtener la URL base dinámicamente
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
                $host = $_SERVER['HTTP_HOST'];
                $base_url = $protocol . $host . dirname($_SERVER['PHP_SELF']);
                $base_url = rtrim($base_url, '/\\'); // Eliminar la última barra
                
                // Crear enlace absoluto
                $reset_link = $base_url . "/reset_password.php?token=$token";
                
                $mail->Body = "
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <style>
                            body { font-family: Arial, sans-serif; line-height: 1.6; }
                            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                            .header { background-color: #343a40; color: white; padding: 10px; text-align: center; }
                            .content { padding: 20px; background-color: #f8f9fa; }
                            .button {
                                display: inline-block;
                                padding: 10px 20px;
                                background-color: #007bff;
                                color: white !important;
                                text-decoration: none;
                                border-radius: 5px;
                                margin: 15px 0;
                            }
                            .footer { margin-top: 20px; font-size: 12px; color: #6c757d; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <div class='header'>
                                <h2>YouMultimedia</h2>
                            </div>
                            <div class='content'>
                                <h3>Hola $admin_name,</h3>
                                <p>Hemos recibido una solicitud para restablecer tu contraseña.</p>
                                <p>Por favor, haz clic en el siguiente botón para continuar con el proceso:</p>
                                <p>
                                    <a href='$reset_link' class='button'>Restablecer Contraseña</a>
                                </p>
                                <p>Si no solicitaste este cambio, puedes ignorar este mensaje.</p>
                                <p>El enlace expirará en 1 hora.</p>
                            </div>
                            <div class='footer'>
                                <p>© " . date('Y') . " YouMultimedia. Todos los derechos reservados.</p>
                            </div>
                        </div>
                    </body>
                    </html>
                ";
                
                $mail->AltBody = "Hola $admin_name,\n\nPara restablecer tu contraseña, visita el siguiente enlace:\n$reset_link\n\nSi no solicitaste este cambio, ignora este mensaje.\n\nEl enlace expirará en 1 hora.";
                
                $mail->send();
                $success = "Se ha enviado un enlace de recuperación a tu correo electrónico.";
            } catch (Exception $e) {
                error_log("Error al enviar correo: " . $mail->ErrorInfo);
                $error = "Ocurrió un error al enviar el correo. Por favor, inténtalo de nuevo más tarde.";
            }
        } else {
            $error = "No existe una cuenta activa con ese correo electrónico.";
        }
    } catch (PDOException $e) {
        error_log("Error en recuperación: " . $e->getMessage());
        $error = "Ocurrió un error al procesar tu solicitud.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - YouMultimedia</title>
    <link rel="stylesheet" href="./sesionadmin.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-dark text-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card mt-5 bg-secondary">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Recuperar Contraseña</h2>
                        
                        <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                        <div class="text-center mt-3">
                            <a href="login.php" class="btn btn-primary">Volver al Login</a>
                        </div>
                        <?php else: ?>
                        <form method="post">
                            <div class="form-group">
                                <label>Correo Electrónico:</label>
                                <input type="email" name="email" class="form-control" required autofocus>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Enviar Enlace de Recuperación</button>
                            <div class="text-center mt-3">
                                <a href="login.php" class="text-light">Volver al Login</a>
                            </div>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>