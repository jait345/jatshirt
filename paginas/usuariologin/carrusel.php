<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Ropa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 80px;
        }
        
        /* Logo */
        .logo {
            position: fixed;
            top: 20px;
            left: 10%;
            width: 60px;
            height: 60px;
            z-index: 1000;
            transition: transform 0.3s ease;
        }
        
        .logo:hover {
            transform: rotate(15deg) scale(1.1);
        }
        
        /* Título */
        .page-title {
            text-align: center;
            margin-bottom: 30px;
            font-family: 'Arial', sans-serif;
            color: #333;
        }
        
        /* Contenedor principal */
        .main-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 10px;
        }
        
        /* Carrusel */
        .carousel-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto 10px;
        }
        
        .carousel, .carousel-inner, .carousel-item {
            height: 400px;
            width: 600px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* Galería */
        .gallery-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .gallery-item {
            width: 100%;
            height: 150px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .gallery-item:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 10;
        }
        
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* Elementos más grandes */
        .gallery-item.large {
            grid-column: span 2;
            height: 180px;
        }
        
        /* Elementos más pequeños */
        .gallery-item.small {
            height: 120px;
        }
        
        /* Responsive */
        @media (min-width: 768px) {
            .main-container {
                flex-direction: row;
                align-items: flex-start;
                justify-content: center;
                flex-wrap: wrap;
                gap: 30px;
            }
            
            .carousel-container {
                margin: 0;
                flex: 1;
                min-width: 400px;
                max-width: 500px;
            }
            
            .gallery-container {
                flex: 2;
                min-width: 300px;
                max-width: 600px;
            }
        }
        
        @media (max-width: 480px) {
            .gallery-container {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .gallery-item.large {
                grid-column: span 2;
            }
            
            .carousel, .carousel-inner, .carousel-item {
                height: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="logo">
        <img src="./IMG/logojatshirts.png" alt="Logo" class="img-fluid">
    </div>

    <h1 class="page-title">JAT SHIRTS COLECCIÓN 2023</h1>

    <div class="main-container">
        <!-- Carrusel principal -->
        <div class="carousel-container">
            <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="./img/carrusel/video.jpg" class="d-block" alt="Modelo 1">
                    </div>
                    <div class="carousel-item">
                        <img src="./img/carrusel/imagenes.jpg" class="d-block" alt="Modelo 2">
                    </div>
                    <div class="carousel-item">
                        <img src="./img/carrusel/musica.jpg" class="d-block" alt="Modelo 3">
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>

        <!-- Galería de imágenes -->
        <div class="gallery-container">
            <div class="gallery-item large">
                <img src="https://via.placeholder.com/300x180?text=Look+Completo" alt="Look completo">
            </div>
            <div class="gallery-item">
                <img src="https://via.placeholder.com/200x150?text=Detalle" alt="Detalle">
            </div>
            <div class="gallery-item">
                <img src="https://via.placeholder.com/200x150?text=Detalle" alt="Detalle">
            </div>
            <div class="gallery-item large">
                <img src="https://via.placeholder.com/300x180?text=Look+Completo" alt="Look completo">
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>