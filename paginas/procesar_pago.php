<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PayPal Integration</title>
</head>
<body>
    <div id="paypal-button-container"></div>
    <p id="result-message"></p>

    <!-- PayPal JS SDK -->
    <script src="https://www.paypal.com/sdk/js?client-id=AYuW6bDyQD4_ZaWz4AXXrjYh2aYbCN1YkGY8xzW5ou_uYKC4tGSpUBqsjOVf7boKEXNvIVvXBX3veLi0&currency=USD"></script>
    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return fetch("create_order.php", {
                    method: "POST",
                })
                .then((response) => response.json())
                .then((order) => order.id);
            },
            onApprove: function(data, actions) {
                return fetch(`capture_order.php?orderID=${data.orderID}`, {
                    method: "POST",
                })
                .then((response) => response.json())
                .then((details) => {
                    alert("Transaction completed by " + details.payer.name.given_name);
                });
            },
        }).render("#paypal-button-container");
    </script>
</body>
</html>