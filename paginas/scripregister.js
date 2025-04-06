document.getElementById("registerForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Evita el envío automático

    let recaptchaResponse = grecaptcha.getResponse();
    if (!recaptchaResponse) {
        alert("Por favor, completa el reCAPTCHA.");
        return;
    }

    let formData = new FormData(this);
    formData.append("g-recaptcha-response", recaptchaResponse);

    fetch("register.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => console.log(data))
    .catch(error => console.error("Error:", error));
});


//verificar que  o haya los mismos nombres en la bd
document.getElementById('fullName').addEventListener('input', function() {
    const fullName = this.value;
    const fullNameError = document.getElementById('fullNameError');

    if (fullName.length > 3) { // Verifica si el nombre completo tiene más de 3 caracteres
        fetch(`checkfullname.php?fullName=${encodeURIComponent(fullName)}`)
            .then(response => response.json())
            .then(data => {
                if (data.available) {
                    fullNameError.style.color = 'green';
                    fullNameError.textContent = '✔ Nombre completo disponible';
                } else {
                    fullNameError.style.color = 'red';
                    fullNameError.textContent = '✖ Nombre completo ya registrado';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                fullNameError.textContent = 'Error verificando el nombre completo';
            });
    } else {
        fullNameError.textContent = '';
    }
});