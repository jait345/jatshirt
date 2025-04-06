<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./menu/menu.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@100;300;400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Add JWT and MFA libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jwt-decode/3.1.2/jwt-decode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/otpauth/dist/otpauth.min.js"></script>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://www.google.com/recaptcha/enterprise.js?render=6LeRyN4qAAAAAE_-HkySrbhSJmNH_pbfyrRKW4oP"></script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <style>
        /* Estilos para la notificación flotante */
        .toast {
            display: none;
            position: fixed;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #eaf100;
            color: white;
            padding: 20px;
            border-radius: 5px;
            z-index: 9999; /* Asegura que la notificación esté sobre otros elementos */
        }
    </style>
</head>
<body>
<div class="wrapper">
    <span class="icon-close">
        <ion-icon name="close"></ion-icon>
    </span>

    <div class="form-box login">
        <h2>Iniciar sesión</h2>
        <form id="loginForm" action="login.php" method="POST">
            <div class="input-box">
                <span class="icon">
                    <ion-icon name="mail"></ion-icon>
                </span>
                <input type="email" name="email" id="email" required>
                <label>Correo electrónico</label>
            </div>
            <div class="input-box">
                <span class="icon">
                    <ion-icon name="lock-closed"></ion-icon>
                </span>
                <input type="password" name="password" id="password" required>
                <label>Contraseña</label>
            </div>

            <!-- Add MFA section -->
            <div class="input-box mfa-section" style="display: none;">
                <span class="icon">
                    <ion-icon name="shield-checkmark"></ion-icon>
                </span>
                <input type="text" name="mfa_code" id="mfa_code" maxlength="6" placeholder="Código de verificación">
                <label>Código de verificación</label>
                <small class="mfa-help">Se ha enviado un código a tu correo electrónico</small>
                <br>
                <br>
                <button type="button" id="validate-code-button" onclick="validateMFACode()">Validar código</button>
                <p id="verification-message" style="color: red;"></p>
            </div>

            <div class="remember-forgot">
                <label><input type="checkbox" name="remember"> Recordarme</label>
                <a href="newpassword.php" class="newpassword-link">¿Olvidaste la contraseña?</a>
            </div>
            <br>
            <br>
            <button type="submit" class="bth">Iniciar sesión</button>
            <div id="g_id_onload"
        data-client_id="1012880852310-oodrcuf6qe4oehir3124po0tugq2eu6s.apps.googleusercontent.com"
        data-cback="handleCredentialResponse">
    </div>
    <br>
    <div class="g_id_signin" data-type="standard"></div>
            <div class="login-register">
                <p>¿No tienes una cuenta? <a href="#" class="register-link">Registrarse</a></p>
            </div>
        </form>
    </div>
    <script>
        // Manejar el envío del formulario de inicio de sesión
        document.getElementById('login-form').addEventListener('submit', function (e) {
            e.preventDefault(); // Evitar el envío tradicional del formulario

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            // Enviar credenciales al servidor
            fetch('login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email: email, password: password }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar la sección de validación de código
                    document.getElementById('verification-section').style.display = 'block';
                } else {
                    alert(data.message); // Mostrar mensaje de error
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

        // Validar el código de verificación
        document.getElementById('validate-code-button').addEventListener('click', function () {
            const code = document.getElementById('mfa_code').value;

            fetch('login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ verification_code: code }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirigir al usuario a login.php
                    window.location.href = 'ilogin.php';
                } else {
                    // Mostrar mensaje de error
                    document.getElementById('verification-message').innerText = data.message;
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    </script>

        <script>
        function handleCredentialResponse(response) {
            // Enviar el token de Google al backend
            fetch('tu_script_php.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ credential: response.credential }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirigir al usuario si la autenticación fue exitosa
                    window.location.href = data.redirect;
                } else {
                    // Mostrar un mensaje de error
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>

<script>

        document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const email = document.querySelector('input[name="email"]').value;
    const password = document.querySelector('input[name="password"]').value;
    const mfaCode = document.querySelector('input[name="mfa_code"]').value;

    const formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);
    if (mfaCode) formData.append('mfa_code', mfaCode);

    fetch('login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.token) {
                // Store JWT token securely
                localStorage.setItem('jat_shirt_token', data.token);
                window.location.href = './usuariologin/configuracion.php';
            } else {
                // Show MFA section if token is not provided yet
                document.querySelector('.mfa-section').style.display = 'block';
            }
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error en la conexión');
    });
});
    </script>
<div class="form-box register">
    <h2>Registro</h2>
    <form id="registerForm">
        <div class="input-box">
            <span class="icon">
                <ion-icon name="person"></ion-icon>
            </span>
            <input type="text" id="fullName" name="fullName" required>
            <label>Nombre completo</label>
            <small class="error"></small>
            <small class="error" id="fullNameError"></small>
        </div>
        <div class="input-box">
            <span class="icon">
                <ion-icon name="mail"></ion-icon>
            </span>
            <input type="email" id="email" name="email" required>
            <label>Correo electrónico</label>
            <small class="error"></small>
        </div>
        <div class="input-box">
            <span class="icon">
                <ion-icon name="lock-closed"></ion-icon>
            </span>
            <input type="password" id="password" name="password" required>
            <label>Contraseña</label>
            <small class="error"></small>
        </div>
        <div class="input-box">
            <span class="icon">
                <ion-icon name="lock-closed"></ion-icon>
            </span>
            <input type="password" id="confirmPassword" name="confirmPassword" required>
            <label>Confirmar contraseña</label>
            <small class="error"></small>
        </div>
        <!-- Verificación Humana con Pregunta Simple -->
        <div class="input-box">
            <label>¿Cuánto es 5 + 3?</label>
            <input type="text" id="humanCheck" name="humanCheck" required>
            <small class="error"></small>
            <br>
            <br>
        </div>
        <!-- Google reCAPTCHA -->
        <!--<div class="g-recaptcha" data-sitekey="6LeRyN4qAAAAAE_-HkySrbhSJmNH_pbfyrRKW4oP"></div>-->
        <br>
        <div class="remember-forgot">
            <label><input type="checkbox" name="terms" required> Acepto los términos y condiciones</label>
        </div>
        <button type="submit" class="bth">Registrarse</button>
        <div class="login-register">
            <p>¿Ya tienes una cuenta? <a href="#" class="login-link">Iniciar sesión</a></p>
        </div>
    </form>
</div>

<script>
    document.getElementById("registerForm").addEventListener("input", function () {
        validarFormulario();
    });
    
    function validarFormulario() {
        const fullName = document.getElementById("fullName");
        const email = document.getElementById("email");
        const password = document.getElementById("password");
        const confirmPassword = document.getElementById("confirmPassword");
        
        validarCampo(fullName, fullName.value.trim() !== "", "El nombre completo es obligatorio");
        validarCampo(email, /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value), "Ingrese un correo válido");
        validarCampo(password, /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/.test(password.value), "La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número");
        validarCampo(confirmPassword, confirmPassword.value === password.value, "Las contraseñas no coinciden");
    }
    
    function validarCampo(input, condicion, mensajeError) {
        const errorElement = input.nextElementSibling;
        if (!condicion) {
            errorElement.textContent = mensajeError;
            input.classList.add("invalid");
        } else {
            errorElement.textContent = "";
            input.classList.remove("invalid");
        }
    }

    document.getElementById('registerForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Evita el envío tradicional del formulario

            // Envía el formulario con AJAX
            const formData = new FormData(this);
            formData.append('g-recaptcha-response', captchaResponse); // Asegúrate de incluir el token

            fetch('register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                if (data.status === "success") {
                    // Store session token in localStorage
                    if (data.token) {
                        localStorage.setItem('sessionToken', data.token);
                    }
                    // Redirect to configuration page
                    window.location.href = './usuariologin/configuracion.php';
                } else {
                    alert("Error: " + (data.message || data.messages.join(", ")));
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
</script>
<!-- <script>
function onClick(e) {
    e.preventDefault();
    grecaptcha.enterprise.ready(async () => {
    const token = await grecaptcha.enterprise.execute('6LeRyN4qAAAAAE_-HkySrbhSJmNH_pbfyrRKW4oP', {action: 'LOGIN'});
    });
}
</script> -->



    <div class="form-box newpassword">
        <h2>Nueva contraseña</h2>
        <form action="reset_password.php" method="POST">
            <div class="input-box">
                <span class="icon">
                    <ion-icon name="mail"></ion-icon>
                </span>
                <input type="email" name="email" required>
                <label>Correo electrónico</label>
            </div>
            <div class="input-box">
                <span class="icon">
                    <ion-icon name="lock-closed"></ion-icon>
                </span>
                <input type="password" name="new_password" required>
                <label>Nueva contraseña</label>
            </div>
            <div class="input-box">
                <span class="icon">
                    <ion-icon name="lock-closed"></ion-icon>
                </span>
                <input type="password" name="confirm_password" required>
                <label>Confirmar nueva contraseña</label>
            </div>
            <button type="submit" class="bth">Actualizar contraseña</button>
            <div class="login-register">
                <p>¿Quieres volver a iniciar sesión? <a href="#" class="login-link">Iniciar sesión</a></p>
            </div>
        </form>
    </div>
</div>
<script src="script.js"></script>
<script src="./scripregister.js"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script>
    // Check for existing session
    window.addEventListener('load', function() {
        const sessionToken = localStorage.getItem('sessionToken');
        if (sessionToken) {
            // Verify token and redirect if valid
            fetch('verify_session.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ token: sessionToken }),
                credentials: 'include'
            })
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    window.location.href = './usuariologin/configuracion.php';
                } else {
                    localStorage.removeItem('sessionToken');
                }
            });
        }
    });

    document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Evita el envío tradicional del formulario

    // Validar la pregunta humana
    const humanCheck = document.getElementById('humanCheck').value;
    if (humanCheck !== "8") {
        showToast("Respuesta incorrecta en la verificación humana.");
        return;
    }

    // Obtener los datos del formulario
    const formData = new FormData(this);

    // Enviar los datos al servidor
    fetch('register.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("Error en la conexión.");
        }
        return response.json();
    })
    .then(data => {
        if (data.status === "success") {
            showToast(data.message);
            // Redirigir o realizar otra acción
        } else {
            showToast("Error: " + (data.message || data.messages.join(", ")));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast("Error en la conexión.");
    });
});
</script>
</script>
<!-- api para iniciar sesion en google -->
<script src="https://accounts.google.com/gsi/client" async defer></script>
<script>
  function handleCredentialResponse(response) {
    console.log("Token de ID:", response.credential);
    // Enviar el token al backend para su validación
  }
