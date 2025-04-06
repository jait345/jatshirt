<?php
require './conexiondb/conexion.php';

// Habilitar errores para depuración (quítalo en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Asegurar respuesta JSON
header('Content-Type: application/json');

// Verificar si es una solicitud GET y si se proporcionó el nombre completo
if ($_SERVER["REQUEST_METHOD"] !== "GET" || !isset($_GET["fullName"])) {
    echo json_encode(["status" => "error", "message" => "Solicitud inválida"]);
    exit;
}

// Obtener el nombre completo
$fullName = trim($_GET["fullName"]);

// Validar que el nombre completo no esté vacío
if (empty($fullName)) {
    echo json_encode(["status" => "error", "message" => "Nombre completo no proporcionado"]);
    exit;
}

// Conectar a la base de datos
$pdo = connectDB();

// Verificar si el nombre completo ya existe
$stmt = $pdo->prepare("SELECT id FROM usuarios_registrados WHERE nombre_completo = ?");
$stmt->execute([$fullName]);

if ($stmt->fetch()) {
    // Si el nombre completo ya está registrado
    echo json_encode(["status" => "error", "message" => "Nombre completo ya registrado", "available" => false]);
} else {
    // Si el nombre completo está disponible
    echo json_encode(["status" => "success", "message" => "Nombre completo disponible", "available" => true]);
}

exit;
?>
