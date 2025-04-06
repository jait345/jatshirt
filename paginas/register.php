<?php
// Habilitar errores para depuración (quítalo en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Asegurar respuesta JSON
header('Content-Type: application/json');

// Verificar si es POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Método inválido"]);
    exit;
}

// Obtener datos del formulario
$nombre_completo = trim($_POST["fullName"] ?? '');
$email = trim($_POST["email"] ?? '');
$password = $_POST["password"] ?? '';
$confirmPassword = $_POST["confirmPassword"] ?? '';
$humanCheck = trim($_POST["humanCheck"] ?? '');
$errors = [];

// Validación de la pregunta humana (5 + 3)
if ($humanCheck !== "8") {
    $errors[] = "Respuesta incorrecta en la verificación humana.";
}

// Validar nombre (solo letras y espacios)
if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u", $nombre_completo)) {
    $errors[] = "El nombre solo debe contener letras y espacios.";
}

// Validar email (único en la BD)
require './conexiondb/conexion.php'; // Asegúrate de que la ruta sea correcta
$pdo = connectDB();
$stmt = $pdo->prepare("SELECT id FROM usuarios_registrados WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    $errors[] = "El correo ya está registrado.";
}

// Validar contraseña
if (!preg_match("/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $password)) {
    $errors[] = "La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un carácter especial.";
}

// Confirmación de contraseña
if ($password !== $confirmPassword) {
    $errors[] = "Las contraseñas no coinciden.";
}

// Si hay errores, devolver JSON con errores
if (!empty($errors)) {
    echo json_encode(["status" => "error", "messages" => $errors]);
    exit;
}

// Registrar usuario
$hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash de la contraseña
$stmt = $pdo->prepare("INSERT INTO usuarios_registrados (nombre_completo, email, contraseña) VALUES (?, ?, ?)");

if ($stmt->execute([$nombre_completo, $email, $hashedPassword])) {
    echo json_encode(["status" => "success", "message" => "Registro exitoso."]);
} else {
    echo json_encode(["status" => "error", "message" => "Error al registrar en la base de datos."]);
}

exit;
?>