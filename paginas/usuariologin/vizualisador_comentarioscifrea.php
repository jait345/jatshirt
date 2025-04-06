<?php
// Iniciar buffer de salida al principio del script
ob_start();
session_start();
header('Content-Type: text/html; charset=UTF-8');

require '../admin/menuadmin/menu.php';
require './conexiondb/conexion.php'; // Asegura la conexión a la BD

define('AES_METHOD', 'AES-256-CBC');

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
    
    // Procesar eliminación de comentario si se ha enviado
    if (isset($_POST['delete_comment']) && isset($_POST['comment_id'])) {
        $commentId = $_POST['comment_id'];
        $stmt = $pdo->prepare("DELETE FROM comentarios WHERE id = ?");
        $stmt->execute([$commentId]);
        
        // Limpiar buffer y redireccionar
        ob_end_clean();
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
    
    // Obtener todos los comentarios
    $stmt = $pdo->query("SELECT * FROM comentarios ORDER BY fecha DESC");
    $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    // Limpiar buffer antes de mostrar error
    ob_end_clean();
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
            padding: 20px;
            background-color: #f5f5f5;
        }

        .comment-container {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 10px;
        }
        
        .comment-box {
            flex: 1 1 300px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: relative;
        }
        
        .comment-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.9em;
            color: #666;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }
        
        .comment-content {
            margin-bottom: 15px;
            white-space: pre-line;
            min-height: 60px;
        }
        
        .media {
            margin-top: 10px;
        }
        
        .media img, .media video {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }
        
        .delete-form {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        
        .delete-btn {
            background: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .delete-btn:hover {
            background: #d32f2f;
        }
        
        .key-form {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .key-form input[type="password"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 300px;
            max-width: 100%;
        }
        
        .key-form button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .key-form button:hover {
            background: #45a049;
        }
        
        .encrypted-warning {
            color: #ff5722;
            font-style: italic;
        }
        
        .anonymous {
            color: #9e9e9e;
        }
        
        h1 {
            color: #333;
            margin-top: 0;
        }
        
        .no-comments {
            text-align: center;
            padding: 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Visualizador de Comentarios</h1>
        
        <form method="post" class="key-form">
            <label for="aes_key">Clave AES para descifrar comentarios:</label>
            <input type="password" id="aes_key" name="aes_key" placeholder="Ingresa la clave secreta">
            <button type="submit">Mostrar comentarios descifrados</button>
        </form>
        
        <?php if (!empty($comentarios)): ?>
            <h2>Lista de Comentarios</h2>
            <div class="comment-container">
                <?php foreach ($comentarios as $comentario): ?>
                    <div class="comment-box">
                        <form method="post" class="delete-form" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este comentario?');">
                            <input type="hidden" name="comment_id" value="<?= $comentario['id'] ?>">
                            <button type="submit" name="delete_comment" class="delete-btn">Eliminar</button>
                        </form>
                        
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
            </div>
            
        <?php else: ?>
            <p class="no-comments">No hay comentarios para mostrar.</p>
        <?php endif; ?>
    </div>
<?php
    require 'footer.php';
?>
</body>
</html>