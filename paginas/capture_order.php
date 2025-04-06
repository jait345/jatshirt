<?php
$clientId = "AYuW6bDyQD4_ZaWz4AXXrjYh2aYbCN1YkGY8xzW5ou_uYKC4tGSpUBqsjOVf7boKEXNvIVvXBX3veLi0"; // Reemplaza con tu Client ID
$clientSecret = "EDOzGUuoIu7VRSUIKGlxi54N16j1M935EPa0k32pec-I7Ld8vKXXHT5GweaLsDtby-WyZh-OtgZTCM_v"; // Reemplaza con tu Client Secret
$orderID = $_GET['orderID'];

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

// Capturar la orden
$capture = curl_init();
curl_setopt($capture, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v2/checkout/orders/$orderID/capture");
curl_setopt($capture, CURLOPT_POST, true);
curl_setopt($capture, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $accessToken"
]);
curl_setopt($capture, CURLOPT_RETURNTRANSFER, true);
$captureResponse = curl_exec($capture);
curl_close($capture);

header('Content-Type: application/json');
echo $captureResponse;
?>