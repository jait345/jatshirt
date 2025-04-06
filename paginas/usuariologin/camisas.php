<?php
session_start(); // Start session before any output
require './menu.php';
require './api_printify.php'; // Incluye el archivo que maneja la API

// Inicializar variable de email del usuario
$userEmail = isset($_SESSION['email']) ? $_SESSION['email'] : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .description-container {
            overflow: hidden;
            max-height: 0;
            opacity: 0;
            transition: max-height 0.5s ease-out, opacity 0.5s ease-out;
        }

        .description-container.open {
            max-height: 500px;
            opacity: 1;
        }

        .category-button {
            margin: 5px;
            padding: 50px 100px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
        }

        .category-button i {
            margin-right: 10px;
        }

        .hidden {
            display: none;
        }

        .alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: none;
        }

        .description-container {
            overflow: hidden;
            max-height: 0;
            opacity: 0;
            transition: max-height 0.5s ease-out, opacity 0.5s ease-out;
        }

        .description-container.open {
            max-height: 500px;
            opacity: 1;
        }

        .category-button {
        width: 300px;
        height: 300px;
        background-size: cover;
        background-position: center;
        border: none;
        color: white;
        font-size: 16px;
        font-weight: bold;
        text-transform: uppercase;
        position: relative;
        display: inline-block;
        margin: 10px;
        overflow: hidden;
        cursor: pointer;
        transition: transform 0.3s;
    }

    .category-button span {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.5);
        padding: 8px 12px;
        border-radius: 5px;
        backdrop-filter: blur(5px);
    }

    .category-button:hover {
        transform: scale(1.1);
    }



        .mensaje {
            background: transparent(255, 255, 255, 0.2);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            animation: fadeIn 1s ease-in-out;
        }

        .mensaje p {
            margin: 10px 0;
            border-radius: 5px;
            transition: transform 0.3s ease-in-out;
        }

        .mensaje p:first-child {
            font-size: 1.2rem;
            font-weight: bold;
            color: #fff;
            background: #ff4b5c;
            animation: bounceIn 1.0s ease-in-out;
        }

        .mensaje p:last-child {
            font-size: 1.2rem;
            color: #333;
            background: #ffdd94;
            animation: slideIn 1.2s ease-in-out;
        }

        .mensaje p:hover {
            transform: scale(1.05);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes bounceIn {
            0% { transform: scale(0.5); opacity: 0; }
            60% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>
</head>
<body>
    <div id="alertMessage" class="alert alert-success" role="alert">
        Producto agregado al carrito!
    </div>

    <div class="perfil" style="background-image: url('https://via.placeholder.com/150');"></div>
    <div class="user-info text-center">
        <?php if ($userEmail): ?>
            <p>Bienvenido, <strong><?php echo htmlspecialchars($userEmail); ?></strong></p>
            <a href="logout.php" class="btn-logout">Cerrar sesión</a>
        <?php else: ?>
            <p>No has iniciado sesión.</p>
        <?php endif; ?>
    </div>

    <div class="container mt-5 text-center">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <input type="text" id="searchInput" class="form-control form-control-lg" placeholder="Buscar...">
            </div>
        </div>
    </div>

    <!-- Botones de categorías -->
    <div class="container mt-5 text-center">
    <button class="category-button" onclick="toggleCategory('camiseta_corta')" style="background-image: url('./IMG/categorias/camisa\ corta\ .jpg');">
        <span><i class="fas fa-tshirt"></i> Camisetas Cortas</span>
    </button>
    <button class="category-button" onclick="toggleCategory('sudadera')" style="background-image: url('./IMG/categorias/sudadera.jpg');">
        <span><i class="fas fa-hoodie"></i> Sudaderas</span>
    </button>
    <button class="category-button" onclick="toggleCategory('camiseta_larga')" style="background-image: url('./IMG/categorias/camiseta\ larga.jpg');">
        <span><i class="fas fa-long"></i> Camisetas Largas</span>
    </button>
</div>

    <div class="container mt-5">
        <h1>Productos Disponibles</h1>
        <div class="row" id="productosContainer">
            <?php if (!empty($products['data'])): ?>
                <?php foreach ($products['data'] as $product): ?>
                    <?php
                    $category = 'camiseta_corta'; // Cambia esto según la lógica de tu aplicación
                    if (strpos(strtolower($product['title']), 'sudadera') !== false) {
                        $category = 'sudadera';
                    } elseif (strpos(strtolower($product['title']), 'larga') !== false) {
                        $category = 'camiseta_larga';
                    }
                    ?>
                    <div class="col-md-4 producto <?php echo $category; ?> hidden">
                        <div class="card mb-4" style="background-color:rgb(94, 94, 94); border-radius: 10px; padding: 15px;">
                            <?php if (!empty($product['images'][0]['src'])): ?>
                                <img src="<?php echo $product['images'][0]['src']; ?>" class="card-img-top" alt="<?php echo $product['title']; ?>">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/300" class="card-img-top" alt="Imagen no disponible">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $product['title']; ?></h5>
                                <div class="description-container">
                                    <p class="card-text" id="productDescription"><?php echo $product['description']; ?></p>
                                </div>
                                <button onclick="toggleDescription(this)">Mostrar descripción</button>
                                <form class="add-to-cart-form" method="POST">
                                    <input type="hidden" name="agregar" value="1">
                                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="nombre" value="<?php echo $product['title']; ?>">
                                    <input type="hidden" name="precio" value="<?php echo $product['variants'][0]['price']; ?>">
                                    <button type="submit" class="btn btn-primary">Agregar al carrito</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No se encontraron productos.</p>
            <?php endif; ?>
        </div>
        <div class="mt-4">
            <a href="carrito.php" class="btn btn-success">Ver Carrito</a>
        </div>
    </div>

    <?php require 'footer.php'; ?>

    <script>
        $(document).ready(function() {
            $('#searchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('#productosContainer .producto').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });


            $('.add-to-cart-form').on('submit', function(e) {
    e.preventDefault(); // Prevenir envío de formulario tradicional
    var form = $(this);
    var data = form.serialize(); // Serializar los datos del formulario
    console.log('Datos enviados al servidor: ', data); // Ver los datos antes de enviarlos
    $.ajax({
        type: 'POST',
        url: 'acciones_carrito.php',
        data: data,
        success: function(response) {
            console.log('Respuesta del servidor:', response); // Mostrar la respuesta del servidor
            var result = JSON.parse(response); // Parsear la respuesta JSON
            if (result.status === 'success') {
                $('#alertMessage').text(result.message).fadeIn().delay(2000).fadeOut();
            } else if (result.status === 'warning') {
                alert(result.message); // Advertir si el producto ya está en el carrito
            } else {
                alert('Hubo un problema al agregar el producto al carrito.');
            }
        },
        error: function() {
            alert('Error al agregar el producto al carrito.');
        }
    });
});
        });

        function toggleDescription(button) {
            var container = button.previousElementSibling;
            container.classList.toggle("open");
            button.textContent = container.classList.contains("open") ? "Ocultar descripción" : "Mostrar descripción";
        }

        function toggleCategory(category) {
            document.querySelectorAll('.producto').forEach(function(product) {
                product.classList.add('hidden');
            });

            document.querySelectorAll('.' + category).forEach(function(product) {
                product.classList.remove('hidden');
            });
        }
    </script>
</body>
</html>
