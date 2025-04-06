<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        html, body {
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
}

.content {
    flex: 1; /* Esto hace que el contenido ocupe el espacio restante */
}

footer {
    width: 100%;
    background-color: #001b29;
}
    </style>
</head>
<body>
<div class="content">


</div>

    <!-- Footer -->
<footer class="text-white" style="background-color: #001b29;">
        <br>
        <div class="container text-center text-md-left">
            <div class="row">
                <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 font-weight-bold text-warning">Nombre de la Empresa</h5>
                    <p>JAIT Empresa creadora de páginas y desarrollo de Aplicaciones</p>
                    <h5 class="text-uppercase mb-4 font-weight-bold text-warning">Contactos</h5>
                    <p>
                        <a href="mailto:info@empresa.com" class="text-white" style="text-decoration: none;">Correo:
                            jesusibarra3114@gmail.com</a>
                    </p>
                    <p>
                        <a href="tel:+52 449-000-0000" class="text-white" style="text-decoration: none;">Tel: +52
                            449-000-0000</a>
                    </p>
                </div>
                <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 font-weight-bold text-warning">Sígueme en mis redes</h5>
                    <a href="#" class="btn btn-outline-light btn-floating m-1">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="btn btn-outline-light btn-floating m-1">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="btn btn-outline-light btn-floating m-1">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.youtube.com/@colorsreds518" class="btn btn-outline-light btn-floating m-1">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <h5 class="text-uppercase mt-4 font-weight-bold text-warning">Hecho con</h5>
                    PHP<br>
                    JavaScript<br>
                    Bootstrap<br>
                    MySQL<br>
                    <p>
                        <i></i> por <strong>
                            <h7 class="text-uppercase mb-4 font-weight-bold text-info">Jesus Adrian Ibarra Tiscareño
                            </h7>
                        </strong>
                    </p>
                </div>
                <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 font-weight-bold text-warning">Quejas y Sugerencias</h5>
                    <form>
                        <div class="form-group">
                            <label for="nombreQuejas"></label>
                            <input type="text" class="form-control" id="nombreQuejas" placeholder="Tu nombre">
                        </div>
                        <div class="form-group">
                            <label for="emailQuejas"></label>
                            <input type="email" class="form-control" id="emailQuejas" placeholder="Tu correo">
                        </div>
                        <div class="form-group">
                            <label for="mensajeQuejas"></label>
                            <textarea class="form-control" id="mensajeQuejas" rows="3"
                                placeholder="Escribe tu mensaje"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="imagenQuejas"></label>
                            <input type="file" class="form-control" id="imagenQuejas" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-warning text-dark">Enviar</button>
                    </form>
                </div>
            </div>
            <hr class="mb-4">
            <div class="row align-items-center justify-content-center">
                <div class="col-md-12 text-center">
                    <p>© 2025wqw Derechos Reservados:
                        <a href="#" style="text-decoration: none;">
                            <strong class="text-warning">JAT-SHIRT</strong>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>