<?php
session_start();
require './conexiondb/conexion.php'; // Asegura la conexión a la BD

define('AES_KEY', 'clave_secreta_123456'); // Clave secreta para AES
define('AES_METHOD', 'AES-256-CBC');

date_default_timezone_set('America/Mexico_City'); // Zona horaria

header('Content-Type: application/json'); // Respuesta en formato JSON

function encryptAES($data) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(AES_METHOD));
    $encrypted = openssl_encrypt($data, AES_METHOD, AES_KEY, 0, $iv);
    return base64_encode($iv . $encrypted);
}

try {
    if (!isset($_SESSION['email'])) {
        throw new Exception('Usuario no autenticado.');
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido.');
    }
    if (empty($_POST['comentario']) || trim($_POST['comentario']) === '') {
        throw new Exception('El comentario no puede estar vacío.');
    }

    $pdo = connectDB();
    $comentario = trim($_POST['comentario']);
    $email_usuario = $_SESSION['email'];
    
    // Verificar si se marcó la opción "Enviar anónimamente"
    $anonimo = isset($_POST['anonimo']) && $_POST['anonimo'] === 'on';
    if ($anonimo) {
        $comentario = encryptAES($comentario);
    }

    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('No se pudo crear el directorio de subida.');
        }
    }

    $imagePath = null;
    $videoPath = null;

    function uploadFile($file, $allowedTypes, $uploadDir) {
        if (!isset($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $fileName = basename($file['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowedTypes)) {
            throw new Exception('Tipo de archivo no permitido: ' . $fileExt);
        }

        $newFileName = uniqid() . '.' . $fileExt;
        $destination = $uploadDir . $newFileName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception('Error al mover el archivo subido.');
        }

        return $newFileName;
    }

    if (!empty($_FILES['imagen']['name'])) {
        $imagePath = uploadFile($_FILES['imagen'], ['jpg', 'jpeg', 'png', 'gif'], $uploadDir);
    }
    if (!empty($_FILES['video']['name'])) {
        $videoPath = uploadFile($_FILES['video'], ['mp4', 'avi', 'mov', 'wmv'], $uploadDir);
    }

    $stmt = $pdo->prepare("INSERT INTO comentarios (comentario, imagen, video, fecha, email_usuario, anonimo) VALUES (?, ?, ?, NOW(), ?, ?)");
    $stmt->execute([$comentario, $imagePath, $videoPath, $email_usuario, $anonimo ? 1 : 0]);

    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
