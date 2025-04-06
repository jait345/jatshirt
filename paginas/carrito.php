<?php
session_start();
require_once './api_printify.php';
require './menu/menu.php';
require 'ids_r_o.php';

// Verificar si el carrito tiene productos
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    echo '<p>No hay productos en el carrito.</p>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Incluir jQuery para AJAX -->
    <!-- SDK de PayPal -->
    <script src="https://www.paypal.com/sdk/js?client-id=AYuW6bDyQD4_ZaWz4AXXrjYh2aYbCN1YkGY8xzW5ou_uYKC4tGSpUBqsjOVf7boKEXNvIVvXBX3veLi0&currency=USD"></script>
</head>
<body>
    <div class="container mt-5">
        <h1>Carrito de Compras</h1>
        <div id="mensaje"></div> <!-- Mensaje de confirmación de acciones -->
        <div class="row">
            <div class="col-12">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Total</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($_SESSION['carrito'] as $id => $item) {
                            $subtotal = $item['precio'] * $item['cantidad'];
                            $total += $subtotal;
                            ?>
                            <tr id="producto-<?php echo $item['id']; ?>">
                                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                                <td>
                                    <!-- Botones para sumar y restar -->
                                    <button class="btn btn-primary btn-sm" onclick="actualizarCantidad('<?php echo $item['id']; ?>', 'sumar')">+</button>
                                    <span id="cantidad-<?php echo $item['id']; ?>"><?php echo $item['cantidad']; ?></span>
                                    <button class="btn btn-warning btn-sm" onclick="actualizarCantidad('<?php echo $item['id']; ?>', 'restar')">-</button>
                                </td>
                                <td>$<?php echo number_format($item['precio'], 2); ?></td>
                                <td>$<?php echo number_format($subtotal, 2); ?></td>
                                <td>
                                    <!-- Botón para eliminar producto -->
                                    <button class="btn btn-danger btn-sm" onclick="eliminarProducto('<?php echo $item['id']; ?>')">Eliminar</button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <h3>Total: $<?php echo number_format($total, 2); ?></h3>
                <!-- Botón para limpiar el carrito -->
                <button class="btn btn-secondary" onclick="limpiarCarrito()">Limpiar Carrito</button>

                <!-- Integración de PayPal -->
                <div id="paypal-button-container"></div>

            </div>
        </div>
    </div>
<?php
require 'footer.php';
?>
    <script>
        // Función para actualizar la cantidad de un producto
        function actualizarCantidad(id, accion) {
            $.ajax({
                url: 'acciones_carrito.php',
                type: 'POST',
                data: {
                    id: id,
                    accion: accion
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.status === 'success') {
                        // Actualizar la cantidad en la interfaz
                        const cantidadElement = document.getElementById(`cantidad-${id}`);
                        const cantidadActual = parseInt(cantidadElement.innerText);
                        if (accion === 'sumar') {
                            cantidadElement.innerText = cantidadActual + 1;
                        } else if (accion === 'restar') {
                            cantidadElement.innerText = cantidadActual - 1;
                        }
                        mostrarMensaje(data.message);
                        location.reload(); // Recargar la página para actualizar el total
                    } else {
                        mostrarMensaje(data.message, 'error');
                    }
                }
            });
        }

        // Función para eliminar un producto
        function eliminarProducto(id) {
            $.ajax({
                url: 'acciones_carrito.php',
                type: 'POST',
                data: {
                    id: id,
                    accion: 'eliminar'
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.status === 'success') {
                        document.getElementById(`producto-${id}`).remove();
                        mostrarMensaje(data.message);
                        location.reload(); // Recargar la página para actualizar el total
                    } else {
                        mostrarMensaje(data.message, 'error');
                    }
                }
            });
        }

        // Función para limpiar el carrito
        function limpiarCarrito() {
            $.ajax({
                url: 'acciones_carrito.php',
                type: 'POST',
                data: {accion: 'limpiar'},
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.status === 'success') {
                        location.reload(); // Recargar la página
                    } else {
                        mostrarMensaje(data.message, 'error');
                    }
                }
            });
        }

        // Función para mostrar mensajes de confirmación
        function mostrarMensaje(message, type = 'success') {
            const mensajeDiv = document.getElementById('mensaje');
            mensajeDiv.className = `alert alert-${type}`;
            mensajeDiv.innerText = message;
        }

        // Renderizar el botón de PayPal con el total del carrito
        function renderPayPalButton(total) {
            document.getElementById('paypal-button-container').innerHTML = ''; // Limpiar el contenedor antes de renderizar
            paypal.Buttons({
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: total // Total del carrito
                            }
                        }]
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(details) {
                        alert('Transacción completada por ' + details.payer.name.given_name);
                        // Aquí puedes redirigir al usuario o hacer algo más después de un pago exitoso
                    });
                }
            }).render('#paypal-button-container');
        }

        // Renderizar el botón de PayPal cuando la página se cargue
        window.onload = function() {
            renderPayPalButton('<?php echo $total; ?>'); // Pasar el total dinámico del carrito
        };
    </script>

</body>
</html>
