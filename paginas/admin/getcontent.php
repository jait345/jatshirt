<?php
// getcontent.php

// Verifica si se recibió la categoría
if (isset($_GET['category'])) {
    $category = $_GET['category'];
    
    // Define la ruta según la categoría usando la raíz del servidor
    $directory = $_SERVER['DOCUMENT_ROOT'] . "/YourMultimedia2.0/Principal/priinicio/Admin/uploads/$category/";

    // Verifica si el directorio existe
    if (!is_dir($directory)) {
        echo '<p>Directorio no encontrado.</p>';
        exit;
    }

    switch ($category) {
        case 'videos':
            echo '<h2>Videos</h2>';
            echo '<p>Aquí aparecerán los videos relacionados.</p>';
            // Obtiene todos los archivos de video en la carpeta
            foreach (glob($directory . "*.mp4") as $video) {
                $video_url = "/YourMultimedia2.0/Principal/priinicio/Admin/uploads/videos/" . basename($video);
                echo '<div class="video-container">';
                echo '<video controls class="video">';
                echo '<source src="' . $video_url . '" type="video/mp4">';
                echo 'Tu navegador no soporta el elemento de video.';
                echo '</video>';
                echo '<p class="video-title">' . basename($video) . '</p>'; // Muestra el nombre del archivo
                echo '</div>';
            }
            break;

        case 'music':
            echo '<h2>Música</h2>';
            echo '<p>Aquí aparecerán las listas de música.</p>';
            // Obtiene todos los archivos de audio en la carpeta
            foreach (glob($directory . "*.mp3") as $music) {
                $music_url = "/YourMultimedia2.0/Principal/priinicio/Admin/uploads/music/" . basename($music);
                echo '<div class="music-container">';
                echo '<audio controls>';
                echo '<source src="' . $music_url . '" type="audio/mpeg">';
                echo 'Tu navegador no soporta el elemento de audio.';
                echo '</audio>';
                echo '<p class="music-title">' . basename($music) . '</p>'; // Muestra el nombre del archivo
                echo '</div>';
            }
            break;

        case 'images':
            echo '<h2>Imágenes</h2>';
            echo '<p>Aquí aparecerán las imágenes relacionadas.</p>';
            // Obtiene todos los archivos de imagen en la carpeta
            foreach (glob($directory . "*.{jpg,png,gif}", GLOB_BRACE) as $image) {
                $image_url = "/YourMultimedia2.0/Principal/priinicio/Admin/uploads/images/" . basename($image);
                echo '<div class="image-container">';
                echo '<img src="' . $image_url . '" class="media" alt="Ejemplo de imagen">';
                echo '</a>';
                echo '<p>' . basename($image) . '</p>'; // Muestra el nombre del archivo
                echo '</div>';
            }
            break;

        default:
            echo '<p>No hay contenido disponible para esta categoría.</p>';
            break;
    }
} else {
    echo '<p>No se recibió ninguna categoría.</p>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h2 {
            color: #333;
        }

        .video-container {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            margin: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 320px; /* Ancho fijo para las tarjetas de video */
            transition: box-shadow 0.3s; /* Sombra al pasar el mouse */
            float: left; /* Alineación en varias columnas */
        }

        .video-container:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Sombra en hover */
        }

        .video {
            width: 300px; /* Ancho fijo para los videos */
            height: 200px; /* Alto fijo para los videos */
        }

        .video-title {
            margin: 10px 0;
            font-weight: bold;
            text-align: center; /* Centra el título del video */
        }

        .music-container {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            margin: 10px;
            display: inline-block;
            text-align: center;
            width: 320px; /* Ancho fijo para las tarjetas de música e imágenes */
            transition: box-shadow 0.3s; /* Sombra al pasar el mouse */
            float: left; /* Alineación en varias columnas */
        }

        .music-container:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Sombra en hover */
        }

        

        .music-title, .image-container p {
            margin: 10px 0;
            font-weight: bold;
        }

        .image-container {
            display: inline-block;
            margin: 10px;
            position: relative;
            text-align: center;
        }
        
        .media {
            width: 150px; /* Ancho de la imagen */
            height: auto; /* Mantener la relación de aspecto */
            transition: transform 0.2s; /* Efecto de zoom al pasar el mouse */
        }

        .media:hover {
            transform: scale(1.1); /* Escala la imagen */
        }

        .image-container a {
            display: block;
            text-decoration: none;
            color: black; /* Color del texto para el enlace */
        }

        .image-container a:hover .media {
            opacity: 0.7; /* Opacidad cuando se pasa el mouse */
        }

        .image-container p {
            margin: 0;
        }
    </style>
</head>
<body>
    
</body>
</html>
