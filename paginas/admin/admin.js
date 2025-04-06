// admin.js

// Obtener los elementos
const openLogin = document.getElementById('openLogin');
const loginModal = document.getElementById('loginModal');
const closeModal = document.getElementById('closeModal');

// Mostrar el modal al hacer clic en "Iniciar sesión (Admin)"
openLogin.addEventListener('click', (e) => {
    e.preventDefault();
    loginModal.style.display = 'flex'; // Flex para centrar
});

// Ocultar el modal al hacer clic en la "X"
closeModal.addEventListener('click', () => {
    loginModal.style.display = 'none';
});

// Ocultar el modal al hacer clic fuera del contenido
window.addEventListener('click', (e) => {
    if (e.target === loginModal) {
        loginModal.style.display = 'none';
    }
});



    // Elemento de contenido
    const content = document.getElementById('content');

    // Cargar y mostrar archivos según la categoría
    function loadContent(category) {
        fetch(`load_content.php?category=${category}`)
            .then(response => response.text())
            .then(data => {
                content.innerHTML = data; // Cargar el HTML retornado en el contenedor
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    // Asignar eventos de clic para los botones
    document.getElementById('showVideos').addEventListener('click', () => {
        loadContent('videos');
    });
    document.getElementById('showMusic').addEventListener('click', () => {
        loadContent('music');
    });
    document.getElementById('showImages').addEventListener('click', () => {
        loadContent('images');
    });
