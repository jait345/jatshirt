<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href='./menu.css'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        .navbar {
            height: 90px;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 22px;
        }
        .nav-link:hover i {
            transform: rotate(360deg);
        }
        
        /* Estilos para el menú desplegable */
        #adminMenu {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            z-index: 1000;
            min-width: 160px;
            margin-top: 0;
        }
        
        .navbar-nav {
            position: relative; /* Necesario para posicionar correctamente el dropdown */
        }
        
        .dropdown-menu-end {
            right: 0;
            left: auto;
        }
    </style>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark w-100">
            <div class="container-fluid">
                <a href="../../paginas/i.php" class="custom-link">JAT-SHIRT--Inicio</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="../admin/admin_panel.php" id="link-inicio">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./camisas.php" id="link-camisas">Camisas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../admin/datostotales.php" id="link-datos">Datos Totales</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../admin/descifrar.php" id="link-decifrardatos">Descifrar datos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../../paginas/usuariologin/vizualisador_comentarioscifrea.php" id="link-comentarios">Comentarios</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="#" id="adminDropdown">
                                <i class="fas fa-user-cog" style="font-size: 34px;"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" id="adminMenu">
                                <li><a  class="dropdown-item" href="../admin/cambiarpassw.php">Configuraciones avanzadas</a></li>
                                <li><a  class="dropdown-item" href="../admin/logout.php" id="link-cerrarsesion">Cerrar sesión</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Detectar la URL actual para resaltar el menú correspondiente
        const currentPath = window.location.pathname;
        const menuLinks = {
            '/admin_panel.php': 'link-inicio',
            '/camisas.php': 'link-camisas',
            '/../admin/datostotales.php': 'link-datos',
            '/../admin/descifrar.php': 'link-descifrardatos',
            '/../../paginas/usuariologin/vizualisador_comentarioscifrea.php': 'link-comentarios',
            '/datosusuario.php': 'link-usuario',
            '/carrito.php': 'link-carrito',
        };

        // Resaltar el enlace activo
        Object.keys(menuLinks).forEach(path => {
            if (currentPath.includes(path)) {
                document.getElementById(menuLinks[path]).classList.add('active');
            }
        });

        // Manejo del menú de admin
        document.getElementById('adminDropdown').addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const menu = document.getElementById('adminMenu');
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        });

        // Cerrar menú si se hace clic fuera
        document.addEventListener('click', function(e) {
            const menu = document.getElementById('adminMenu');
            const icon = document.getElementById('adminDropdown');
            
            if (!icon.contains(e.target) && !menu.contains(e.target)) {
                menu.style.display = 'none';
            }
        });

        // Cerrar menú al seleccionar una opción
        document.querySelectorAll('#adminMenu .dropdown-item').forEach(item => {
            item.addEventListener('click', () => {
                document.getElementById('adminMenu').style.display = 'none';
            });
        });
    </script>
</body>
</html>