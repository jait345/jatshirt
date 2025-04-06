<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Pago</title>
    <link rel="stylesheet" href="./menu/usuario.css">
    <script defer src="script.js"></script>
</head>
<body>
    <!-- Contenedor de la tarjeta animada -->
    <div class="card-container">
        <div class="credit-card visa" id="credit-card">
            <div class="card-number" id="card-number-display">•••• •••• •••• ••••</div>
            <div class="cardholder-name" id="cardholder-display">NOMBRE DEL TITULAR</div>
            <div class="expiry-date" id="expiry-display">MM/YY</div>
        </div>
    </div>

    <div class="payment-container">
        <h2>Formulario de Pago</h2>
        <form id="payment-form" action="procesar_pago.php" method="POST">
            <label for="cardholder">Nombre del Titular</label>
            <input type="text" id="cardholder" name="cardholder" required>

            <label for="card-type">Tipo de Tarjeta</label>
            <select id="card-type" name="card-type">
                <option value="visa">Visa</option>
                <option value="mastercard">MasterCard</option>
                <option value="amex">American Express</option>
            </select>

            <label for="card-number">Número de Tarjeta</label>
            <input type="text" id="card-number" name="card-number" pattern="\d{16}" required>

            <label for="expiry-date">Fecha de Vencimiento</label>
            <input type="month" id="expiry-date" name="expiry-date" required>

            <label for="cvv">Código de Seguridad</label>
            <input type="text" id="cvv" name="cvv" pattern="\d{3}" required>

            <button type="submit">Pagar</button>
        </form>
    </div>
</body>
</html>
