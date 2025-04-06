const loginLink = document.querySelector('.login-link');
const registerLink = document.querySelector('.register-link');
const forgotPasswordLink = document.querySelector('.forgot-password-link'); // Nuevo enlace
const bthPopup = document.querySelector('.bthLogin-popup');
const iconClose = document.querySelector('.icon-close');

const wrapper = document.querySelector('.wrapper'); // Selecciona el wrapper
const mainContent = document.querySelector('.main-content'); // Selecciona el contenido principal

// Función para ocultar todos los formularios
function hideAllForms() {
    const forms = wrapper.querySelectorAll('.form-box');
    forms.forEach(form => {
        form.style.display = 'none'; // Oculta todos los formularios
        form.classList.remove('active'); // Remueve la clase activa
    });
}

// Eventos para el registro y login
registerLink.addEventListener('click', () => {
    hideAllForms(); // Oculta todos los formularios
    wrapper.classList.add('active'); // Activa el formulario de registro
});

loginLink.addEventListener('click', () => {
    hideAllForms(); // Oculta todos los formularios
    wrapper.classList.remove('active'); // Desactiva el formulario de registro
});

// Evento para mostrar el formulario emergente
bthPopup.addEventListener('click', () => {
    wrapper.classList.add('active-popup'); // Muestra el formulario
    mainContent.classList.add('active'); // Desplaza el contenido principal
    console.log("Formulario abierto y contenido desplazado");
});

// Cerrar el formulario
iconClose.addEventListener('click', () => {
    wrapper.classList.remove('active-popup'); // Oculta el formulario
    mainContent.classList.remove('active'); // Restaura el contenido principal
    hideAllForms(); // Oculta todos los formularios al cerrar
    console.log("Formulario cerrado y contenido restaurado");
});

// Evento para mostrar el formulario de nueva contraseña
forgotPasswordLink.addEventListener('click', (e) => {
    e.preventDefault(); // Prevenir el comportamiento predeterminado
    hideAllForms(); // Oculta todos los formularios
    wrapper.classList.add('active-new-password'); // Muestra el formulario de nueva contraseña
    console.log("Formulario de nueva contraseña abierto");
});