<?php
ob_start();
// session_start(); // Removed redundant session_start() since session is already initialized
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./menu.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        .navbar{
            height: 90px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 22px;
        }
    </style>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark w-100">
        <div class="container-fluid">
            <a href="../Admin/sesionadmin.php" class="custom-link">JAT-SHIRT</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="./ilogin.php" id="link-inicio">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./camisas.php" id="link-camisas">Camisas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./servicios.php" id="link-servicios">Servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./comentarios.php" id="link-comentarios">Comentarios</a>
                    </li>
                    </li>
                    <!-- Ícono de carrito de compras -->
                    <li class="nav-item">
                        <a class="nav-link" href="./carrito.php" id="link-carrito">
                        <i class="fas fa-shopping-cart" style="font-size: 24px;"></i>    
                            <span class="badge bg-danger" id="cart-count">0</span> <!-- Contador de productos -->
                        </a>
                    </li>

                    <?php if(isset($_SESSION['email'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['email']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="./configuracion.php"><i class="fas fa-cog"></i> Configuración</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="./logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
<body>
    <script>
        // Detectar la URL actual para resaltar el menú correspondiente
        const currentPath = window.location.pathname;

        // Asociar las rutas a los enlaces del menú
        const menuLinks = {
            '/ilogin.php': 'link-inicio',
            '/camisas.php': 'link-camisas', // Nuevo enlace para camisas.php
            '/servicios.php': 'link-servicios',
            '/comentarios.php': 'link-comentarios',
            '/datosusuario.php': 'link-usuario',
            '/carrito.php': 'link-carrito'
        };

        // Resaltar el enlace activo
        Object.keys(menuLinks).forEach(path => {
            if (currentPath.includes(path)) {
                document.getElementById(menuLinks[path]).classList.add('active');
            }
        });

        // Manejo de eventos para abrir y cerrar el menú
        document.querySelectorAll('.navbar-nav .nav-link, .navbar-nav .dropdown-item').forEach(function(link) {
            link.addEventListener('click', function() {
                var navbarToggler = document.querySelector('.navbar-toggler');
                var navbarCollapse = document.querySelector('.navbar-collapse');
                if (navbarCollapse.classList.contains('show')) {
                    navbarToggler.click(); // Simular clic para cerrar
                }
            });
        });

        // Cerrar el menú si se hace clic fuera del menú
        document.addEventListener('click', function(event) {
            var navbarToggler = document.querySelector('.navbar-toggler');
            var navbarCollapse = document.querySelector('.navbar-collapse');
            if (navbarCollapse.classList.contains('show') && !event.target.closest('.navbar')) {
                navbarToggler.click(); // Simular clic para cerrar
            }
        });


        
    </script>
</body>
</html>