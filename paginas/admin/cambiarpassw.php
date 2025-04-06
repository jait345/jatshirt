<?php
session_start();
require './menuadmin/menu.php';
require './conexion/conexion.php';

// Verificar sesión de administrador
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: authenticate.php');
    exit;
}
// Establecer conexión y verificar
try {
    $conexion = connectDB();
    if (!$conexion) {
        throw new Exception("No se pudo establecer conexión con la base de datos");
    }
} catch (Exception $e) {
    die("Error de conexión: " . $e->getMessage());
}

$admin_id = $_SESSION['admin_id']; // Obtener el ID del admin de la sesión
if (isset($_POST['update_email'])) {
    $new_email = trim($_POST['new_email']);
    
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Por favor ingresa un correo electrónico válido";
    } else {
        try {
            $stmt = $conexion->prepare("UPDATE admin SET email = ? WHERE id = ?");
            if ($stmt->execute([$new_email, $admin_id])) {
                $success = "Correo electrónico actualizado correctamente";
                $_SESSION['admin_email'] = $new_email; // Actualizar en sesión si es necesario
                $_POST = array();
            } else {
                $error = "Error al actualizar el correo electrónico";
            }
        } catch (PDOException $e) {
            error_log("Error al cambiar email: " . $e->getMessage());
            $error = "Error del sistema al actualizar el correo";
        }
    }
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['update_password'])) {
        $current_password = trim($_POST['current_password']);
        $new_password = trim($_POST['new_password']);
        $confirm_password = trim($_POST['confirm_password']);

        // Validaciones
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = "Todos los campos son obligatorios";
        } elseif ($new_password !== $confirm_password) {
            $error = "Las contraseñas no coinciden";
        } elseif (strlen($new_password) < 8) {
            $error = "La nueva contraseña debe tener al menos 8 caracteres";
        } else {
            try {
                // Obtener la contraseña actual del admin
                $stmt = $conexion->prepare("SELECT password FROM admin WHERE id = ?");
                $stmt->execute([$admin_id]);
                $admin = $stmt->fetch();

                if ($admin && password_verify($current_password, $admin['password'])) {
                    // Actualizar la contraseña
                    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                    $update_stmt = $conexion->prepare("UPDATE admin SET password = ? WHERE id = ?");
                    
                    if ($update_stmt->execute([$hashed_password, $admin_id])) {
                        $success = "Contraseña actualizada correctamente";
                        // Limpiar los campos del formulario
                        $_POST = array();
                    } else {
                        $error = "Error al actualizar la contraseña";
                    }
                } else {
                    $error = "Contraseña actual incorrecta";
                }
            } catch (PDOException $e) {
                error_log("Error al cambiar contraseña: " . $e->getMessage());
                $error = "Error del sistema al procesar la solicitud";
            }
        }
    }
    
    // Manejo para cerrar sesión
    if (isset($_POST['logout'])) {
        session_destroy();
        header('Location: authenticate.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Administrador</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            background-color: white;
        }
        .full-height-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .config-card {
            background-color: #6c757d;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            flex-grow: 1;
            margin: 20px 0;
        }
        .form-section {
            margin-bottom: 25px;
            padding: 20px;
            background-color: #495057;
            border-radius: 8px;
        }
        .btn-block {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid ">
        <div class="row justify-content-center ">
            <div class="">
                <div class="config-card card mt-3 mb-3">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4"><i class="fas fa-user-cog"></i> Configuración del Administrador</h2>
                        
                        <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-section">
                                    <h4><i class="fas fa-key"></i> Cambiar Contraseña</h4>
                                    <form action="#" method="POST">
                                        <div class="form-group">
                                            <label for="current_password">Contraseña Actual:</label>
                                            <input type="password" name="current_password" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="new_password">Nueva Contraseña:</label>
                                            <input type="password" name="new_password" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="confirm_password">Confirmar Nueva Contraseña:</label>
                                            <input type="password" name="confirm_password" class="form-control" required>
                                        </div>
                                        <button type="submit" name="update_password" class="btn btn-primary btn-block">
                                            <i class="fas fa-save"></i> Actualizar Contraseña
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-section">
                                    <h4><i class="fas fa-envelope"></i> Cambiar Correo</h4>
                                    <form action="#" method="POST">
                                        <div class="form-group">
                                            <label for="new_email">Nuevo Correo:</label>
                                            <input type="email" name="new_email" class="form-control" required>
                                        </div>
                                        <button type="submit" name="update_email" class="btn btn-warning btn-block">
                                            <i class="fas fa-envelope"></i> Actualizar Correo
                                        </button>
                                    </form>
                                </div>
                                
                                <div class="form-section mt-4">
                                    <h4><i class="fas fa-sign-out-alt"></i> Sesión</h4>
                                    <form action="#" method="POST">
                                        <button type="submit" name="logout" class="btn btn-danger btn-block">
                                            <i class="fas fa-power-off"></i> Cerrar Sesión
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
require 'footer.php';
?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>