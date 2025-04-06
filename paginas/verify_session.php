<?php
session_start();
require './conexiondb/conexion.php';

// Check if user is logged in
if (isset($_SESSION['email'])) {
    echo json_encode(['valid' => true]);
} else {
    echo json_encode(['valid' => false]);
}

?>