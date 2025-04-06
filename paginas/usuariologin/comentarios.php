<?php
session_start();
require './menu.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat en Tiempo Real</title>
    <link rel="stylesheet" href="./comentarios.css">
    <style>
        .comment-section { padding: 20px; background-color: #f9f9f9; border-radius: 10px; margin-top: 20px; }
        .comment { padding: 10px; background-color: #fff; border-radius: 5px; }
        .error { color: red; display: none; }
        .progress-container { width: 100%; height: 5px; background: #ddd; }
        .progress-bar { width: 100%; height: 100%; background: green; transition: width 0.3s; }
    </style>
</head>
<body>

<?php if (isset($_SESSION['email'])): ?>
    <div style="text-align: right; padding: 10px;">
        <i class="fas fa-user"></i> <span>Usuario: <?= htmlspecialchars($_SESSION['email']); ?></span>
    </div>
<?php endif; ?>

<h2>Deja tu comentario</h2>
<form id="comment-form" enctype="multipart/form-data">
    <textarea id="comment" name="comentario" placeholder="Escribe aquí..." maxlength="200"></textarea>
    <div class="progress-container">
        <div id="progress-bar" class="progress-bar"></div>
    </div>
    <p id="char-count">200 caracteres restantes</p>
    <p id="error-msg" class="error">¡Has excedido el límite de caracteres!</p>
    <input type="file" id="imagen" name="imagen" accept="image/*">
    <input type="file" id="video" name="video" accept="video/*">
    <label><input type="checkbox" id="anonimo" name="anonimo"> Enviar anónimamente</label>
    <button type="submit">Enviar</button>
</form>

<div class="comment-section" id="comment-section">
    <h3>Comentarios</h3>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('comment-form');
    const textarea = document.getElementById('comment');
    const progressBar = document.getElementById('progress-bar');
    const charCount = document.getElementById('char-count');
    const errorMsg = document.getElementById('error-msg');
    const commentSection = document.getElementById('comment-section');
    const maxLength = 200;

    textarea.addEventListener('input', () => {
        const length = textarea.value.length;
        const remaining = maxLength - length;
        charCount.textContent = `${remaining} caracteres restantes`;
        progressBar.style.width = `${(remaining / maxLength) * 100}%`;
        progressBar.style.background = remaining >= 0 ? 'green' : 'red';
        errorMsg.style.display = remaining < 0 ? 'block' : 'none';
    });

    function loadComments() {
        fetch('get_comments.php')
            .then(response => response.json())
            .then(data => {
                commentSection.innerHTML = '<h3>Comentarios</h3>';
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

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        if (textarea.value.trim() === "" || textarea.value.length > maxLength) return;

        const formData = new FormData(form);
        fetch('submit_comment.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                loadComments();
                form.reset();
                progressBar.style.width = "100%";
                progressBar.style.background = 'green';
                errorMsg.style.display = 'none';
                charCount.textContent = `${maxLength} caracteres restantes`;
            } else {
                console.error("Error en la respuesta del servidor:", data.message);
                alert("Error al enviar el comentario: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error al enviar el comentario:", error);
            alert("Error al enviar el comentario. Inténtalo de nuevo.");
        });
    });

    setInterval(loadComments, 5000);
    loadComments();
});
</script>

<?php require 'footer.php'; ?>
</body>
</html>
