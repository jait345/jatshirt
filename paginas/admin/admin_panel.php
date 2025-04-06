<?php
session_start();
require './menuadmin/menu.php'; // Asegura la conexión a la BD

// Verificar si el administrador ha iniciado sesión
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: authenticate.php');
    exit;
}

// API Key y Shop ID de Printify
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
    curl_close($ch);

    return json_decode($response, true);
}

$products = get_printify_products();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador - Productos Printify</title>
    <link rel="stylesheet" href="./admin.panel.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@100;300;400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <style>
        .toast-success {
    background-color: #28a745 !important;
}
.toast-error {
    background-color: #dc3545 !important;
}
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between">
            <h3>Productos Disponibles</h3>
        </div>
        <div class="row mt-3">
            <?php if ($products && isset($products['data'])): ?>
                <?php foreach ($products['data'] as $product): ?>
                    <div class="col-md-2">
                        <div class="card mb-4">
                            <?php
                            $image_src = !empty($product['images'][0]['src']) ? $product['images'][0]['src'] : "https://via.placeholder.com/150";
                            ?>
                            <img src="<?= htmlspecialchars($image_src) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['title']) ?>" style="width: 150px; height: auto; margin: auto; display: block;">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= htmlspecialchars($product['title']) ?></h5>
                                <p class="card-text">ID: <?= htmlspecialchars($product['id']) ?></p>
                                <p class="card-text">Precio: $<?= number_format($product['variants'][0]['price'] / 100, 2) ?> USD</p>
                                <p class="card-text">
                                    Comprado: <?= isset($product['orders_count']) ? htmlspecialchars($product['orders_count']) : '0' ?> veces
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">No se encontraron productos.</p>
            <?php endif; ?>
        </div>
    </div>






<!-- Al final del <body> -->
<script>
    <?php if (isset($_SESSION['toast'])): ?>
        toastr.<?= $_SESSION['toast']['type'] ?>('<?= $_SESSION['toast']['message'] ?>');
        <?php unset($_SESSION['toast']); ?>
    <?php endif; ?>
</script>
</body>
</html>