<?php
// delete_comment.php
require './conexiondb/conexion.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];

    $stmt = $pdo->prepare("DELETE FROM comentarios WHERE id = ?");
    $stmt->execute([$id]);

    echo "Comentario eliminado con éxito";
}
?>