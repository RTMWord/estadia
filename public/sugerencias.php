<?php
require_once '../app/config/db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buzón de Sugerencias - MetaHogar</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Widget de Accesibilidad -->
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
    <div class="container py-5">
        <h2 class="text-primary mb-4">Buzón de Sugerencias</h2>
        <?php if (isset($_GET['sent'])): ?>
            <div class="alert alert-success">Gracias por tu comentario. Lo revisaremos pronto.</div>
        <?php endif; ?>
        <form method="POST" action="../app/controllers/SugerenciaController.php">
            <div class="mb-3">
                <label class="form-label">Tu correo (opcional)</label>
                <input type="email" name="email" class="form-control" placeholder="Tu correo si deseas seguimiento">
            </div>
            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text" name="titulo" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="5" required></textarea>
            </div>
            <button type="submit" name="enviar" class="btn btn-primary">Enviar Sugerencia</button>
        </form>
    </div>
    <?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
