<?php
header('Content-Type: application/json'); // Asegúrate de que este encabezado esté presente

session_start();
require __DIR__ . '/conexiondb/conexion.php'; // Conexión a la base de datos
require __DIR__ . '/vendor/autoload.php'; // Autoload de Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Configuración de Google
$clientID = '1012880852310-oodrcuf6qe4oehir3124po0tugq2eu6s.apps.googleusercontent.com';
$clientSecret = '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';

// Leer los datos JSON enviados en el cuerpo de la solicitud (si es una solicitud JSON)
$input = json_decode(file_get_contents('php://input'), true);

// Si no es una solicitud JSON, usar $_POST
if (json_last_error() !== JSON_ERROR_NONE || empty($input)) {
    $input = $_POST; // Usar $_POST si no se pudo decodificar JSON o si está vacío
}

// Manejo de autenticación de Google
if (isset($input['credential'])) {
    $idToken = $input['credential'];
    try {
        $client = new Google_Client(['client_id' => $clientID]);
        $payload = $client->verifyIdToken($idToken);

        if ($payload) {
            $googleId = $payload['sub']; // ID único de Google
            $googleEmail = $payload['email'];
            $googleName = $payload['name'];
            
            // Verifica si el usuario ya existe en la tabla usuarios_google
            try {
                $conn = connectDB();
                $stmt = $conn->prepare("SELECT * FROM usuarios_google WHERE google_id = :google_id");
                $stmt->bindParam(':google_id', $googleId);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    // El usuario ya existe, obtener sus datos
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['nombre'] = $user['nombre_completo'];
                } else {
                    // Crear nuevo usuario en la tabla usuarios_google
                    $stmt = $conn->prepare("INSERT INTO usuarios_google (google_id, email, nombre_completo) VALUES (:google_id, :email, :nombre)");
                    $stmt->bindParam(':google_id', $googleId);
                    $stmt->bindParam(':email', $googleEmail);
                    $stmt->bindParam(':nombre', $googleName);
                    $stmt->execute();

                    $_SESSION['email'] = $googleEmail;
                    $_SESSION['nombre'] = $googleName;
                }

                // Generar el token JWT
                $payload = [
                    'email' => $googleEmail,
                    'exp' => time() + 3600, // Expira en 1 hora
                ];
                $token = JWTHandler::generateToken($payload);

                // Almacenar el token en una cookie segura
                setcookie('jwt_token', $token, time() + 3600, '/', '', true, true);

                // Depuración
                error_log("Usuario autenticado con Google: " . $googleEmail);
                error_log("Redirigiendo a configuracion.php...");

                // Redirigir al usuario a la página de configuración
                header('Location: configuracion.php');
                exit;
            } catch (PDOException $e) {
                error_log('Error de base de datos: ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Error de base de datos. Por favor, inténtalo más tarde.']);
                exit;
            }
        }
    } catch (Exception $e) {
        error_log('Error en autenticación Google: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error en la autenticación con Google.']);
        exit;
    }
}

