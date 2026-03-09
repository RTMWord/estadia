<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/helpers/auth.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sitios de Interés - MetaHogar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/bs-navbar.css">
    <style>
        :root {
            --azul-1: #17466e;
            --azul-2: #4b96c3;
        }
        body {
            background: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .hero-sites {
            background: linear-gradient(180deg, var(--azul-1) 0%, var(--azul-2) 100%);
            color: white;
            padding: 80px 0 60px;
            position: relative;
            overflow: hidden;
        }
        .hero-sites::before,
        .hero-sites::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
        }
        .hero-sites::before {
            width: 380px; height: 380px; top: -120px; right: -80px;
        }
        .hero-sites::after {
            width: 260px; height: 260px; bottom: -80px; left: -60px;
        }
        .section-title {
            color: var(--azul-1);
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .card-site {
            border: none;
            border-radius: 18px;
            box-shadow: 0 12px 36px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            background: white;
        }
        .card-site:hover {
            transform: translateY(-8px);
            box-shadow: 0 18px 42px rgba(23, 70, 110, 0.18);
        }
        .card-site img {
            height: 160px;
            object-fit: contain;
            background: #f8f9ff;
            padding: 20px;
        }
        .card-site .card-body {
            min-height: 180px;
        }
        .badge-pill {
            border-radius: 999px;
            padding: 8px 14px;
            font-size: 0.8rem;
            background: linear-gradient(180deg, var(--azul-1) 0%, var(--azul-2) 100%);
            color: white;
            box-shadow: 0 6px 14px rgba(23, 70, 110, 0.25);
        }
        .btn-site {
            background: linear-gradient(180deg, var(--azul-1) 0%, var(--azul-2) 100%);
            border: none;
            color: white;
            padding: 10px 18px;
            border-radius: 10px;
            font-weight: 600;
            box-shadow: 0 8px 18px rgba(23, 70, 110, 0.25);
        }
        .btn-site:hover { opacity: 0.92; color: white; }
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

    <header class="hero-sites text-center">
        <div class="container">
            <p class="text-uppercase mb-2" style="letter-spacing: 2px;">Sitios y portales</p>
            <h1 class="display-5 fw-bold">Sitios de Interés MetaHogar</h1>
            <p class="lead mt-3 mb-0">Accesos rápidos a recursos clave y servicios aliados.</p>
        </div>
    </header>

    <main class="container py-5">
        <!-- SECCIÓN 1: Sitios y Portales MetaHogar -->
        <div class="mb-5 pb-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <h2 class="section-title mb-0">Sitios y Portales MetaHogar</h2>
                <span class="badge-pill">Internos</span>
            </div>

            <div class="row g-4 row-cols-1 row-cols-sm-2 row-cols-lg-3">
                <?php
                $sitios_metahogar = [
                    [
                        'title' => 'Portal MetaHogar',
                        'desc'  => 'Información corporativa y novedades internas.',
                        'img'   => 'assets/css/images/LogoMeta.png',
                        'url'   => 'index.php',
                        'tag'   => 'Corporativo'
                    ],
                    [
                        'title' => 'Centro de Diagnóstico',
                        'desc'  => 'Servicios especializados y soporte técnico.',
                        'img'   => 'assets/css/images/cdiagnostico.png',
                        'url'   => 'servicios.php',
                        'tag'   => 'Servicios'
                    ],
                    [
                        'title' => 'Comunidad MetaHogar',
                        'desc'  => 'Foros, eventos y colaboración con aliados.',
                        'img'   => 'assets/css/images/comunidad.png',
                        'url'   => 'comunidad.php',
                        'tag'   => 'Comunidad'
                    ],
                ];

                foreach ($sitios_metahogar as $site): ?>
                    <div class="col">
                        <div class="card card-site h-100">
                            <img src="<?= htmlspecialchars($site['img']) ?>" class="card-img-top" alt="<?= htmlspecialchars($site['title']) ?>">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title fw-bold mb-0" style="color: var(--azul-1);">
                                        <?= htmlspecialchars($site['title']) ?>
                                    </h5>
                                    <span class="badge-pill"><?= htmlspecialchars($site['tag']) ?></span>
                                </div>
                                <p class="card-text text-muted flex-grow-1" style="text-align: justify;">
                                    <?= htmlspecialchars($site['desc']) ?>
                                </p>
                                <div class="mt-3">
                                    <a class="btn btn-site w-100" href="<?= htmlspecialchars($site['url']) ?>" target="_blank" rel="noopener">Ir al sitio</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <hr class="my-5">

        <!-- SECCIÓN 2: Sitios de Interés Independientes -->
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <h2 class="section-title mb-0">Sitios de Interés Independientes</h2>
                <span class="badge-pill">Externos</span>
            </div>

            <div class="row g-4 row-cols-1 row-cols-sm-2 row-cols-lg-3">
                <?php
                $sitios_externos = [
                    [
                        'title' => 'Ayuntamiento de Jiutepec',
                        'desc'  => 'Información municipal y trámites del ayuntamiento de Jiutepec.',
                        'img'   => 'assets/images/logo-jiutepec.jpeg',
                        'url'   => 'https://www.jiutepec.gob.mx',
                        'tag'   => 'Gobierno'
                    ],
                    [
                        'title' => 'IMSS',
                        'desc'  => 'Instituto Mexicano del Seguro Social - Servicios de salud y prestaciones.',
                        'img'   => 'assets/images/logo-imss.jpg',
                        'url'   => 'https://www.imss.gob.mx',
                        'tag'   => 'Salud'
                    ],
                    [
                        'title' => 'ISSSTE',
                        'desc'  => 'Instituto de Seguridad y Servicios Sociales de Trabajadores del Estado.',
                        'img'   => 'assets/images/issste-logo.png',
                        'url'   => 'https://www.gob.mx/issste',
                        'tag'   => 'Salud'
                    ],
                    [
                        'title' => 'INEGI',
                        'desc'  => 'Instituto Nacional de Estadística y Geografía - Información demográfica.',
                        'img'   => 'assets/images/inegi-logo.png',
                        'url'   => 'https://www.inegi.org.mx',
                        'tag'   => 'Información'
                    ],
                    [
                        'title' => 'Secretaría de Bienestar',
                        'desc'  => 'Programas y servicios para personas adultas mayores en México.',
                        'img'   => 'assets/images/logo-bienestar.jpeg',
                        'url'   => 'https://www.gob.mx/bienestar',
                        'tag'   => 'Social'
                    ],
                    [
                        'title' => 'CONAPO',
                        'desc'  => 'Consejo Nacional de Población - Análisis demográfico de adultos mayores.',
                        'img'   => 'assets/images/Conapo-logo-2025.jpeg',
                        'url'   => 'https://www.gob.mx/conapo',
                        'tag'   => 'Demográfico'
                    ],
                ];

                foreach ($sitios_externos as $site): ?>
                    <div class="col">
                        <div class="card card-site h-100">
                            <img src="<?= htmlspecialchars($site['img']) ?>" class="card-img-top" alt="<?= htmlspecialchars($site['title']) ?>">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title fw-bold mb-0" style="color: var(--azul-1);">
                                        <?= htmlspecialchars($site['title']) ?>
                                    </h5>
                                    <span class="badge-pill"><?= htmlspecialchars($site['tag']) ?></span>
                                </div>
                                <p class="card-text text-muted flex-grow-1" style="text-align: justify;">
                                    <?= htmlspecialchars($site['desc']) ?>
                                </p>
                                <div class="mt-3">
                                    <a class="btn btn-site w-100" href="<?= htmlspecialchars($site['url']) ?>" target="_blank" rel="noopener">Ir al sitio</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
