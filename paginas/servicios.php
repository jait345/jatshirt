<?php
require './menu/menu.php';
require 'ids_r_o.php';
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios - YouMultimedia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Estilos personalizados -->
    <style>
        body {
            background-color: #162938;
    
        font-family: 'Roboto Condensed', sans-serif;
        }

        h1 {
            font-family: 'Roboto Condensed', sans-serif;
            font-weight: 700; /* Negrita */
        }

        p {
            font-family: 'Roboto Condensed', sans-serif;
            font-weight: 300; /* Ligero */
        }

        .texts {
            color: black;
        }
        p{
            color: #ddd;
        }
        li{
            color:#ddd;
        }

        /* Estilo para las tarjetas de servicios */
        .service-card {
            border: 10px solid grey;
            border-radius: 80px;
            transition: transform 0.3s, box-shadow 0.3s;
            padding: 100px;
            background-color:#162938;
            margin-bottom: 20px;
        }

        /* Efecto hover para las tarjetas */
        .service-card:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        /* Animación al cargar la página */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .fade-in.show {
            opacity: 1;
            transform: translateY(0);
        }

        .img-fluid {
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .icon-animation {
            transition: color 0.7s ease;
        }

        .icon-animation:hover {
            color: red;
        }
        .carousel-item img {
            max-height: 250px; /* Altura máxima para las imágenes del carrusel */
            width: 100%; /* Anchura al 100% para imágenes responsivas en el carrusel */
            object-fit: cover; /* Asegura que la imagen cubra el área sin distorsión */
        }

        
    </style>
</head>
<body>


    <!-- Sección de Bienvenida -->
<div class="info-section text-center">
    <h2 class="texts">Camisas Personalizadas a tu Estilo</h2>
    <p>En YouFashion, te ofrecemos una amplia variedad de camisas personalizadas para que puedas expresar tu estilo único. Escoge tu diseño, elige el tamaño y color, y haz tu pedido de manera rápida y fácil.</p>
</div>

<!-- Servicios -->
<div class="row">
    <!-- Servicio de Diseño Personalizado -->
    <div class="col-md-6 fade-in service-card">
        <h3 class="text-warning">Diseña tu Camisa Personalizada</h3>
        <p>Explora nuestras opciones de diseño, donde puedes agregar tus propias imágenes, texto o logotipos. Nos aseguramos de que cada camisa sea única, reflejando tu estilo personal.</p>
        <ul class="text-start">
            <li><strong>Opciones de personalización</strong> con imágenes, textos y logotipos.</li>
            <li>Elige entre <strong>diversos colores y tamaños</strong> para adaptarse a tu gusto.</li>
            <li>Vista previa de tu diseño en tiempo real antes de realizar el pedido.</li>
        </ul>
    </div>

    <!-- Servicio de Calidad de Producto -->
    <div class="col-md-6 fade-in service-card">
        <h3 class="text-warning">Calidad Premium en Camisas</h3>
        <p>Las camisas están hechas con materiales de alta calidad para asegurar comodidad y durabilidad. Cada prenda es cuidadosamente confeccionada para ofrecerte lo mejor.</p>
        <ul class="text-start">
            <li><strong>Tejido suave y cómodo</strong> para uso diario.</li>
            <li>Disponible en una variedad de <strong>tamaños y cortes</strong>.</li>
            <li>Impresión de alta resolución para que tu diseño luzca siempre perfecto.</li>
        </ul>
    </div>

    <!-- Carrito de Compras -->
    <div class="col-md-12 fade-in service-card">
        <h3 class="text-warning">Tu Carrito de Compras</h3>
        <p>Añade las camisas que te gusten a tu carrito y realiza el pago de manera segura. Revisa los productos antes de finalizar tu compra.</p>
        <ul class="text-start">
            <li>Accede a tu <strong>carrito de compras</strong> desde cualquier página.</li>
            <li>Puedes <strong>modificar la cantidad</strong> de productos o eliminar artículos.</li>
            <li>Revisa el <strong>total de la compra</strong> y elige tu forma de pago.</li>
        </ul>
    </div>

    <!-- Opciones de Pago -->
    <div class="col-md-12 fade-in service-card">
        <h3 class="text-warning">Opciones de Pago en Línea</h3>
        <p>Realiza tu compra de manera segura con nuestras opciones de pago en línea, para que puedas recibir tus camisas personalizadas rápidamente.</p>
        <ul class="text-start">
            <li><strong>Pago con tarjeta de crédito</strong> (Visa, MasterCard, American Express).</li>
            <li><strong>Pago con PayPal</strong> para una experiencia más rápida.</li>
            <li><strong>Pago contra reembolso</strong> disponible en ciertas regiones.</li>
        </ul>
    </div>
</div>

<!-- Footer -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Animaciones Fade-in al hacer scroll
    document.addEventListener('DOMContentLoaded', function () {
        const faders = document.querySelectorAll('.fade-in');
        const appearOptions = {
            threshold: 0.5
        };

        const appearOnScroll = new IntersectionObserver(function(entries, appearOnScroll) {
            entries.forEach(entry => {
                if (!entry.isIntersecting) {
                    return;
                } else {
                    entry.target.classList.add('show');
                    appearOnScroll.unobserve(entry.target);
                }
            });
        }, appearOptions);

        faders.forEach(fader => {
            appearOnScroll.observe(fader);
        });
    });



</script>
<?php
require 'footer.php';
?>
</body>
</html>