</script>
<!-- Contenedor de la notificación -->
<div class="toast" id="toast"></div>

<script>
    // Función para mostrar la notificación
    function showToast(message) {
        var toast = document.getElementById("toast");
        toast.innerText = message;
        toast.style.display = "block";
        setTimeout(function() {
            toast.style.display = "none";
        }, 3000); // Oculta la notificación después de 3 segundos
    }

    // Manejar el envío del formulario con XMLHttpRequest
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Evita el envío tradicional del formulario

        var form = document.getElementById('registerForm');
        var formData = new FormData(form); // Obtiene los datos del formulario

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'register.php', true); // Enviar a registro.php

        xhr.onload = function() {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                // Mostrar el mensaje de la respuesta
                showToast(response.message);
            } else {
                showToast('Error en la solicitud');
            }
        };

        xhr.onerror = function() {
            showToast('Error en la conexión');
        };

        // Enviar los datos del formulario
        xhr.send(formData);
    });
</script>

<script>
    function validateMFACode() {
        console.log("Función validateMFACode ejecutada"); // Depuración

        // Obtener los valores de los campos
        const email = document.getElementById('email').value; // Obtener el email
        const password = document.getElementById('password').value; // Obtener la contraseña
        const code = document.getElementById('mfa_code').value; // Obtener el código MFA

        console.log("Email ingresado:", email); // Depuración
        console.log("Contraseña ingresada:", password); // Depuración
        console.log("Código ingresado:", code); // Depuración

        // Validar que los campos no estén vacíos
        if (!email || !password || !code) {
            console.log("Uno o más campos están vacíos"); // Depuración
            document.getElementById('verification-message').innerText = 'Por favor, completa todos los campos.';
            document.getElementById('verification-message').style.color = 'red';
            return;
        }

        console.log("Enviando datos al servidor..."); // Depuración
        fetch('login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json', // Asegúrate de que el encabezado sea correcto
            },
            body: JSON.stringify({
                email: email, // Enviar el email
                password: password, // Enviar la contraseña
                mfa_code: code // Enviar el código MFA
            }),
        })
        .then(response => {
            console.log("Respuesta del servidor recibida"); // Depuración
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json(); // Convertir la respuesta a JSON
            } else {
                return response.text().then(text => {
                    throw new Error(`Respuesta no es un JSON válido: ${text}`);
                });
            }
        })
        .then(data => {
            console.log("Datos recibidos del servidor:", data); // Depuración
            if (data.success) {
                document.getElementById('verification-message').innerText = 'Código válido. Redirigiendo...';
                document.getElementById('verification-message').style.color = 'green';
                setTimeout(() => {
                    window.location.href = './usuariologin/ilogin.php';
                }, 2000);
            } else {
                document.getElementById('verification-message').innerText = data.message || 'Código inválido';
                document.getElementById('verification-message').style.color = 'red';
            }
        })
        .catch(error => {
            console.error('Error:', error); // Depuración
            document.getElementById('verification-message').innerText = 'Error en la conexión. Inténtalo de nuevo.';
            document.getElementById('verification-message').style.color = 'red';
        });
    }
</script>
<script>
    document.getElementById('validate-code-button').addEventListener('click', validateMFACode);
</script>
</body>
</html>



