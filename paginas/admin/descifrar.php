<?php
// Configuración - CLAVE DEBE SER IDÉNTICA al archivo de cifrado
define('ENCRYPTION_KEY', 'miClaveSuperSecreta123!abcdefghi'); // 32 caracteres exactos

// Limpiar todos los buffers de salida
while (ob_get_level()) {
    ob_end_clean();
}

function decryptFile($filePath, $key) {
    // Verificar que el archivo existe y es legible
    if (!file_exists($filePath)) {
        throw new Exception("El archivo no existe o no se puede leer");
    }

    // Leer archivo completo en modo binario
    $encryptedData = file_get_contents($filePath);
    if ($encryptedData === false) {
        throw new Exception("Error al leer el archivo cifrado");
    }

    // Verificar tamaño mínimo (IV + al menos 1 bloque de cifrado)
    if (strlen($encryptedData) < 32) {
        throw new Exception("Archivo cifrado demasiado pequeño");
    }

    // Extraer IV (primeros 16 bytes)
    $iv = substr($encryptedData, 0, 16);
    $ciphertext = substr($encryptedData, 16);

    // Preparar clave (32 bytes exactos)
    $key = substr(hash('sha256', $key, true), 0, 32);

    // Descifrar
    $decrypted = openssl_decrypt($ciphertext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    if ($decrypted === false) {
        throw new Exception("Error al descifrar: " . openssl_error_string());
    }

    return $decrypted;
}

// Procesar archivo subido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_cifrado'])) {
    try {
        // Validar archivo subido
        if ($_FILES['archivo_cifrado']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Error al subir el archivo: " . $_FILES['archivo_cifrado']['error']);
        }

        // Descifrar archivo
        $decryptedData = decryptFile($_FILES['archivo_cifrado']['tmp_name'], ENCRYPTION_KEY);

        // Verificar si es JSON válido
        json_decode($decryptedData);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Los datos descifrados no son JSON válido");
        }

        // Forzar descarga del JSON descifrado
        header('Content-Description: File Transfer');
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="datos_descifrados_'.date('YmdHis').'.json"');
        header('Content-Length: ' . strlen($decryptedData));
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        
        echo $decryptedData;
        exit;

    } catch (Exception $e) {
        // Limpiar buffers antes de mostrar error
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        require './menuadmin/menu.php';
        
        // Información detallada del error
        $errorInfo = [
            'error' => $e->getMessage(),
            'archivo' => [
                'nombre' => $_FILES['archivo_cifrado']['name'],
                'tamaño' => $_FILES['archivo_cifrado']['size'],
                'tipo' => $_FILES['archivo_cifrado']['type']
            ]
        ];
        
        // Mostrar página de error
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error de Descifrado</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
            <div class="container py-5">
                <div class="alert alert-danger">
                    <h3 class="alert-heading">Error en el Descifrado</h3>
                    <p><strong>Motivo:</strong> <?= htmlspecialchars($e->getMessage()) ?></p>
                    
                    <h4 class="mt-4">Información del Archivo:</h4>
                    <ul>
                        <li>Nombre: <?= htmlspecialchars($errorInfo['archivo']['nombre']) ?></li>
                        <li>Tamaño: <?= htmlspecialchars($errorInfo['archivo']['tamaño']) ?> bytes</li>
                        <li>Tipo: <?= htmlspecialchars($errorInfo['archivo']['tipo']) ?></li>
                    </ul>
                    
                    <hr>
                    <h5>Recomendaciones:</h5>
                    <ol>
                        <li>Verifique que el archivo .enc fue generado por este sistema</li>
                        <li>Intente generar y descargar un nuevo archivo cifrado</li>
                        <li>Si el problema persiste, contacte al administrador</li>
                    </ol>
                </div>
                <a href="descifrar.php" class="btn btn-primary">Volver a intentar</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Mostrar formulario si no es POST
require './menuadmin/menu.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descifrar Archivos .enc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .upload-box {
            border: 2px dashed #dee2e6;
            border-radius: 5px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 1rem;
            background: #f8f9fa;
        }
        .upload-box:hover {
            background: #e9ecef;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h2 class="text-center mb-0"><i class="fas fa-lock-open me-2"></i>Descifrar Archivo .enc</h2>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data" id="decryptForm">
                            <div class="mb-4">
                                <label for="archivoInput" class="form-label fw-bold">Seleccione archivo cifrado:</label>
                                <div class="upload-box" onclick="document.getElementById('archivoInput').click()">
                                    <i class="fas fa-file-upload fa-3x mb-3"></i>
                                    <p id="fileName">Arrastre el archivo .enc aquí o haga clic para seleccionar</p>
                                </div>
                                <input type="file" class="form-control d-none" id="archivoInput" name="archivo_cifrado" accept=".enc" required>
                                <div class="form-text">Solo archivos .enc generados por este sistema</div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 py-3">
                                <i class="fas fa-key me-2"></i> Descifrar Archivo
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Font Awesome para iconos -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    
    <script>
        // Mostrar nombre de archivo seleccionado
        document.getElementById('archivoInput').addEventListener('change', function(e) {
            const fileName = this.files[0] ? this.files[0].name : 'Arrastre el archivo .enc aquí o haga clic para seleccionar';
            document.getElementById('fileName').textContent = fileName;
        });
        
        // Prevenir envío del formulario si no hay archivo
        document.getElementById('decryptForm').addEventListener('submit', function(e) {
            if (!document.getElementById('archivoInput').files.length) {
                e.preventDefault();
                alert('Por favor seleccione un archivo .enc');
            }
        });
    </script>
</body