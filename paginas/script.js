// Seleccionar los elementos necesarios
const loginLinks = document.querySelectorAll('.login-link'); // Cambiar a querySelectorAll para múltiples enlaces de 'login-link'
const registerLink = document.querySelector('.register-link');
const newpasswordLink = document.querySelector('.newpassword-link');
const bthPopup = document.querySelector('.bthLogin-popup');
const iconClose = document.querySelector('.icon-close');
const wrapper = document.querySelector('.wrapper'); // Selecciona el wrapper
const mainContent = document.querySelector('.main-content'); // Selecciona el contenido principal

// Función para ocultar todos los formularios
function hideAllForms() {
    const forms = wrapper.querySelectorAll('.form-box');
    forms.forEach(form => {
        form.style.display = 'none'; // Oculta todos los formularios
    });
}

// Mostrar formulario de inicio de sesión
loginLinks.forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault(); // Previene el comportamiento predeterminado del enlace
        hideAllForms(); // Oculta todos los formularios
        wrapper.querySelector('.form-box.login').style.display = 'block'; // Muestra el formulario de inicio de sesión
        wrapper.classList.remove('active'); // Si quieres remover la clase 'active' del registro
        console.log("Formulario de inicio de sesión mostrado");
    });
});

// Mostrar formulario de registro
registerLink.addEventListener('click', (e) => {
    e.preventDefault(); // Previene el comportamiento predeterminado del enlace
    hideAllForms(); // Oculta todos los formularios
    wrapper.querySelector('.form-box.register').style.display = 'block'; // Muestra el formulario de registro
    wrapper.classList.add('active'); // Si quieres agregar la clase 'active' al registro
    console.log("Formulario de registro mostrado");
});

// Mostrar formulario de nueva contraseña
newpasswordLink.addEventListener('click', (e) => {
    e.preventDefault(); // Previene el comportamiento predeterminado del enlace
    hideAllForms(); // Oculta todos los formularios
    wrapper.querySelector('.form-box.newpassword').style.display = 'block'; // Muestra el formulario de nueva contraseña
    console.log("Formulario de nueva contraseña mostrado");
});

// Evento para mostrar el formulario emergente
bthPopup.addEventListener('click', () => {
    wrapper.classList.add('active-popup'); // Muestra el formulario
    mainContent.classList.add('active'); // Desplaza el contenido principal
    hideAllForms(); // Oculta todos los formularios
    wrapper.querySelector('.form-box.login').style.display = 'block'; // Muestra el formulario de inicio de sesión por defecto
    console.log("Formulario emergente abierto y contenido desplazado");
});

// Cerrar el formulario
iconClose.addEventListener('click', () => {
    wrapper.classList.remove('active-popup'); // Oculta el formulario
    mainContent.classList.remove('active'); // Restaura el contenido principal
    hideAllForms(); // Oculta todos los formularios al cerrar
    console.log("Formulario cerrado y contenido restaurado");
});

// Inicializar mostrando el formulario de inicio de sesión al cargar
hideAllForms(); // Oculta todos los formularios al inicio
wrapper.querySelector('.form-box.login').style.display = 'block'; // Muestra el formulario de inicio de sesión al inicio





