<?php
$clientId = "AYuW6bDyQD4_ZaWz4AXXrjYh2aYbCN1YkGY8xzW5ou_uYKC4tGSpUBqsjOVf7boKEXNvIVvXBX3veLi0"; // Reemplaza con tu Client ID
$clientSecret = "EDOzGUuoIu7VRSUIKGlxi54N16j1M935EPa0k32pec-I7Ld8vKXXHT5GweaLsDtby-WyZh-OtgZTCM_v"; // Reemplaza con tu Client Secret
// Autenticación con PayPal
$auth = curl_init();
curl_setopt($auth, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v1/oauth2/token");
curl_setopt($auth, CURLOPT_POST, true);
curl_setopt($auth, CURLOPT_USERPWD, "$clientId:$clientSecret");
curl_setopt($auth, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
curl_setopt($auth, CURLOPT_RETURNTRANSFER, true);
$authResponse = curl_exec($auth);
curl_close($auth);

$authData = json_decode($authResponse, true);
$accessToken = $authData['access_token'];

// Crear la orden
$order = curl_init();
curl_setopt($order, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v2/checkout/orders");
curl_setopt($order, CURLOPT_POST, true);
curl_setopt($order, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $accessToken"
]);
curl_setopt($order, CURLOPT_POSTFIELDS, json_encode([
    "intent" => "CAPTURE",
    "purchase_units" => [
        [
            "amount" => [
                "currency_code" => "USD",
                "value" => "100.00" // Monto del pago
            ]
        ]
    ]
]));
curl_setopt($order, CURLOPT_RETURNTRANSFER, true);
$orderResponse = curl_exec($order);
curl_close($order);

header('Content-Type: application/json');
echo $orderResponse;
?>