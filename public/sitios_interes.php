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
                        'img'   => 'assets/images/serv_1765343390_3850.jpg',
                        'url'   => 'portal.php',
                        'tag'   => 'Corporativo'
                    ],
                    [
                        'title' => 'Centro de Diagnóstico',
                        'desc'  => 'Servicios especializados y soporte técnico.',
                        'img'   => 'assets/images/serv_1764193090_8603.png',
                        'url'   => 'diagnostico.php',
                        'tag'   => 'Servicios'
                    ],
                    [
                        'title' => 'Comunidad MetaHogar',
                        'desc'  => 'Foros, eventos y colaboración con aliados.',
                        'img'   => 'https://placehold.co/500x300/17466e/ffffff?text=Comunidad',
                        'url'   => 'comunidad.php',
                        'tag'   => 'Comunidad'
                    ],
                    [
                        'title' => 'Soporte 24/7',
                        'desc'  => 'Mesas de ayuda y documentación técnica.',
                        'img'   => 'https://placehold.co/500x300/4b96c3/ffffff?text=Soporte',
                        'url'   => 'soporte.php',
                        'tag'   => 'Soporte'
                    ],
                    [
                        'title' => 'Portal de Innovación',
                        'desc'  => 'Tendencias, investigación y proyectos estratégicos.',
                        'img'   => 'https://placehold.co/500x300/2d6a8f/ffffff?text=Innovacion',
                        'url'   => 'innovacion.php',
                        'tag'   => 'Innovación'
                    ],
                    [
                        'title' => 'Capacitación',
                        'desc'  => 'Cursos, webinars y materiales de formación.',
                        'img'   => 'https://placehold.co/500x300/3d7aa0/ffffff?text=Capacitacion',
                        'url'   => 'capacitacion.php',
                        'tag'   => 'Aprendizaje'
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
                        'img'   => 'https://placehold.co/500x300/1a5a8f/ffffff?text=Ayuntamiento',
                        'url'   => 'https://www.jiutepec.gob.mx',
                        'tag'   => 'Gobierno'
                    ],
                    [
                        'title' => 'IMSS',
                        'desc'  => 'Instituto Mexicano del Seguro Social - Servicios de salud y prestaciones.',
                        'img'   => 'https://placehold.co/500x300/0d47a1/ffffff?text=IMSS',
                        'url'   => 'https://www.imss.gob.mx',
                        'tag'   => 'Salud'
                    ],
                    [
                        'title' => 'ISSSTE',
                        'desc'  => 'Instituto de Seguridad y Servicios Sociales de Trabajadores del Estado.',
                        'img'   => 'https://placehold.co/500x300/1565c0/ffffff?text=ISSSTE',
                        'url'   => 'https://www.gob.mx/issste',
                        'tag'   => 'Salud'
                    ],
                    [
                        'title' => 'INEGI',
                        'desc'  => 'Instituto Nacional de Estadística y Geografía - Información demográfica.',
                        'img'   => 'https://placehold.co/500x300/1976d2/ffffff?text=INEGI',
                        'url'   => 'https://www.inegi.org.mx',
                        'tag'   => 'Información'
                    ],
                    [
                        'title' => 'Secretaría de Bienestar',
                        'desc'  => 'Programas y servicios para personas adultas mayores en México.',
                        'img'   => 'https://placehold.co/500x300/1e88e5/ffffff?text=Bienestar',
                        'url'   => 'https://www.gob.mx/bienestar',
                        'tag'   => 'Social'
                    ],
                    [
                        'title' => 'CONAPO',
                        'desc'  => 'Consejo Nacional de Población - Análisis demográfico de adultos mayores.',
                        'img'   => 'https://placehold.co/500x300/2196f3/ffffff?text=CONAPO',
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
