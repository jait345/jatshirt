<?php
require './menupricipal/conexion/conexion.php'; // Cambia esto al path correcto

header('Content-Type: application/json'); // Establecer el tipo de contenido como JSON

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = isset($_POST['name']) ? trim($_POST['name']) : '';
    $correo = isset($_POST['email']) ? trim($_POST['email']) : '';
    $importancia = isset($_POST['importance']) ? trim($_POST['importance']) : '';
    $queja = isset($_POST['suggestion']) ? trim($_POST['suggestion']) : '';

    $errores = [];

    if (empty($nombre)) {
        $errores[] = "El nombre es obligatorio.";
    }
    if (empty($correo)) {
        $errores[] = "El correo es obligatorio.";
    }
    if (empty($importancia) || !in_array($importancia, ['bajo', 'medio', 'alto'])) {
        $errores[] = "El nivel de importancia es inválido.";
    }
    if (empty($queja)) {
        $errores[] = "La queja o sugerencia es obligatoria.";
    }

    if (!empty($errores)) {
        echo json_encode(['status' => 'error', 'message' => implode(", ", $errores)]);
    } else {
        $conexion = connectDB();
        $query = "INSERT INTO quejasysugerencias (nombre, correo, importancia, queja) VALUES (:nombre, :correo, :importancia, :queja)";

        try {
            $stmt = $conexion->prepare($query);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':importancia', $importancia);
            $stmt->bindParam(':queja', $queja);

            $stmt->execute();
            echo json_encode(['status' => 'success', 'message' => 'Queja/Sugerencia enviada con éxito.']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se recibieron datos del formulario.']);
}