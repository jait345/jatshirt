<?php
require_once './api_printify.php';
session_start(); // Inicia sesión para acceder a los datos de la sesión

// Verifica si se está enviando la solicitud con el parámetro 'agregar'
if (isset($_POST['agregar'])) {
    // Obtener los datos del producto
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : null;
    $precio = isset($_POST['precio']) ? $_POST['precio'] : null;

    // Verifica si los datos necesarios están presentes
    if ($id && $nombre && $precio) {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = array();
        }

        if (isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id]['cantidad'] += 1; // Aumenta la cantidad si ya está en el carrito
        } else {
            $_SESSION['carrito'][$id] = array(
                'id' => $id,
                'nombre' => $nombre,
                'precio' => $precio,
                'cantidad' => 1
            );
        }

        echo json_encode(array(
            'status' => 'success',
            'message' => 'Producto agregado al carrito'
        ));
    } else {
        echo json_encode(array(
            'status' => 'error',
            'message' => 'Faltan datos del producto'
        ));
    }
}

// Sumar o restar productos
elseif (isset($_POST['accion']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $accion = $_POST['accion'];

    if (isset($_SESSION['carrito'][$id])) {
        if ($accion == 'sumar') {
            $_SESSION['carrito'][$id]['cantidad'] += 1;
            $message = 'Cantidad aumentada';
        } elseif ($accion == 'restar' && $_SESSION['carrito'][$id]['cantidad'] > 1) {
            $_SESSION['carrito'][$id]['cantidad'] -= 1;
            $message = 'Cantidad disminuida';
        } else {
            $message = 'No se puede restar más de uno';
        }

        echo json_encode(array(
            'status' => 'success',
            'message' => $message
        ));
    } else {
        echo json_encode(array(
            'status' => 'error',
            'message' => 'Producto no encontrado en el carrito'
        ));
    }
}

// Eliminar un producto
elseif (isset($_POST['eliminar']) && isset($_POST['id'])) {
    $id = $_POST['id'];

    if (isset($_SESSION['carrito'][$id])) {
        unset($_SESSION['carrito'][$id]);
        echo json_encode(array(
            'status' => 'success',
            'message' => 'Producto eliminado del carrito'
        ));
    } else {
        echo json_encode(array(
            'status' => 'error',
            'message' => 'Producto no encontrado en el carrito'
        ));
    }
}

// Limpiar el carrito
elseif (isset($_POST['limpiar'])) {
    unset($_SESSION['carrito']); // Elimina todos los productos del carrito
    echo json_encode(array(
        'status' => 'success',
        'message' => 'Carrito limpiado'
    ));
}

else {
    echo json_encode(array(
        'status' => 'error',
        'message' => 'Solicitud no válida'
    ));
}
?>
