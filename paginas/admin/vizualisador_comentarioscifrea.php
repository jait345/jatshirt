<?php
session_start();
require './menuadmin/menu.php';
require './conexion/conexion.php'; // Asegura la conexión a la BD

define('AES_METHOD', 'AES-256-CBC');

header('Content-Type: text/html; charset=UTF-8');

function decryptAES($encryptedData, $key) {
    $data = base64_decode($encryptedData);
    $ivLength = openssl_cipher_iv_length(AES_METHOD);
    $iv = substr($data, 0, $ivLength);
    $encrypted = substr($data, $ivLength);
    return openssl_decrypt($encrypted, AES_METHOD, $key, 0, $iv);
}

try {
    $pdo = connectDB();
    
    // Verificar si se ha enviado la clave
    $providedKey = isset($_POST['aes_key']) ? trim($_POST['aes_key']) : '';
    $showDecrypted = !empty($providedKey);
    
    // Obtener todos los comentarios
    $stmt = $pdo->query("SELECT * FROM comentarios ORDER BY fecha DESC");
    $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizador de Comentarios Cifrados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .comment {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }
        .comment:last-child {
            border-bottom: none;
        }
        .comment-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.9em;
            color: #666;
        }
        .comment-content {
            margin-bottom: 10px;
        }
        .media {
            margin-top: 10px;
        }
        .media img, .media video {
            max-width: 100%;
            height: auto;
        }
        form {
            margin-bottom: 20px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #45a049;
        }
        .encrypted-warning {
            color: #ff5722;
            font-style: italic;
        }
        .anonymous {
            color: #9e9e9e;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Visualizador de Comentarios</h1>
        
        <form method="post">
            <label for="aes_key">Clave AES para descifrar comentarios:</label>
            <input type="password" id="aes_key" name="aes_key" placeholder="Ingresa la clave secreta">
            <button type="submit">Mostrar comentarios descifrados</button>
        </form>
        
        <?php if (!empty($comentarios)): ?>
            <h2>Lista de Comentarios</h2>
            
            <?php foreach ($comentarios as $comentario): ?>
                <div class="comment">
                    <div class="comment-header">
                        <span>
                            <?php if ($comentario['anonimo']): ?>
                                <span class="anonymous">Anónimo</span>
                            <?php else: ?>
                                <?= htmlspecialchars($comentario['email_usuario']) ?>
                            <?php endif; ?>
                        </span>
                        <span><?= htmlspecialchars($comentario['fecha']) ?></span>
                    </div>
                    
                    <div class="comment-content">
                        <?php if ($comentario['anonimo']): ?>
                            <?php if ($showDecrypted): ?>
                                <?php
                                try {
                                    $decrypted = decryptAES($comentario['comentario'], $providedKey);
                                    echo nl2br(htmlspecialchars($decrypted));
                                } catch (Exception $e) {
                                    echo '<span class="encrypted-warning">Error al descifrar: clave incorrecta</span>';
                                }
                                ?>
                            <?php else: ?>
                                <span class="encrypted-warning">[Contenido cifrado - Ingresa la clave para verlo]</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <?= nl2br(htmlspecialchars($comentario['comentario'])) ?>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($comentario['imagen']): ?>
                        <div class="media">
                            <img src="uploads/<?= htmlspecialchars($comentario['imagen']) ?>" alt="Imagen adjunta">
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($comentario['video']): ?>
                        <div class="media">
                            <video controls width="100%">
                                <source src="uploads/<?= htmlspecialchars($comentario['video']) ?>" type="video/mp4">
                                Tu navegador no soporta el elemento de video.
                            </video>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
        <?php else: ?>
            <p>No hay comentarios para mostrar.</p>
        <?php endif; ?>
    </div>

<?php
    require 'footer.php';
?>
</body>
</html>