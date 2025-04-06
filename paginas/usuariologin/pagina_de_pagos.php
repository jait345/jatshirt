<?php
require './menu.php';

// pagina_de_pagos.php

// Obtener el ID del producto desde la URL
$product_id = $_GET['product_id'] ?? null;

if (!$product_id) {
    die("Producto no válido.");
}

// Obtener los detalles del producto desde la API de Printify
$api_key = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzN2Q0YmQzMDM1ZmUxMWU5YTgwM2FiN2VlYjNjY2M5NyIsImp0aSI6ImZlYmRkYTk0YmJhMjA1NTE0ZDljODdmZGQ4YTI5YjNmZDNjMTE5MGY5NjM0ZGNmMDA2ZWNhM2FhNTc5NmI0MDJjYjFmNGM3OThmM2UyOWM1IiwiaWF0IjoxNzQwMjAwNDY0LjEyMzYxMiwibmJmIjoxNzQwMjAwNDY0LjEyMzYxNiwiZXhwIjoxNzcxNzM2NDY0LjExNTQ0MSwic3ViIjoiMjIwNzE4NTYiLCJzY29wZXMiOlsic2hvcHMubWFuYWdlIiwic2hvcHMucmVhZCIsImNhdGFsb2cucmVhZCIsIm9yZGVycy5yZWFkIiwib3JkZXJzLndyaXRlIiwicHJvZHVjdHMucmVhZCIsInByb2R1Y3RzLndyaXRlIiwid2ViaG9va3MucmVhZCIsIndlYmhvb2tzLndyaXRlIiwidXBsb2Fkcy5yZWFkIiwidXBsb2Fkcy53cml0ZSIsInByaW50X3Byb3ZpZGVycy5yZWFkIiwidXNlci5pbmZvIl19.AoGz6XwwHR7rpRnMuhqpQ5D475t9sd4GYZYLa10QK6F65ezVJGiuld87XMA2XnRrq_LUpmEw3rhUDAghfe4'; // Reemplaza con tu API Key de Printify
$shop_id = '20893289'; // Usa el shop_id que obtuviste

function get_product_details($product_id) {
    global $api_key, $shop_id;
    $url = "https://api.printify.com/v1/shops/$shop_id/products/$product_id.json";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $api_key,
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

$product = get_product_details($product_id);

if (empty($product)) {
    die("No se pudo obtener la información del producto.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Pagos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SDK de PayPal -->
    <script src="https://www.paypal.com/sdk/js?client-id=AYuW6bDyQD4_ZaWz4AXXrjYh2aYbCN1YkGY8xzW5ou_uYKC4tGSpUBqsjOVf7boKEXNvIVvXBX3veLi0&currency=USD"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Pagar: <?php echo htmlspecialchars($product['title'] ?? 'Producto no disponible'); ?></h2>
        <div class="row">
            <div class="col-md-6">
                <?php if (!empty($product['images'][0]['src'])): ?>
                    <img src="<?php echo htmlspecialchars($product['images'][0]['src']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($product['title']); ?>">
                <?php else: ?>
                    <img src="https://via.placeholder.com/300" class="img-fluid" alt="Imagen no disponible">
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <h3><?php echo htmlspecialchars($product['title'] ?? 'Producto no disponible'); ?></h3>
                <p><?php echo htmlspecialchars($product['description'] ?? 'Descripción no disponible'); ?></p>
                
                <!-- Seleccionar talla y mostrar precio -->
                <form id="payment-form">
                    <div class="mb-3">
                        <label for="talla" class="form-label">Selecciona tus tallas:</label>
                        <select class="form-select" id="talla" name="talla[]" multiple required>
                            <?php if (!empty($product['variants'])): ?>
                                <?php foreach ($product['variants'] as $variant): ?>
                                    <option value="<?php echo htmlspecialchars($variant['id']); ?>" data-price="<?php echo htmlspecialchars($variant['price']); ?>">
                                        <?php echo htmlspecialchars($variant['title']); ?> - $<?php echo number_format($variant['price'], 2); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">No hay tallas disponibles</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="precio" class="form-label">Precio total:</label>
                        <input type="text" id="precio" class="form-control" readonly>
                    </div>
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                    <button type="button" id="add-to-cart" class="btn btn-secondary">Agregar al carrito</button>
                </form>

                <!-- Botón de PayPal -->
                <div id="paypal-button-container"></div>
            </div>
        </div>
    </div>
    <?php
    require 'footer.php';
    ?>
    <!-- Script para actualizar el precio y el botón de PayPal -->
    <script>
        // Función para actualizar el precio
        function updatePrice() {
            var select = document.getElementById('talla');
            var options = select.selectedOptions;
            var totalPrice = 0;

            for (var i = 0; i < options.length; i++) {
                totalPrice += parseFloat(options[i].getAttribute('data-price'));
            }

            document.getElementById('precio').value = '$' + totalPrice.toFixed(2);
            return totalPrice.toFixed(2); // Devuelve el precio sin el símbolo $
        }

        // Mostrar el precio inicial al cargar la página
        window.onload = function() {
            var precio = updatePrice();
            renderPayPalButton(precio); // Renderizar el botón de PayPal con el precio inicial
        };

        // Actualizar el botón de PayPal cuando se cambia la talla
        document.getElementById('talla').addEventListener('change', function() {
            var precio = updatePrice();
            renderPayPalButton(precio); // Renderizar el botón de PayPal con el nuevo precio
        });

        // Función para renderizar el botón de PayPal
        function renderPayPalButton(amount) {
            // Limpiar el contenedor del botón de PayPal antes de renderizar
            document.getElementById('paypal-button-container').innerHTML = '';

            paypal.Buttons({
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: amount // Usar el monto dinámico
                            }
                        }]
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(details) {
                        alert('Transacción completada por ' + details.payer.name.given_name);
                    });
                }
            }).render('#paypal-button-container');
        }

        // Agregar al carrito
        document.getElementById('add-to-cart').addEventListener('click', function() {
            var select = document.getElementById('talla');
            var options = select.selectedOptions;
            var cart = JSON.parse(localStorage.getItem('cart')) || [];

            for (var i = 0; i < options.length; i++) {
                var variantId = options[i].value;
                var variantTitle = options[i].text.split(' - ')[0];
                var variantPrice = options[i].getAttribute('data-price');

                cart.push({
                    product_id: "<?php echo htmlspecialchars($product['id']); ?>",
                    variant_id: variantId,
                    title: variantTitle,
                    price: variantPrice
                });
            }

            localStorage.setItem('cart', JSON.stringify(cart));
            alert('Productos agregados al carrito');
        });
    </script>
</body>
</html>