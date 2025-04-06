<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: authenticate.php');
    exit;
}

define('ENCRYPTION_KEY', 'miClaveSuperSecreta123!abcdefghi');
define('ACCESS_KEY', 'claveAccesoAdmin456@');

// Procesar solicitud de descarga ANTES de cualquier output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['access_key'])) {
    require './conexion/conexion.php';
    require 'api_printify.php';
    
    function encryptData($data, $key) {
        $key = substr(hash('sha256', $key, true), 0, 32);
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return $iv . $encrypted; // Devuelve datos binarios directamente
    }

    if ($_POST['access_key'] !== ACCESS_KEY) {
        header('Content-Type: application/json');
        die(json_encode(['success' => false, 'message' => 'Clave de acceso incorrecta']));
    }

    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'descargar_productos':
                    $products = get_printify_products();
                    if (!$products) {
                        throw new Exception("No se pudieron obtener los productos de Printify");
                    }
                    
                    $jsonData = json_encode($products, JSON_PRETTY_PRINT);
                    $encryptedData = encryptData($jsonData, ENCRYPTION_KEY);
                    
                    // Forzar descarga como archivo binario
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="productos_printify_' . date('YmdHis') . '.bin"');
                    header('Content-Length: ' . strlen($encryptedData));
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    
                    echo $encryptedData;
                    exit;
                    
                case 'descargar_bd':
                    $conn = connectDB();
                    $tables = [];
                    $result = $conn->query("SHOW TABLES");
                    while ($row = $result->fetch(PDO::FETCH_NUM)) {
                        $tables[] = $row[0];
                    }
                
                    $sqlDump = "-- Backup completo de la base de datos\n";
                    $sqlDump .= "-- Generado el: " . date('Y-m-d H:i:s') . "\n\n";
                    
                    foreach ($tables as $table) {
                        $createTable = $conn->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
                        $sqlDump .= "-- Estructura de tabla para `$table`\n";
                        $sqlDump .= $createTable['Create Table'] . ";\n\n";
                        
                        $stmt = $conn->query("SELECT * FROM `$table`");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $values = array_map([$conn, 'quote'], $row);
                            $sqlDump .= "INSERT INTO `$table` (`" . implode("`, `", array_keys($row)) . "`) VALUES (" . implode(", ", $values) . ");\n";
                        }
                        $sqlDump .= "\n";
                    }
                    
                    $encryptedDb = encryptData($sqlDump, ENCRYPTION_KEY);
                    
                    // Forzar descarga como archivo binario
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="backup_bd_' . date('YmdHis') . '.bin"');
                    header('Content-Length: ' . strlen($encryptedDb));
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    
                    echo $encryptedDb;
                    exit;
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            die(json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]));
        }
    }
}
// Si no es POST o no hay acción, mostrar la página normal
require './menuadmin/menu.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Descarga Segura de Datos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .key-input { letter-spacing: 2px; }
        .card { max-width: 600px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h2 class="text-center mb-0">Descarga Segura de Datos</h2>
            </div>
            
            <div class="card-body">
                <div class="mb-4">
                    <label for="accessKey" class="form-label">Ingrese la clave de acceso:</label>
                    <div class="input-group">
                        <input type="password" id="accessKey" class="form-control key-input" placeholder="Clave de seguridad">
                        <button id="verifyKey" class="btn btn-primary">Verificar</button>
                    </div>
                    <div id="keyFeedback" class="mt-2"></div>
                </div>
                
                <div id="downloadSection" style="display: none;">
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <h5 class="card-title">Productos Printify</h5>
                            <p class="card-text">Descarga cifrada de todos los productos</p>
                            <button id="descargarProductos" class="btn btn-primary">
                                <i class="fas fa-download me-2"></i>Descargar
                            </button>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Base de Datos</h5>
                            <p class="card-text">Copia de seguridad cifrada completa</p>
                            <button id="descargarBD" class="btn btn-danger">
                                <i class="fas fa-database me-2"></i>Descargar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Configuración
        const ACCESS_KEY = "claveAccesoAdmin456@";
        let accessVerified = false;
        
        // Mostrar alertas
        function showAlert(message, type = "success") {
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show mt-3`;
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.card-body').prepend(alert);
            
            setTimeout(() => alert.remove(), 5000);
        }
        
        // Verificar clave de acceso
        document.getElementById('verifyKey').addEventListener('click', () => {
            const accessKey = document.getElementById('accessKey').value;
            
            if (!accessKey) {
                showAlert("Por favor ingrese la clave", "warning");
                return;
            }
            
            if (accessKey === ACCESS_KEY) {
                accessVerified = true;
                document.getElementById('downloadSection').style.display = 'block';
                document.getElementById('keyFeedback').innerHTML = 
                    '<span class="text-success"><i class="fas fa-check-circle"></i> Clave verificada</span>';
                showAlert("Clave verificada correctamente", "success");
            } else {
                showAlert("Clave incorrecta", "danger");
            }
        });
        
        // Función para manejar descargas
        async function handleDownload(action, fileName) {
            if (!accessVerified) {
                showAlert("Primero verifique la clave", "warning");
                return;
            }
            
            try {
                const accessKey = document.getElementById('accessKey').value;
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=${action}&access_key=${encodeURIComponent(accessKey)}`
                });
                
                if (!response.ok) {
                    const error = await response.text();
                    throw new Error(error || `Error HTTP: ${response.status}`);
                }
                
                // Obtener el blob de datos
                const blob = await response.blob();
                const url = URL.createObjectURL(blob);
                
                // Crear enlace de descarga
                const a = document.createElement('a');
                a.href = url;
                a.download = fileName || `descarga_${action}_${new Date().toISOString().slice(0,10)}.enc`;
                document.body.appendChild(a);
                a.click();
                
                // Limpiar
                setTimeout(() => {
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                }, 100);
                
                showAlert("Descarga iniciada correctamente");
            } catch (error) {
                console.error("Error en la descarga:", error);
                showAlert("Error al descargar: " + error.message, "danger");
            }
        }
        
        // Descargar productos
        document.getElementById('descargarProductos').addEventListener('click', () => {
            handleDownload('descargar_productos', 'productos_printify.enc');
        });
        
        // Descargar BD
        document.getElementById('descargarBD').addEventListener('click', () => {
            handleDownload('descargar_bd', 'backup_bd.enc');
        });
    </script>
</body>
</html>