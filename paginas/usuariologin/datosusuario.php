<?php
session_start();
require './conexiondb/conexion.php';

// Check if user is logged in and session is not expired
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['last_activity'])) {
    header('Location: login.php');
    exit;
}

// Check for session timeout (30 minutes)
$session_timeout = 1800; // 30 minutes in seconds
if (time() - $_SESSION['last_activity'] > $session_timeout) {
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Get user data from session
$user_id = $_SESSION['usuario_id'];
$email = $_SESSION['email'];
$nombre = $_SESSION['nombre'];
$apellido = $_SESSION['apellido'];
$telefono = $_SESSION['telefono'];

// Establish database connection for updates
$pdo = connectDB();

// Process profile updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (isset($_POST['update_profile'])) {
            // Validate and sanitize inputs
            $nuevo_nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
            $nuevo_apellido = filter_input(INPUT_POST, 'apellido', FILTER_SANITIZE_STRING);
            $nuevo_email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $nuevo_telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_STRING);
            
            if (!$nuevo_nombre || !$nuevo_apellido || !$nuevo_email || !$nuevo_telefono) {
                throw new Exception("Datos de entrada inválidos");
            }

            $update_query = "UPDATE usuarios_registrados SET nombre = ?, apellido = ?, email = ?, telefono = ? WHERE id = ?";
            $stmt = $pdo->prepare($update_query);
            $stmt->execute([$nuevo_nombre, $nuevo_apellido, $nuevo_email, $nuevo_telefono, $user_id]);
            
            // Update session variables
            $_SESSION['nombre'] = $nuevo_nombre;
            $_SESSION['apellido'] = $nuevo_apellido;
            $_SESSION['email'] = $nuevo_email;
            $_SESSION['telefono'] = $nuevo_telefono;
            
            header('Location: datosusuario.php?success=1');
            exit;
        }
        
        if (isset($_POST['change_password'])) {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            
            if ($new_password !== $confirm_password) {
                throw new Exception("Las contraseñas nuevas no coinciden");
            }
            
            // Verify current password
            $stmt = $pdo->prepare("SELECT contraseña FROM usuarios_registrados WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
            if (!password_verify($current_password, $user['contraseña'])) {
                throw new Exception("La contraseña actual es incorrecta");
            }
            
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = "UPDATE usuarios_registrados SET contraseña = ? WHERE id = ?";
            $stmt = $pdo->prepare($update_query);
            $stmt->execute([$hashed_password, $user_id]);
            
            header('Location: datosusuario.php?success=2');
            exit;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <?php 
                        if ($_GET['success'] == 1) echo "Perfil actualizado exitosamente";
                        if ($_GET['success'] == 2) echo "Contraseña actualizada exitosamente";
                        ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="mb-0">Datos del Perfil</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="update_profile" value="1">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="apellido" class="form-label">Apellido</label>
                                <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Cambiar Contraseña</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="change_password" value="1">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Contraseña Actual</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirmar Nueva Contraseña</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-warning">Cambiar Contraseña</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>