// Manejo de autenticación manual (email y password)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($input['credential'])) {
    try {
        // Verifica si los campos están definidos
        if (!isset($input['email']) || !isset($input['password'])) {
            throw new Exception('Los campos email y password son requeridos.');
        }

        $email = trim($input['email']);
        $contrasena = $input['password'];

        // Verifica las credenciales del usuario
        try {
            $conn = connectDB();
        } catch (Exception $e) {
            error_log('Error de conexión a la base de datos: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos. Por favor, inténtalo más tarde.']);
            exit;
        }

        $stmt = $conn->prepare("SELECT * FROM usuarios_registrados WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($contrasena, $user['contrasena'])) {
                // Credenciales válidas, genera un código de verificación
                $verificationCode = rand(100000, 999999); // Código de 6 dígitos

                // Guarda el código en la base de datos
                $stmt = $conn->prepare("UPDATE usuarios_registrados SET mfa_code = :mfa_code WHERE email = :email");
                $stmt->bindParam(':mfa_code', $verificationCode);
                $stmt->bindParam(':email', $email);
                $stmt->execute();

                $_SESSION['email'] = $email;

                // Envía el código al correo del usuario
                sendVerificationEmail($email, $verificationCode);

                // Devuelve una respuesta JSON para mostrar la sección de MFA
                echo json_encode(['success' => true, 'message' => 'Se ha enviado un código de verificación a tu correo.']);
                exit;
            } else {
                // Contraseña incorrecta
                echo json_encode(['success' => false, 'message' => 'Correo o contrasena incorrectos.']);
                exit;
            }
        } else {
            // Usuario no encontrado
            echo json_encode(['success' => false, 'message' => 'Correo o contrasena incorrectos.']);
            exit;
        }
    } catch (Exception $e) {
        error_log('Error en el proceso de inicio de sesión: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Manejo de verificación del código MFA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($input['mfa_code'])) {
    try {
        // Verifica si se envió el código MFA
        if (!isset($input['mfa_code'])) {
            throw new Exception('Código MFA no proporcionado.');
        }

        $userCode = trim($input['mfa_code']);
        $email = $_SESSION['email'];

        // Verifica el código MFA en la base de datos
        $conn = connectDB();
        $stmt = $conn->prepare("SELECT mfa_code FROM usuarios_registrados WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $storedCode = $user['mfa_code'];

            if ($userCode == $storedCode) {
                // Código correcto, completar el inicio de sesión
                $_SESSION['loggedin'] = true;

                // Marcar al usuario como verificado
                $stmt = $conn->prepare("UPDATE usuarios_registrados SET is_verified = 1, mfa_code = NULL WHERE email = :email");
                $stmt->bindParam(':email', $email);
                $stmt->execute();

                echo json_encode(['success' => true, 'message' => 'Inicio de sesión exitoso.']);
                exit;
            } else {
                // Código incorrecto
                echo json_encode(['success' => false, 'message' => 'Código de verificación incorrecto.']);
                exit;
            }
        } else {
            // Usuario no encontrado
            echo json_encode(['success' => false, 'message' => 'Error en la verificación.']);
            exit;
        }
    } catch (Exception $e) {
        // Manejar errores y devolver un JSON con el mensaje de error
        http_response_code(400); // Código de estado HTTP para errores
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

/**
 * Envía un correo electrónico con el código de verificación.
 */
function sendVerificationEmail($email, $code) {
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

        // Remitente y destinatario
        $mail->setFrom('jesusibarra31104@gmail.com', 'JAT-SHIRT');
        $mail->addAddress($email); // Correo del destinatario

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Código de verificación - JAT-SHIRT';
        $mail->Body = "<div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f5f5f5;'>"
                    . "<h2 style='color: #333;'>Verificación de Inicio de Sesión</h2>"
                    . "<p>Tu código de verificación es:</p>"
                    . "<h1 style='color: #4CAF50; font-size: 32px;'><b>$code</b></h1>"
                    . "<p>Este código expirará en 10 minutos.</p>"
                    . "<p>Si no solicitaste este código, por favor ignora este correo.</p>"
                    . "</div>";

        // Envía el correo
        if (!$mail->send()) {
            throw new Exception('Error al enviar el correo: ' . $mail->ErrorInfo);
        }
    } catch (Exception $e) {
        error_log('Error al enviar el correo: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error al enviar el código de verificación. Por favor, intenta nuevamente.']);
        exit;
    }
}

require __DIR__ . '/utils/JWTHandler.php';

// Después de validar las credenciales del usuario
if ($userData && password_verify($password, $userData['contrasena'])) {
    // Crear el payload del token
    $payload = [
        'email' => $userData['email'],
        'exp' => time() + 3600, // Expira en 1 hora
    ];

    // Generar el token JWT
    $token = JWTHandler::generateToken($payload);

    // Almacenar el token en una cookie segura
    setcookie('jwt_token', $token, time() + 3600, '/', '', true, true);

    // Redirigir al usuario a la página de perfil
    header('Location: configuracion.php');
    exit;
}
?>