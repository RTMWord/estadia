<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/helpers/auth.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¿Quiénes Somos? - MetaHogar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/bs-navbar.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e0f2f1 0%, #ffffff 100%);
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
        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }
        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            position: relative;
            z-index: 1;
        }
        .hero-section p {
            position: relative;
            z-index: 1;
        }
        .card-about {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 131, 176, 0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        .card-about:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 131, 176, 0.25);
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
            box-shadow: 0 5px 15px rgba(0, 180, 216, 0.3);
        }
        .icon-box svg {
            width: 40px;
            height: 40px;
            fill: white;
        }
        .team-card {
            text-align: center;
            padding: 30px;
            background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%);
            border-radius: 20px;
            margin-bottom: 30px;
            transition: all 0.3s ease;
            border-top: 4px solid #0083b0;
        }
        .team-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0, 131, 176, 0.2);
        }
        .team-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #0083b0 0%, #00b4d8 100%);
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            box-shadow: 0 5px 15px rgba(0, 131, 176, 0.3);
        }
        .team-card h4 {
            color: #004d7a;
            font-weight: 600;
            margin: 15px 0 10px;
        }
        .team-card p {
            color: #00695c;
            font-size: 0.95rem;
        }
        .value-item {
            padding: 25px;
            background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%);
            border-radius: 15px;
            margin-bottom: 20px;
            border-left: 5px solid #0083b0;
            transition: all 0.3s ease;
        }
        .value-item:hover {
            background: linear-gradient(135deg, #b2dfdb 0%, #80cbc4 100%);
            transform: translateX(10px);
        }
        .value-item h4 {
            color: #004d7a;
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
            box-shadow: 0 10px 30px rgba(0, 77, 122, 0.3);
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
        .milestone {
            background: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 131, 176, 0.1);
            transition: all 0.3s ease;
            border-top: 4px solid #0083b0;
        }
        .milestone:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 131, 176, 0.2);
        }
        .milestone-year {
            font-size: 2rem;
            font-weight: 700;
            color: #0083b0;
            margin-bottom: 10px;
        }
        .milestone-text {
            color: #004d7a;
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
            <h1 class="mb-4">¿Quiénes Somos?</h1>
            <p class="lead fs-4">Conoce la historia y los valores que nos guían</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6 mb-4">
                <div class="card-about">
                    <div class="card-header-custom">
                        <div class="icon-box">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z"/></svg>
                        </div>
                        <h3 class="text-center mb-0">Nuestra Historia</h3>
                    </div>
                    <div class="card-body-custom">
                        <p class="lead text-center text-muted mb-4">
                            Desde nuestros inicios hasta hoy
                        </p>
                        <p style="line-height: 1.8; text-align: justify;">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Somos una empresa dedicada a la innovación y el compromiso con nuestros clientes desde hace más de una década.
                        </p>
                        <p style="line-height: 1.8; text-align: justify;">
                            Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur excepteur sint occaecat cupidatat non proident.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="image-section">
                    <img src="assets/images/serv_1765343390_3850.jpg" alt="MetaHogar Equipo" class="img-fluid">
                </div>
            </div>
        </div>

        <div class="quote-section">
            <p class="quote-text text-center">
                "Nos esforzamos cada día por ser más que una empresa, por ser un socio confiable en la transformación digital de los hogares y negocios."
            </p>
        </div>

        <div class="row mt-5 mb-5">
            <div class="col-12 mb-4">
                <h2 class="text-center mb-5" style="color: #004d7a; font-weight: 700;">Nuestros Valores</h2>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="value-item">
                    <h4>Integridad</h4>
                    <p style="text-align: justify;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin vel mauris eu libero tempor consectetur sed et turpis.</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="value-item">
                    <h4>Excelencia</h4>
                    <p style="text-align: justify;">Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae sed consequat leo eget bibendum.</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="value-item">
                    <h4>Innovación</h4>
                    <p style="text-align: justify;">Sed posuere consectetur est at lobortis donec id elit non mi porta gravida at eget metus maecenas faucibus mollis.</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="value-item">
                    <h4>Responsabilidad</h4>
                    <p style="text-align: justify;">Fusce dapibus tellus ac cursus commodo tortor mauris condimentum nibh ut fermentum massa justo sit amet risus.</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="value-item">
                    <h4>Transparencia</h4>
                    <p style="text-align: justify;">Aenean lacinia bibendum nulla sed consectetur praesent commodo cursus magna vestibulum aenean ou ligula.</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="value-item">
                    <h4>Compromiso</h4>
                    <p style="text-align: justify;">Cras justo odio dapibus ac facilisis in egestas eget velit aenean lacinia bibendum nulla sed consectetur.</p>
                </div>
            </div>
        </div>

        <div class="row align-items-center mt-5 mb-5">
            <div class="col-lg-6 mb-4">
                <div class="image-section">
                    <img src="assets/css/images/quienes_somos.png" alt="MetaHogar Servicios" class="img-fluid">
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card-about">
                    <div class="card-header-custom">
                        <h3 class="text-center mb-0">Por Qué Elegirnos</h3>
                    </div>
                    <div class="card-body-custom">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <strong style="color: #0083b0;">✓ Experiencia Probada:</strong> Más de 10 años en el mercado con miles de clientes satisfechos.
                            </li>
                            <li class="mb-3">
                                <strong style="color: #0083b0;">✓ Equipo Profesional:</strong> Personal capacitado y comprometido con la excelencia.
                            </li>
                            <li class="mb-3">
                                <strong style="color: #0083b0;">✓ Tecnología Avanzada:</strong> Utilizamos las mejores herramientas y soluciones del mercado.
                            </li>
                            <li class="mb-3">
                                <strong style="color: #0083b0;">✓ Atención Personalizada:</strong> Nos adaptamos a las necesidades específicas de cada cliente.
                            </li>
                            <li class="mb-3">
                                <strong style="color: #0083b0;">✓ Garantía de Calidad:</strong> Respaldamos nuestro trabajo con garantías y soporte continuo.
                            </li>
                            <li class="mb-3">
                                <strong style="color: #0083b0;">✓ Precios Competitivos:</strong> Ofrecemos las mejores soluciones al mejor valor.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5 mb-5">
            <div class="col-12 mb-4">
                <h2 class="text-center mb-5" style="color: #004d7a; font-weight: 700;">Nuestro Equipo de Liderazgo</h2>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="team-card">
                    <div class="team-avatar">#</div>
                    <h4>Juan Pérez</h4>
                    <p><strong>Director General</strong></p>
                    <p style="font-size: 0.9rem;">Lorem ipsum dolor sit amet, consectetur adipiscing elit sed do eiusmod tempor.</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="team-card">
                    <div class="team-avatar">#</div>
                    <h4>María García</h4>
                    <p><strong>Directora de Operaciones</strong></p>
                    <p style="font-size: 0.9rem;">Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua ut enim.</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="team-card">
                    <div class="team-avatar">#</div>
                    <h4>Carlos López</h4>
                    <p><strong>Director Técnico</strong></p>
                    <p style="font-size: 0.9rem;">Ut labore et dolore magna aliqua enim ad minim veniam quis nostrud.</p>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12 mb-4">
                <h2 class="text-center mb-5" style="color: #004d7a; font-weight: 700;">Nuestro Recorrido</h2>
            </div>
            <div class="col-md-3 mb-4">
                <div class="milestone">
                    <div class="milestone-year">2013</div>
                    <div class="milestone-text">Fundación de MetaHogar</div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="milestone">
                    <div class="milestone-year">2016</div>
                    <div class="milestone-text">Primer 1000 Clientes</div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="milestone">
                    <div class="milestone-year">2020</div>
                    <div class="milestone-text">Expansión Regional</div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="milestone">
                    <div class="milestone-year">2024</div>
                    <div class="milestone-text">Líder del Mercado</div>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
