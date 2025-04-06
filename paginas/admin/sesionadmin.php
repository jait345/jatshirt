<?php
session_start();
require './conexion/conexion.php';

// Inicializar $conexion al principio
$conexion = connectDB();

// Si ya ha iniciado sesión, redirigir al panel de administración
if (isset($_SESSION['admin_logged_in'])) {
    $_SESSION['toast'] = [
        'type' => 'info',
        'message' => 'Ya has iniciado sesión'
    ];
    header('Location: ./admin_panel.php');
    exit;
}

// Verificar si necesita cambiar contraseña por tiempo
if (isset($_SESSION['admin_id'])) {
    $dias_maximos = 90; // Configuración: días máximos para cambiar contraseña
    
    // Usamos $conexion que ya está definida
    $stmt = $conexion->prepare("SELECT ultimo_cambio_password FROM admin WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['admin_id'], PDO::PARAM_INT);
    $stmt->execute();
    $last_change = $stmt->fetchColumn();

    if ($last_change && strtotime($last_change) < strtotime("-$dias_maximos days")) {
        $_SESSION['toast'] = [
            'type' => 'warning',
            'message' => 'Debes cambiar tu contraseña por seguridad'
        ];
        header('Location: cambiar_password.php?expired=1');
        exit;
    }
}

// Procesar el formulario de login si se envió
$error = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($conexion) {
        try {
            // Preparar la consulta para obtener el usuario
            $query = "SELECT id, nombre, email, password, ultimo_cambio_password FROM admin WHERE email = :email AND activo = 1 LIMIT 1";
            $stmt = $conexion->prepare($query);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            $admin = $stmt->fetch();

            // Verificar las credenciales
            if ($admin && hash('sha256', $password) === $admin['password']) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_nombre'] = $admin['nombre'];
                $_SESSION['admin_email'] = $admin['email'];
                
                // Verificar si necesita cambiar contraseña
                $dias_maximos = 90;
                if ($admin['ultimo_cambio_password'] && strtotime($admin['ultimo_cambio_password']) < strtotime("-$dias_maximos days")) {
                    $_SESSION['toast'] = [
                        'type' => 'warning',
                        'message' => 'Por seguridad, debes cambiar tu contraseña'
                    ];
                    header('Location: cambiar_password.php?expired=1');
                } else {
                    $_SESSION['toast'] = [
                        'type' => 'success',
                        'message' => 'Bienvenido ' . htmlspecialchars($admin['nombre'])
                    ];
                    header('Location: admin_panel.php');
                }
                exit;
            } else {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Credenciales incorrectas'
                ];
                $error = "Credenciales incorrectas.";
            }
        } catch (PDOException $e) {
            error_log("Error en la consulta: " . $e->getMessage());
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Error del sistema. Intente nuevamente.'
            ];
            $error = "Ocurrió un error, intente nuevamente.";
        }
    } else {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => 'Error de conexión a la base de datos'
        ];
        $error = "Error de conexión a la base de datos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - YouMultimedia</title>
    <link rel="stylesheet" href="./sesionadmin.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .password-container {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
        .error-message {
            color: #dc3545;
            margin-top: 5px;
        }
        .toast-success {
            background-color: #28a745 !important;
        }
        .toast-error {
            background-color: #dc3545 !important;
        }
        .toast-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }
        .toast-info {
            background-color: #17a2b8 !important;
        }
    </style>
</head>
<body class="bg-dark text-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card mt-5 bg-secondary">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Iniciar Sesión</h2>
                        
                        <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form action="sesionadmin.php" method="POST" autocomplete="off">
                            <div class="form-group">
                                <label for="email">Correo Electrónico:</label>
                                <input type="email" name="email" class="form-control" required autofocus>
                            </div>
                            <div class="form-group password-container">
                                <label for="password">Contraseña:</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                                <span class="toggle-password" onclick="togglePassword()">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            <div class="form-group text-right">
                                <a href="forgot_password.php" class="text-light">¿Olvidaste tu contraseña?</a>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery primero -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Luego Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <script>
        // Configuración de Toastr
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": true,
            "timeOut": "5000",
            "extendedTimeOut": "1000"
        };

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.querySelector('.toggle-password i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Mostrar mensajes toast
        <?php if (isset($_SESSION['toast'])): ?>
            toastr.<?= $_SESSION['toast']['type'] ?>('<?= addslashes($_SESSION['toast']['message']) ?>');
            <?php unset($_SESSION['toast']); ?>
        <?php endif; ?>
    </script>
</body>
</html>