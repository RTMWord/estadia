<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/helpers/auth.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestra Misión - MetaHogar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e3f2fd 0%, #ffffff 100%);
        }
        .hero-section {
            background: linear-gradient(180deg, #17466e 0%, #4b96c3 100%); 
            color: white;
            padding: 80px 0 60px;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .card-mision {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 99, 204, 0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        .card-mision:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 99, 204, 0.25);
        }
        .card-header-custom {
            background: linear-gradient(180deg, #17466e 0%, #4b96c3 100%); 
            color: white;
            padding: 25px;
            font-size: 1.5rem;
            font-weight: 600;
        }
        .card-body-custom {
            padding: 30px;
            background: white;
        }
        .icon-box {
            width: 80px;
            height: 80px;
            background: linear-gradient(180deg, #17466e 0%, #4b96c3 100%); 
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 5px 15px rgba(0, 153, 255, 0.3);
        }
        .icon-box svg {
            width: 40px;
            height: 40px;
            fill: white;
        }
        .feature-item {
            padding: 20px;
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-radius: 15px;
            margin-bottom: 20px;
            border-left: 5px solid linear-gradient(180deg, #17466e 0%, #4b96c3 100%); 
            transition: all 0.3s ease;
        }
        .feature-item:hover {
            background: linear-gradient(135deg, #bbdefb 0%, #90caf9 100%);
            transform: translateX(10px);
        }
        .feature-item h4 {
            color: #003d82;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .image-section {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        .image-section img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .image-section:hover img {
            transform: scale(1.05);
        }
        .quote-section {
            background: linear-gradient(180deg, #17466e 0%, #4b96c3 100%); 
            color: white;
            padding: 50px;
            border-radius: 20px;
            margin: 40px 0;
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 61, 130, 0.3);
        }
        .quote-section::before {
            content: '"';
            font-size: 120px;
            position: absolute;
            top: -20px;
            left: 30px;
            opacity: 0.2;
            font-family: Georgia, serif;
        }
        .quote-text {
            font-size: 1.5rem;
            font-style: italic;
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/partials/bs-navbar.php'; ?>

    <div class="hero-section">
        <div class="container text-center">
            <h1 class="mb-4">Nuestra Misión</h1>
            <p class="lead fs-4">Comprometidos con la excelencia y el bienestar de nuestros clientes</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6 mb-4">
                <div class="card-mision">
                    <div class="card-header-custom">
                        <div class="icon-box">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5zm0 18c-3.87-.78-7-4.27-7-8V8.3l7-3.11 7 3.11V12c0 3.73-3.13 7.22-7 8z"/></svg>
                        </div>
                        <h3 class="text-center mb-0">Nuestra Misión Principal</h3>
                    </div>
                    <div class="card-body-custom">
                        <p class="lead text-center text-muted mb-4">
                            Transformar la experiencia del hogar en el futuro
                        </p>
                        <p style="line-height: 1.8; text-align: justify;">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                        </p>
                        <p style="line-height: 1.8; text-align: justify;">
                            Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="image-section">
                    <img src="assets/images/serv_1765343390_3850.jpg" alt="MetaHogar Misión" class="img-fluid">
                </div>
            </div>
        </div>

        <div class="quote-section">
            <p class="quote-text text-center">
                "Nuestra misión es proporcionar soluciones innovadoras que mejoren la calidad de vida de las personas, creando espacios inteligentes y sostenibles para el futuro."
            </p>
        </div>

        <div class="row mt-5">
            <div class="col-12 mb-4">
                <h2 class="text-center mb-5" style="color: #003d82; font-weight: 700;">Nuestros Pilares Fundamentales</h2>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="feature-item">
                    <div class="icon-box mx-auto mb-3" style="width: 60px; height: 60px;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                    </div>
                    <h4>Innovación Constante</h4>
                    <p style="text-align: justify;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla facilisi. Proin vel mauris eu libero tempor consectetur.</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="feature-item">
                    <div class="icon-box mx-auto mb-3" style="width: 60px; height: 60px;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    </div>
                    <h4>Calidad Garantizada</h4>
                    <p style="text-align: justify;">Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae. Sed consequat leo eget bibendum.</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="feature-item">
                    <div class="icon-box mx-auto mb-3" style="width: 60px; height: 60px;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                    </div>
                    <h4>Compromiso Social</h4>
                    <p style="text-align: justify;">Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-lg-6 mb-4">
                <div class="image-section">
                    <img src="assets/images/serv_1764193090_8603.png" alt="MetaHogar Servicios" class="img-fluid">
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card-mision">
                    <div class="card-header-custom">
                        <h3 class="text-center mb-0">Objetivos Estratégicos</h3>
                    </div>
                    <div class="card-body-custom">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <strong style="color: #0066cc;">Objetivo 1:</strong> Lorem ipsum dolor sit amet, consectetur adipiscing elit sed do eiusmod tempor incididunt.
                            </li>
                            <li class="mb-3">
                                <strong style="color: #0066cc;">Objetivo 2:</strong> Ut labore et dolore magna aliqua enim ad minim veniam quis nostrud exercitation.
                            </li>
                            <li class="mb-3">
                                <strong style="color: #0066cc;">Objetivo 3:</strong> Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore.
                            </li>
                            <li class="mb-3">
                                <strong style="color: #0066cc;">Objetivo 4:</strong> Excepteur sint occaecat cupidatat non proident sunt in culpa qui officia.
                            </li>
                            <li class="mb-3">
                                <strong style="color: #0066cc;">Objetivo 5:</strong> Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
