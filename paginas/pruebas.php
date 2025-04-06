<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

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
</script>

<style>
    .input-box .error {
    color: red;
    font-size: 12px;
    position: absolute;
    bottom: -18px;
    left: 5px;
}
.invalid {
    border: 1px solid red;
}
</style>
    
</body>
</html>