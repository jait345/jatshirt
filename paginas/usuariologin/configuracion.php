<?php
ob_start();
session_start();
require './menu.php';
require_once __DIR__ . '/utils/MFAHandler.php';
require_once __DIR__ . '/utils/JWTHandler.php'; // Asegúrate de que esta clase exista
require_once __DIR__ . '/conexiondb/conexion.php';

// Inicializar el manejador de MFA
try {
    MFAHandler::initialize();
} catch (Exception $e) {
    error_log('Error initializing MFA: ' . $e->getMessage());
}


// Obtener datos del usuario desde la base de datos
try {
    $pdo = connectDB();
    $stmt = $pdo->prepare("SELECT id, nombre_completo, email, contrasena, mfa_code, created_at, is_verified 
                           FROM usuarios_registrados WHERE email = ?");
    $stmt->execute([$_SESSION['email']]);
    $userData = $stmt->fetch();
    
    if (!$userData) {
        // Si el usuario no existe en la base de datos, redirigir a login
        $sessionError = 'Usuario no encontrado. Por favor, inicie sesión nuevamente.';
        header('Location: login.php');
        exit;
    }

    // Almacenar datos del usuario en la sesión
    $_SESSION['id'] = $userData['id'];
    $_SESSION['nombre'] = $userData['nombre_completo'];
    $_SESSION['email'] = $userData['email'];
    $_SESSION['mfa_code'] = $userData['mfa_code'];
    $_SESSION['is_verified'] = (bool)$userData['is_verified'];
    $_SESSION['created_at'] = $userData['created_at'];
} catch (PDOException $e) {
    error_log("Error al recuperar datos del usuario: " . $e->getMessage());
    $sessionError = 'Error al recuperar datos del usuario. Por favor, intente nuevamente.';
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';
    $response = ['success' => false, 'message' => '', 'error' => ''];

    switch ($action) {
        case 'change_password':
            if (isset($_POST['current_password']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
                if ($_POST['new_password'] !== $_POST['confirm_password']) {
                    $response['error'] = 'Las contraseñas nuevas no coinciden';
                    break;
                }

                if (strlen($_POST['new_password']) < 8) {
                    $response['error'] = 'La contraseña debe tener al menos 8 caracteres';
                    break;
                }

                try {
                    $pdo = connectDB();
                    $stmt = $pdo->prepare("SELECT contrasena FROM usuarios_registrados WHERE email = ?");
                    $stmt->execute([$_SESSION['email']]);
                    $userData = $stmt->fetch();

                    if ($userData && password_verify($_POST['current_password'], $userData['contrasena'])) {
                        $hashedPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                        $updateStmt = $pdo->prepare("UPDATE usuarios_registrados SET contrasena = ? WHERE email = ?");
                        $updateStmt->execute([$hashedPassword, $_SESSION['email']]);
                        
                        $response['success'] = true;
                        $response['message'] = 'Contraseña actualizada exitosamente';
                    } else {
                        $response['error'] = 'La contraseña actual es incorrecta';
                    }
                } catch (PDOException $e) {
                    error_log("Error updating password: " . $e->getMessage());
                    $response['error'] = 'Error al actualizar la contraseña';
                }
            }
            break;

        case 'update_mfa':
            try {
                $mfa_enabled = isset($_POST['mfa_code']) ? 1 : 0;
                $pdo = connectDB();

                if ($mfa_enabled) {
                    $secret = MFAHandler::generateSecretKey();
                    if (empty($secret)) {
                        throw new Exception('Error al generar la clave secreta MFA');
                    }

                    $code = MFAHandler::generateEmailCode();
                    if (!MFAHandler::sendVerificationCode($_SESSION['email'], $code)) {
                        throw new Exception('Error al enviar el código de verificación por correo');
                    }

                    MFAHandler::storeVerificationCode($_SESSION['email'], $code);
                    $_SESSION['temp_mfa_secret'] = $secret;
                    
                    $response['success'] = true;
                    $response['message'] = 'Código de verificación enviado a su correo';
                    $response['qr_code_url'] = MFAHandler::getQRCodeUrl('JAT-SHIRT', $_SESSION['email'], $secret);
                } else {
                    $stmt = $pdo->prepare("UPDATE usuarios_registrados SET mfa_code = NULL WHERE email = ?");
                    $stmt->execute([$_SESSION['email']]);

                    unset($_SESSION['mfa_code']);
                    $response['success'] = true;
                    $response['message'] = 'MFA desactivado exitosamente';
                }
            } catch (Exception $e) {
                $response['success'] = false;
                $response['error'] = 'Error en la configuración MFA: ' . $e->getMessage();
                error_log('MFA Error: ' . $e->getMessage());
            }
            break;

        case 'verify_mfa_code':
            try {
                $verification_code = $_POST['verification_code'] ?? '';
                if (empty($verification_code)) {
                    throw new Exception('Por favor ingrese el código de verificación');
                }

                if (!MFAHandler::verifyStoredCode($_SESSION['email'], $verification_code)) {
                    throw new Exception('Código de verificación inválido o expirado');
                }

                if (isset($_SESSION['temp_mfa_secret'])) {
                    $pdo = connectDB();
                    $stmt = $pdo->prepare("UPDATE usuarios_registrados SET mfa_code = ? WHERE email = ?");
                    $stmt->execute([$_SESSION['temp_mfa_secret'], $_SESSION['email']]);

                    $_SESSION['mfa_code'] = $_SESSION['temp_mfa_secret'];
                    unset($_SESSION['temp_mfa_secret']);
                    $response['success'] = true;
                    $response['message'] = 'MFA activado exitosamente';
                } else {
                    throw new Exception('Error en la configuración MFA: clave secreta no encontrada');
                }
            } catch (Exception $e) {
                $response['success'] = false;
                $response['error'] = $e->getMessage();
                error_log('MFA Verification Error: ' . $e->getMessage());
            }
            break;
    }

    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .config-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .user-info {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="user-info">
            <h3><i class="fas fa-user"></i> Información del Usuario</h3>
            <div class="row mt-3">
                <div class="col-md-6">
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'No disponible'); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Correo:</strong> <?php echo htmlspecialchars($_SESSION['email'] ?? 'No disponible'); ?></p>
                </div>
            </div>
        </div>

        <h2 class="mb-4">Configuración de Perfil</h2>

        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="config-section">
            <h3><i class="fas fa-key"></i> Cambiar Contraseña</h3>
            <form method="POST" class="mt-3">
                <input type="hidden" name="action" value="change_password">
                <div class="mb-3">
                    <label for="current_password" class="form-label">Contraseña Actual</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">Nueva Contraseña</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirmar Nueva Contraseña</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
            </form>
        </div>

        <div class="config-section">
            <h3><i class="fas fa-shield-alt"></i> Autenticación de Dos Factores (MFA)</h3>
            <form method="POST" class="mt-3" id="mfaForm">
                <input type="hidden" name="action" value="update_mfa">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="mfa_enabled" name="mfa_enabled" <?php echo isset($_SESSION['mfa_code']) && $_SESSION['mfa_code'] ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="mfa_enabled">Activar Autenticación de Dos Factores</label>
                </div>
                <div id="qrCodeSection" style="display: none;" class="mt-3">
                    <div class="alert alert-info">
                        <p>1. Escanee el código QR con Google Authenticator</p>
                        <p>2. Ingrese el código de verificación enviado a su correo</p>
                    </div>
                    <div class="text-center mb-3">
                        <img id="qrCode" src="" alt="QR Code" style="max-width: 200px; margin: 20px auto;">
                    </div>
                    <div class="mb-3">
                        <label for="verification_code" class="form-label">Código de Verificación</label>
                        <input type="text" class="form-control" id="verification_code" name="verification_code" maxlength="6" pattern="\d{6}" placeholder="Ingrese el código de 6 dígitos">
                    </div>
                    <button type="button" class="btn btn-primary" id="verifyMfaCode">Verificar Código</button>
                </div>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            let mfaEnabled = <?php echo isset($_SESSION['mfa_code']) && $_SESSION['mfa_code'] ? 'true' : 'false'; ?>;

            $('#mfaForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                $.ajax({
                    url: 'configuracion.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            showAlert('success', response.message);
                            if (response.qr_code_url) {
                                $('#qrCode').attr('src', 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' + encodeURIComponent(response.qr_code_url));
                                $('#qrCodeSection').show();
                            } else {
                                $('#qrCodeSection').hide();
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            }
                        } else {
                            showAlert('danger', response.error || 'Error en la operación');
                        }
                    },
                    error: function() {
                        showAlert('danger', 'Error al procesar la solicitud');
                    }
                });
            });

            $('#verifyMfaCode').on('click', function() {
                const code = $('#verification_code').val();
                if (!code) {
                    showAlert('danger', 'Por favor ingrese el código de verificación');
                    return;
                }

                $.ajax({
                    url: 'configuracion.php',
                    type: 'POST',
                    data: {
                        action: 'verify_mfa_code',
                        verification_code: code
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert('success', response.message);
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else {
                            showAlert('danger', response.error || 'Error al verificar el código');
                        }
                    },
                    error: function() {
                        showAlert('danger', 'Error al procesar la solicitud');
                    }
                });
            });

            function showAlert(type, message) {
                const alertDiv = $('<div>')
                    .addClass(`alert alert-${type} alert-dismissible fade show`)
                    .attr('role', 'alert')
                    .html(`${message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`);
                
                $('.container').prepend(alertDiv);
                
                setTimeout(function() {
                    alertDiv.alert('close');
                }, 5000);
            }
        });
    </script>
</body>
</html>