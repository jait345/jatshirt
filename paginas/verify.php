<?php
header('Content-Type: application/json');

require_once './conexiondb/conexion.php';

// Get verification parameters
$verificationCode = $_GET['code'] ?? '';
$email = $_GET['email'] ?? '';

if (empty($verificationCode) || empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Parámetros de verificación inválidos']);
    exit;
}

try {
    $pdo = connectDB();
    
    // Check if verification code is valid and not expired
    $stmt = $pdo->prepare("SELECT id, verification_code, verification_expiry FROM usuarios_registrados WHERE email = ? AND is_verified = 0");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado o ya verificado']);
        exit;
    }

    if ($user['verification_code'] !== $verificationCode) {
        echo json_encode(['status' => 'error', 'message' => 'Código de verificación inválido']);
        exit;
    }

    if (strtotime($user['verification_expiry']) < time()) {
        echo json_encode(['status' => 'error', 'message' => 'El enlace de verificación ha expirado']);
        exit;
    }

    // Update user verification status
    $stmt = $pdo->prepare("UPDATE usuarios_registrados SET is_verified = 1, verification_code = NULL, verification_expiry = NULL WHERE id = ?");
    if ($stmt->execute([$user['id']])) {
        echo json_encode(['status' => 'success', 'message' => '¡Cuenta verificada exitosamente! Ya puedes iniciar sesión.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al verificar la cuenta']);
    }

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
}
?>