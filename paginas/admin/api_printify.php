<?php
// api_printify.php

$api_key = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzN2Q0YmQzMDM1ZmUxMWU5YTgwM2FiN2VlYjNjY2M5NyIsImp0aSI6ImZlYmRkYTk0YmJhMjA1NTE0ZDljODdmZGQ4YTI5YjNmZDNjMTE5MGY5NjM0ZGNmMDA2ZWNhM2FhNTc5NmI0MDJjYjFmNGM3OThmM2UyOWM1IiwiaWF0IjoxNzQwMjAwNDY0LjEyMzYxMiwibmJmIjoxNzQwMjAwNDY0LjEyMzYxNiwiZXhwIjoxNzcxNzM2NDY0LjExNTQ0MSwic3ViIjoiMjIwNzE4NTYiLCJzY29wZXMiOlsic2hvcHMubWFuYWdlIiwic2hvcHMucmVhZCIsImNhdGFsb2cucmVhZCIsIm9yZGVycy5yZWFkIiwib3JkZXJzLndyaXRlIiwicHJvZHVjdHMucmVhZCIsInByb2R1Y3RzLndyaXRlIiwid2ViaG9va3MucmVhZCIsIndlYmhvb2tzLndyaXRlIiwidXBsb2Fkcy5yZWFkIiwidXBsb2Fkcy53cml0ZSIsInByaW50X3Byb3ZpZGVycy5yZWFkIiwidXNlci5pbmZvIl19.AoGz6XwwHR7rpRnMuhqpQ5D475t9sd4GYZYLa10QK6F65ezVJGiuld87XMA2XnRrq_LUpmEw3rhUDAghfe4'; // Reemplaza con tu API Key de Printify
$shop_id = '20893289'; // Usa el shop_id que obtuviste

function get_printify_products() {
    global $api_key, $shop_id;
    $url = "https://api.printify.com/v1/shops/$shop_id/products.json";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $api_key,
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Validar si la solicitud fue exitosa (código 200)
    if ($http_code !== 200) {
        return null; // Retornar null si hay un error
    }

    return json_decode($response, true);
}

$products = get_printify_products();

// Si quieres mostrar los productos en la página, puedes hacer esto:
if ($products) {
    
} else {
    echo "<p>Error al obtener productos de Printify.</p>";
}
?>
