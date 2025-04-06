<?php
require './conexiondb/conexion.php'; // Asegura la conexión a la BD

header('Content-Type: application/json'); // Respuesta en formato JSON

try {
    // Obtener la conexión PDO
    $pdo = connectDB();

    // Obtener comentarios
    $stmt = $pdo->query("SELECT * FROM comentarios ORDER BY fecha DESC");
    if (!$stmt) {
        throw new Exception("Error en la consulta a la base de datos.");
    }

    $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($comentarios);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error en la consulta a la base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>