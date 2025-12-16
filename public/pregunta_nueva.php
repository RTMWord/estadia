<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/controllers/ComunidadController.php';

// Requerir autenticación
if (!isLogged()) {
    header('Location: login.php?redirect=pregunta_nueva.php');
    exit;
}

$comunidadCtrl = new ComunidadController($pdo);
$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $cuerpo = trim($_POST['cuerpo'] ?? '');
    
    try {
        $preguntaId = $comunidadCtrl->crearPregunta(getUserId(), $titulo, $cuerpo);
        header('Location: pregunta_detalle.php?id=' . $preguntaId . '&msg=creada');
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Obtener etiquetas populares para sugerencias
$etiquetasPopulares = $comunidadCtrl->getEtiquetasPopulares(20);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Pregunta - Comunidad MetaHogar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: #f5f7fb; font-family: 'Segoe UI', sans-serif; }
        .form-box { background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .tag-suggestion { display: inline-block; background: #e8f1f7; color: #17466e; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; margin: 4px; cursor: pointer; transition: all 0.2s; }
        .tag-suggestion:hover { background: #17466e; color: white; }
        .info-box { background: #f0f7ff; border-left: 4px solid #17466e; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
    </style>
    
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

    <div class="container py-5" style="margin-top: 80px;">
        <div class="mb-3">
            <a href="comunidad.php" class="btn btn-outline-primary btn-sm"><i class="fas fa-arrow-left"></i> Volver a Comunidad</a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="form-box">
                    <h1 style="color: #17466e; margin-bottom: 25px;">
                        <i class="fas fa-question-circle"></i> Hacer una Pregunta
                    </h1>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post" id="preguntaForm">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Título de la Pregunta <span class="text-danger">*</span></label>
                            <input type="text" name="titulo" class="form-control form-control-lg" required 
                                   placeholder="Ej: ¿Cómo configurar sensores de movimiento?"
                                   minlength="10" maxlength="200"
                                   value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>">
                            <small class="form-text text-muted">Mínimo 10 caracteres. Sé específico y claro.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Descripción Detallada <span class="text-danger">*</span></label>
                            <textarea name="cuerpo" class="form-control" rows="10" required
                                      placeholder="Describe tu pregunta con el mayor detalle posible. Incluye:&#10;- ¿Qué problema tienes?&#10;- ¿Qué has intentado hacer?&#10;- ¿Qué resultados obtuviste?"
                                      minlength="20"><?= htmlspecialchars($_POST['cuerpo'] ?? '') ?></textarea>
                            <small class="form-text text-muted">Mínimo 20 caracteres. Cuanto más detallado, mejor.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Etiquetas (Opcional)</label>
                            <input type="text" name="etiquetas" id="etiquetasInput" class="form-control" 
                                   placeholder="configuración, sensores, wifi"
                                   value="<?= htmlspecialchars($_POST['etiquetas'] ?? '') ?>">
                            <small class="form-text text-muted">Separa las etiquetas con comas. Máximo 5 etiquetas.</small>
                            
                            <div class="mt-2">
                                <strong class="text-muted small">Etiquetas populares:</strong><br>
                                <?php foreach (array_slice($etiquetasPopulares, 0, 15) as $etiq): ?>
                                    <span class="tag-suggestion" onclick="addTag('<?= htmlspecialchars($etiq['Nombre']) ?>')">
                                        <?= htmlspecialchars($etiq['Nombre']) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane"></i> Publicar Pregunta
                            </button>
                            <a href="comunidad.php" class="btn btn-outline-secondary btn-lg">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="info-box">
                    <h5 style="color: #17466e;" class="mb-3">
                        <i class="fas fa-lightbulb"></i> Consejos para una Buena Pregunta
                    </h5>
                    <ul class="small mb-0">
                        <li class="mb-2"><strong>Título claro:</strong> Resume tu problema en una frase</li>
                        <li class="mb-2"><strong>Contexto:</strong> Explica qué estás tratando de hacer</li>
                        <li class="mb-2"><strong>Detalles:</strong> Incluye información sobre tu configuración</li>
                        <li class="mb-2"><strong>Qué intentaste:</strong> Menciona soluciones que ya probaste</li>
                        <li class="mb-2"><strong>Código o capturas:</strong> Si aplica, comparte ejemplos</li>
                        <li><strong>Etiquetas:</strong> Ayudan a que otros encuentren tu pregunta</li>
                    </ul>
                </div>

                <div class="info-box">
                    <h6 style="color: #17466e;" class="mb-2">
                        <i class="fas fa-search"></i> Antes de Preguntar
                    </h6>
                    <p class="small mb-2">Busca si alguien ya hizo una pregunta similar. Podrías encontrar tu respuesta más rápido.</p>
                    <a href="comunidad.php" class="btn btn-sm btn-outline-primary">Buscar Preguntas</a>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addTag(tagName) {
            const input = document.getElementById('etiquetasInput');
            const currentTags = input.value.split(',').map(t => t.trim()).filter(t => t);
            
            // No duplicar
            if (currentTags.includes(tagName)) {
                return;
            }
            
            // Máximo 5 etiquetas
            if (currentTags.length >= 5) {
                alert('Máximo 5 etiquetas permitidas');
                return;
            }
            
            currentTags.push(tagName);
            input.value = currentTags.join(', ');
        }

        // Validación básica del formulario
        document.getElementById('preguntaForm').addEventListener('submit', function(e) {
            const titulo = document.querySelector('[name="titulo"]').value;
            const cuerpo = document.querySelector('[name="cuerpo"]').value;
            
            if (titulo.length < 10) {
                e.preventDefault();
                alert('El título debe tener al menos 10 caracteres');
                return false;
            }
            
            if (cuerpo.length < 20) {
                e.preventDefault();
                alert('La descripción debe tener al menos 20 caracteres');
                return false;
            }
        });
    </script>
</body>
</html>
