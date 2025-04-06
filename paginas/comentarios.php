<?php
require './menu/menu.php';
require 'ids_r_o.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat en Tiempo Real</title>
    <link rel="stylesheet" href="./css/comentarios.css">
    <style>
        /* Estilos para el contenedor de comentarios */
        .comment-section {
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin-top: 20px;
            transition: background-color 0.3s, box-shadow 0.3s, transform 0.3s;
        }

        /* Efectos al pasar el mouse sobre el contenedor de comentarios */
        .comment-section:hover {
            background-color: #e0f7ff; /* Cambia el color de fondo */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2); /* Agrega sombra */
            transform: scale(1.02); /* Aumenta ligeramente el tamaño */
        }

        /* Estilos para cada comentario individual */
        .comment {
            padding: 10px;
            margin-bottom: 10px;
            background-color: #fff;
            border: 1px solid #eee;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <!-- Contenedor de comentarios -->
    <div class="comment-section" id="comment-section">
        <h3>Inicia sesion o registrate para hacer tu comentario
            cada comentario sea bueno o malo me ayuda a seguir mejorando
            en la pagina, gracias por tu tiempo
        </h3>
        <h1>
            Comentarios:</h1>
        <br>
    </div>
    

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const textarea = document.getElementById('comment');
            const progressBar = document.getElementById('progress-bar');
            const charCount = document.getElementById('char-count');
            const errorMsg = document.getElementById('error-msg');
            const submitButton = document.getElementById('submit-comment');
            const commentSection = document.getElementById('comment-section');
            const imagenInput = document.getElementById('imagen');
            const videoInput = document.getElementById('video');
            const maxLength = 200;

            // Contador de caracteres
            if (textarea) {
                textarea.addEventListener('input', () => {
                    const length = textarea.value.length;
                    const remaining = maxLength - length;
                    charCount.textContent = `${remaining} caracteres restantes`;
                    progressBar.style.width = `${(remaining / maxLength) * 100}%`;
                    progressBar.style.background = remaining >= 0 ? 'green' : 'red';
                    errorMsg.style.display = remaining < 0 ? 'block' : 'none';
                });
            }

            // Función para cargar comentarios
            function loadComments() {
        fetch('get_comments.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                data.forEach(comment => {
                    const commentElement = document.createElement('div');
                    commentElement.classList.add('comment');
                    commentElement.innerHTML = `
                        <p><strong>${comment.email_usuario}</strong>: ${comment.comentario}</p>
                        ${comment.imagen ? `<img src="uploads/${comment.imagen}" width="100" loading="lazy">` : ''}
                        ${comment.video ? `<video src="uploads/${comment.video}" width="100" controls></video>` : ''}
                        <span>${new Date(comment.fecha).toLocaleString()}</span>
                    `;
                    commentSection.appendChild(commentElement);
                });
            })
            .catch(error => {
                console.error("Error cargando comentarios:", error);
                commentSection.innerHTML = '<h3>Error cargando comentarios. Inténtalo de nuevo.</h3>';
            });
    }

            // Recargar comentarios cada 5000 segundos
            setInterval(loadComments, 5000000);
            loadComments(); // Cargar comentarios al inicio
});
    </script>


<?php
require 'footer.php';
?>
</body>
</html>