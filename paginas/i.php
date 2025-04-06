<?php
require './menu/menu.php';
require 'ids_r_o.php';
require './api_printify.php'; // Incluye el archivo que maneja la API
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Principal</title>
    <link rel="stylesheet" href="./style.css">
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
        
    </style>
</head>
<body>
<?php
require './carrusel.php';
?>
<div class="content">
<br>

<!-- Banner de Promoción -->
<div class="container promo-banner">
    <h2>¡Gran Oferta!</h2>
    <p>Hasta un 50% de descuento en productos seleccionados. ¡No te lo pierdas!</p>
</div>

<!-- Sección de Ofertas -->
<div class="container">
    <h3 class="section-title ofertas-title">
        <i class="fas fa-tags"></i> Ofertas
    </h3>
    <div class="search-bar">
        <input type="text" id="search-ofertas" placeholder="Buscar en ofertas..." />
        <i class="fas fa-search"></i>
    </div>
    <div class="row" id="ofertas-section">
        <?php if (!empty($products['data'])): ?>
            <?php foreach ($products['data'] as $product): ?>
                <?php if (stripos($product['title'], 'oferta') !== false || stripos($product['title'], 'descuento') !== false): ?>
                    <div class="col-md-4 product-card ofertas-card">
                        <div class="card mb-4">
                            <?php if (!empty($product['images'][0]['src'])): ?>
                                <img src="<?php echo $product['images'][0]['src']; ?>" class="card-img-top" alt="<?php echo $product['title']; ?>">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/300" class="card-img-top" alt="Imagen no disponible">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $product['title']; ?></h5>
                                <div class="discount-badge">¡Oferta!</div>
                                <a href="pagina_de_pagos.php?product_id=<?php echo $product['id']; ?>" class="btn btn-primary">Comprar</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No se encontraron productos en oferta.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Banner de mas vendido -->
<div class="container promo-banner">
    <h2>¡Se venden como pan caliente!</h2>
    <p>No te pierdas de comprar uno de nuestros mejores productos. ¡Compra ya antes de que se agoten!</p>
</div>
<!-- Sección de Más Vendidos -->
<div class="container">
    <h3 class="section-title">
        <i class="fas fa-fire flame-icon"></i> Más Vendidos
    </h3>
    <div class="search-bar">
        <input type="text" id="search-mas-vendidos" placeholder="Buscar en más vendidos..." />
        <i class="fas fa-search"></i>
    </div>
    <div class="row" id="mas-vendidos-section">
        <?php if (!empty($products['data'])): ?>
            <?php foreach ($products['data'] as $product): ?>
                <?php if (stripos($product['title'], 'más vendido') !== false || stripos($product['title'], 'best seller') !== false): ?>
                    <div class="col-md-4 product-card mas-vendidos-card">
                        <div class="card mb-4">
                            <?php if (!empty($product['images'][0]['src'])): ?>
                                <img src="<?php echo $product['images'][0]['src']; ?>" class="card-img-top" alt="<?php echo $product['title']; ?>">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/300" class="card-img-top" alt="Imagen no disponible">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $product['title']; ?></h5>
                                <div class="best-seller-badge">Más Vendido</div>
                                <a href="pagina_de_pagos.php?product_id=<?php echo $product['id']; ?>" class="btn btn-primary">Comprar</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No se encontraron productos más vendidos.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Banner de Seguridad -->
<div class="container security-banner">
    <i class="fas fa-shield-alt"></i>
    <span>Compra 100% segura - Protegemos tus datos</span>
</div>
</div>

<?php
require 'footer.php';
?>

    <!-- Scripts -->
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script>
        // Buscador para Ofertas
        document.getElementById('search-ofertas').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const ofertasCards = document.querySelectorAll('.ofertas-card');

            ofertasCards.forEach(card => {
                const title = card.querySelector('.card-title').textContent.toLowerCase();
                if (title.includes(searchTerm)) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        });

        // Buscador para Más Vendidos
        document.getElementById('search-mas-vendidos').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const masVendidosCards = document.querySelectorAll('.mas-vendidos-card');

            masVendidosCards.forEach(card => {
                const title = card.querySelector('.card-title').textContent.toLowerCase();
                if (title.includes(searchTerm)) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        });
    </script>
</body>

</html>