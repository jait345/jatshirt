<?php
session_start();
// Verifica si ya ha iniciado sesión, y redirige al panel de administración si es así
if (isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_panel.php');
    exit;
}
?>