<?php
require_once '../app/config/db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buzón de Sugerencias - MetaHogar</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bs-navbar.css">
    <script src="https://cdn.userway.org/widget.js" data-account="kjnkkEfZy1"></script>
    <style>
        body{
            background: linear-gradient(180deg,#f7fbff 0%, #ffffff 60%);
            min-height:100vh;
        }
        .suggestions-card{
            max-width:760px;
            margin:40px auto;
            box-shadow:0 6px 22px rgba(0, 0, 0, 0.08);
            border-radius:12px;
            overflow:hidden;
        }
        .hero{
            background:linear-gradient(90deg,#4f46e5,#06b6d4);
            color:white;
            padding:28px 24px;
        }
        .hero h2{margin:0;font-weight:600}
        .small-note{font-size:1.2rem;color:#000000}
        /* Forzar color de fondo del navbar para que no quede transparente */
    </style>
</head>
<body>
    <?php include __DIR__ . '/partials/bs-navbar.php'; ?>

    <main class="container">
        <section class="suggestions-card bg-white">
            <div class="hero">
                <h2>Buzón de Sugerencias</h2>
                <p class="small-note mt-2">Tu opinión nos ayuda a mejorar. Cuéntanos qué podemos hacer mejor.</p>
            </div>
            <div class="p-4">
                <?php if (isset($_GET['sent'])): ?>
                    <div class="alert alert-success">Gracias por tu comentario. Lo revisaremos pronto.</div>
                <?php endif; ?>

                <form id="sugerenciaForm" method="POST" action="../app/controllers/SugerenciaController.php" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" name="email" class="form-control" id="email" placeholder="tu@correo.com">
                                <label for="email">Tu correo (opcional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="titulo" class="form-control" id="titulo" placeholder="Título de la sugerencia" required>
                                <label for="titulo">Título</label>
                                <div class="invalid-feedback">Por favor indica un título.</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea name="descripcion" class="form-control" placeholder="Escribe tu sugerencia aquí" id="descripcion" style="height:140px" required></textarea>
                                <label for="descripcion">Descripción</label>
                                <div class="invalid-feedback">La descripción es obligatoria.</div>
                            </div>
                        </div>
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <small class="text-muted">Puedes dejar tu correo si deseas seguimiento.</small>
                            <div>
                                <button type="reset" class="btn btn-outline-secondary me-2">Limpiar</button>
                                <button type="submit" name="enviar" class="btn btn-primary">Enviar Sugerencia</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            'use strict'
            var form = document.getElementById('sugerenciaForm')
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })()
    </script>
</body>
</html>
