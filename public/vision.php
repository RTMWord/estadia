<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/helpers/auth.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestra Visión - MetaHogar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e1f5fe 0%, #ffffff 100%);
        }
        .hero-section {
            background: linear-gradient(180deg, #17466e 0%, #4b96c3 100%); 
            padding: 80px 0 60px;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: -30%;
            left: -10%;
            width: 600px;
            height: 600px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
        }
        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -40%;
            right: -5%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            position: relative;
            z-index: 1;
        }
        .hero-section p {
            position: relative;
            z-index: 1;
        }
        .card-vision {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 153, 255, 0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            background: white;
        }
        .card-vision:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 153, 255, 0.25);
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
            box-shadow: 0 5px 15px rgba(0, 204, 255, 0.3);
        }
        .icon-box svg {
            width: 40px;
            height: 40px;
            fill: white;
        }
        .vision-item {
            padding: 25px;
            background: linear-gradient(135deg, #e1f5fe 0%, #b3e5fc 100%);
            border-radius: 15px;
            margin-bottom: 20px;
            border-left: 5px solid #0099ff;
            transition: all 0.3s ease;
            min-height: 180px;
        }
        .vision-item:hover {
            background: linear-gradient(180deg, #17466e 0%, #4b96c3 100%); 
            transform: translateX(10px);
            box-shadow: 0 5px 20px rgba(0, 153, 255, 0.2);
        }
        .vision-item h4 {
            color: #01579b;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .image-section {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            position: relative;
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
        .timeline {
            position: relative;
            padding: 40px 0;
        }
        .timeline-item {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 153, 255, 0.1);
            border-left: 4px solid #0099ff;
            transition: all 0.3s ease;
        }
        .timeline-item:hover {
            transform: translateX(10px);
            box-shadow: 0 8px 25px rgba(0, 153, 255, 0.2);
        }
        .timeline-year {
            display: inline-block;
            background: linear-gradient(180deg, #17466e 0%, #4b96c3 100%); 
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 15px;
        }
        .quote-section {
            background: linear-gradient(180deg, #17466e 0%, #4b96c3 100%); 
            color: white;
            padding: 50px;
            border-radius: 20px;
            margin: 40px 0;
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 99, 204, 0.3);
        }
        .quote-section::before {
            content: '"';
            font-size: 120px;
            position: absolute;
            top: -20px;
            right: 30px;
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
        .stats-box {
            background: linear-gradient(135deg, #ffffff 0%, #e1f5fe 100%);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 153, 255, 0.1);
            transition: all 0.3s ease;
        }
        .stats-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 153, 255, 0.2);
        }
        .stats-number {
            font-size: 3rem;
            font-weight: 700;
            color: #0099ff;
            margin-bottom: 10px;
        }
        .stats-label {
            color: #01579b;
            font-weight: 500;
        }
    </style>
    <script src="https://cdn.userway.org/widget.js" data-account="kjnkkEfZy1"></script>
    <style>
        .userway-icon {
            position: fixed !important;
            right: 20px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            z-index: 9999 !important;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/partials/bs-navbar.php'; ?>

    <div class="hero-section">
        <div class="container text-center">
            <h1 class="mb-4">Nuestra Visión</h1>
            <p class="lead fs-4">Construyendo el futuro con innovación y compromiso</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6 mb-4">
                <div class="image-section">
                    <img src="assets/images/serv_1765343390_3850.jpg" alt="MetaHogar Visión" class="img-fluid">
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card-vision">
                    <div class="card-header-custom">
                        <div class="icon-box">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                        </div>
                        <h3 class="text-center mb-0">Nuestra Visión de Futuro</h3>
                    </div>
                    <div class="card-body-custom">
                        <p class="lead text-center text-muted mb-4">
                            Liderando la transformación digital del hogar
                        </p>
                        <p style="line-height: 1.8; text-align: justify;">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        </p>
                        <p style="line-height: 1.8; text-align: justify;">
                            Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="quote-section">
            <p class="quote-text text-center">
                "Visualizamos un mundo donde la tecnología y el confort se fusionan para crear espacios de vida inteligentes, sostenibles y adaptados a las necesidades del futuro."
            </p>
        </div>

        <div class="row mt-5 mb-5">
            <div class="col-12 mb-4">
                <h2 class="text-center mb-5" style="color: #01579b; font-weight: 700;">Pilares de Nuestra Visión</h2>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="vision-item">
                    <div class="icon-box mx-auto mb-3" style="width: 60px; height: 60px;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
                    </div>
                    <h4>Innovación Tecnológica</h4>
                    <p style="text-align: justify;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="vision-item">
                    <div class="icon-box mx-auto mb-3" style="width: 60px; height: 60px;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M17 10h-4l3-8H7v11h3l-3 8h14V10z"/></svg>
                    </div>
                    <h4>Sostenibilidad</h4>
                    <p style="text-align: justify;">Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae. Integer posuere erat a ante venenatis dapibus posuere velit aliquet.</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="vision-item">
                    <div class="icon-box mx-auto mb-3" style="width: 60px; height: 60px;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    </div>
                    <h4>Enfoque Humano</h4>
                    <p style="text-align: justify;">Sed posuere consectetur est at lobortis. Donec id elit non mi porta gravida at eget metus. Maecenas faucibus mollis interdum.</p>
                </div>
            </div>
        </div>

        <div class="row mt-5 mb-5">
            <div class="col-12 mb-4">
                <h2 class="text-center mb-5" style="color: #01579b; font-weight: 700;">Metas a Alcanzar</h2>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stats-box">
                    <div class="stats-number">2025</div>
                    <div class="stats-label">Año de Expansión</div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stats-box">
                    <div class="stats-number">100%</div>
                    <div class="stats-label">Satisfacción</div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stats-box">
                    <div class="stats-number">50+</div>
                    <div class="stats-label">Colaboradores</div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stats-box">
                    <div class="stats-number">24/7</div>
                    <div class="stats-label">Atención</div>
                </div>
            </div>
        </div>

        <div class="row align-items-center mt-5">
            <div class="col-lg-6 mb-4">
                <div class="card-vision">
                    <div class="card-header-custom">
                        <h3 class="text-center mb-0">Hoja de Ruta Estratégica</h3>
                    </div>
                    <div class="card-body-custom">
                        <div class="timeline">
                            <div class="timeline-item">
                                <span class="timeline-year">2025-2026</span>
                                <h5 style="color: #01579b; margin-top: 15px;">Consolidación Regional</h5>
                                <p style="text-align: justify;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                            </div>
                            <div class="timeline-item">
                                <span class="timeline-year">2027-2028</span>
                                <h5 style="color: #01579b; margin-top: 15px;">Expansión Nacional</h5>
                                <p style="text-align: justify;">Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                            </div>
                            <div class="timeline-item">
                                <span class="timeline-year">2029-2030</span>
                                <h5 style="color: #01579b; margin-top: 15px;">Liderazgo Internacional</h5>
                                <p style="text-align: justify;">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="image-section">
                    <img src="assets/images/serv_1764193090_8603.png" alt="MetaHogar Futuro" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
