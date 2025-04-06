<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'];
    $targetDir = "uploads/" . $category . "/";
    
    // Crear la carpeta si no existe
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    // Guardar el archivo
    $targetFile = $targetDir . basename($_FILES["file"]["name"]);
    
    // Comprobar si el archivo ya existe
    if (file_exists($targetFile)) {
        echo "Lo siento, el archivo ya existe.";
        exit;
    }
    
    // Mover el archivo a la carpeta de destino
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
        echo "El archivo ". htmlspecialchars(basename($_FILES["file"]["name"])). " ha sido subido correctamente.";
    } else {
        echo "Lo siento, hubo un error al subir tu archivo.";
    }
}
?>