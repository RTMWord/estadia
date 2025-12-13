<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/controllers/ComunidadController.php';

$comunidadCtrl = new ComunidadController($pdo);
$idIncidencia = $_GET['id'] ?? 0;

// Procesar acciones
$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLogged()) {
    $userId = getUserId();
    
    try {
        if (isset($_POST['crear_respuesta'])) {
            $cuerpo = trim($_POST['cuerpo'] ?? '');
            $comunidadCtrl->crearRespuesta($idIncidencia, $userId, $cuerpo);
            $mensaje = 'Respuesta publicada correctamente';
            header('Location: pregunta_detalle.php?id=' . $idIncidencia . '&msg=respuesta_creada');
            exit;
        } elseif (isset($_POST['votar_pregunta'])) {
            $valor = intval($_POST['valor']);
            $comunidadCtrl->votarPregunta($idIncidencia, $userId, $valor);
            header('Location: pregunta_detalle.php?id=' . $idIncidencia);
            exit;
        } elseif (isset($_POST['votar_respuesta'])) {
            $respuestaId = intval($_POST['respuesta_id']);
            $valor = intval($_POST['valor']);
            $comunidadCtrl->votarRespuesta($respuestaId, $userId, $valor);
            header('Location: pregunta_detalle.php?id=' . $idIncidencia);
            exit;
        } elseif (isset($_POST['aceptar_respuesta'])) {
            $respuestaId = intval($_POST['respuesta_id']);
            $comunidadCtrl->aceptarRespuesta($idIncidencia, $respuestaId, $userId);
            $mensaje = 'Respuesta marcada como aceptada';
            header('Location: pregunta_detalle.php?id=' . $idIncidencia . '&msg=respuesta_aceptada');
            exit;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Obtener pregunta (incidencia) y respuestas
$pregunta = $comunidadCtrl->ver($idIncidencia);

if (!$pregunta) {
    header('Location: comunidad.php');
    exit;
}

// Función helper para calcular tiempo transcurrido
function calcularTiempoTranscurrido($fecha) {
    $ahora = new DateTime();
    $entonces = new DateTime($fecha);
    $diferencia = $ahora->diff($entonces);
    
    if ($diferencia->y > 0) return 'Hace ' . $diferencia->y . ' año' . ($diferencia->y > 1 ? 's' : '');
    if ($diferencia->m > 0) return 'Hace ' . $diferencia->m . ' mes' . ($diferencia->m > 1 ? 'es' : '');
    if ($diferencia->d > 0) return 'Hace ' . $diferencia->d . ' día' . ($diferencia->d > 1 ? 's' : '');
    if ($diferencia->h > 0) return 'Hace ' . $diferencia->h . ' hora' . ($diferencia->h > 1 ? 's' : '');
    if ($diferencia->i > 0) return 'Hace ' . $diferencia->i . ' minuto' . ($diferencia->i > 1 ? 's' : '');
    return 'Hace unos segundos';
}

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'respuesta_creada') $mensaje = 'Respuesta publicada correctamente';
    if ($_GET['msg'] === 'respuesta_aceptada') $mensaje = 'Respuesta marcada como aceptada';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pregunta['Titulo']) ?> - Comunidad MetaHogar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: #f5f7fb; font-family: 'Segoe UI', sans-serif; }
        .vote-buttons { display: flex; flex-direction: column; align-items: center; gap: 5px; }
        .vote-btn { border: none; background: #f5f7fb; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; transition: all 0.2s; }
        .vote-btn:hover { background: #17466e; color: white; }
        .vote-btn.active { background: #17466e; color: white; }
        .vote-count { font-size: 1.5rem; font-weight: 700; color: #17466e; }
        .pregunta-box, .respuesta-box { background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .user-info { display: flex; align-items: center; gap: 10px; padding: 12px; background: #f8f9fa; border-radius: 8px; }
        .user-avatar { width: 48px; height: 48px; background: linear-gradient(135deg, #17466e 0%, #4b96c3 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; }
        .tag { display: inline-block; background: #e8f1f7; color: #17466e; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; margin-right: 8px; margin-bottom: 8px; }
        .accepted-badge { background: #28a745; color: white; padding: 8px 16px; border-radius: 8px; display: inline-block; margin-bottom: 15px; }
        .respuesta-box.accepted { border-left: 4px solid #28a745; }
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
        <?php if ($mensaje): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($mensaje) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <a href="comunidad.php" class="btn btn-outline-primary btn-sm"><i class="fas fa-arrow-left"></i> Volver a Comunidad</a>
        </div>

        <!-- Pregunta -->
        <div class="pregunta-box">
            <h1 class="mb-3" style="color: #17466e; font-size: 1.8rem;">
                <?= htmlspecialchars($pregunta['Titulo']) ?>
                <?php if ($pregunta['Estado'] === 'RESUELTA'): ?>
                    <span class="badge bg-success ms-2">✓ Resuelta</span>
                <?php endif; ?>
            </h1>

            <div class="mb-3 text-muted small">
                Preguntado <?= calcularTiempoTranscurrido($pregunta['FechaRegistro']) ?> • 
                <?= $pregunta['Vistas'] ?? 0 ?> vistas • 
                <?= $pregunta['NumRespuestas'] ?> respuesta<?= $pregunta['NumRespuestas'] != 1 ? 's' : '' ?>
            </div>

            <div class="row">
                <div class="col-auto">
                    <div class="vote-buttons">
                        <form method="post" style="margin: 0;">
                            <input type="hidden" name="valor" value="1">
                            <button type="submit" name="votar_pregunta" class="vote-btn" <?= !isLogged() ? 'disabled' : '' ?>>
                                <i class="fas fa-chevron-up"></i>
                            </button>
                        </form>
                        <div class="vote-count"><?= $pregunta['Puntos'] ?? 0 ?></div>
                        <form method="post" style="margin: 0;">
                            <input type="hidden" name="valor" value="-1">
                            <button type="submit" name="votar_pregunta" class="vote-btn" <?= !isLogged() ? 'disabled' : '' ?>>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="col">
                    <div class="mb-4" style="font-size: 1.05rem; line-height: 1.6;">
                        <?= nl2br(htmlspecialchars($pregunta['Descripcion'])) ?>
                    </div>

                    <div class="user-info">
                        <div class="user-avatar">
                            <?= strtoupper(substr($pregunta['Nombre'], 0, 1) . substr($pregunta['ApellidoP'], 0, 1)) ?>
                        </div>
                        <div>
                            <div class="fw-bold"><?= htmlspecialchars($pregunta['Nombre'] . ' ' . $pregunta['ApellidoP']) ?></div>
                            <div class="small text-muted"><?= calcularTiempoTranscurrido($pregunta['FechaRegistro']) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Respuestas -->
        <h3 style="color: #17466e;" class="mb-3">
            <?= $pregunta['NumRespuestas'] ?> Respuesta<?= $pregunta['NumRespuestas'] != 1 ? 's' : '' ?>
        </h3>

        <?php foreach ($pregunta['respuestas'] as $respuesta): ?>
            <div class="respuesta-box <?= $respuesta['Aceptada'] ? 'accepted' : '' ?>">
                <?php if ($respuesta['Aceptada']): ?>
                    <div class="accepted-badge">
                        <i class="fas fa-check"></i> Respuesta Aceptada
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-auto">
                        <div class="vote-buttons">
                            <form method="post" style="margin: 0;">
                                <input type="hidden" name="respuesta_id" value="<?= $respuesta['idRespuestaIncidencia'] ?>">
                                <input type="hidden" name="valor" value="1">
                                <button type="submit" name="votar_respuesta" class="vote-btn" <?= !isLogged() ? 'disabled' : '' ?>>
                                    <i class="fas fa-chevron-up"></i>
                                </button>
                            </form>
                            <div class="vote-count"><?= $respuesta['Puntos'] ?? 0 ?></div>
                            <form method="post" style="margin: 0;">
                                <input type="hidden" name="respuesta_id" value="<?= $respuesta['idRespuestaIncidencia'] ?>">
                                <input type="hidden" name="valor" value="-1">
                                <button type="submit" name="votar_respuesta" class="vote-btn" <?= !isLogged() ? 'disabled' : '' ?>>
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </form>
                            
                            <?php if (isLogged() && getUserId() == $pregunta['Usuario_idUsuario'] && !$respuesta['Aceptada']): ?>
                                <form method="post" style="margin: 0; margin-top: 10px;">
                                    <input type="hidden" name="respuesta_id" value="<?= $respuesta['idRespuestaIncidencia'] ?>">
                                    <button type="submit" name="aceptar_respuesta" class="vote-btn" title="Marcar como respuesta aceptada">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-4" style="font-size: 1.05rem; line-height: 1.6;">
                            <?= nl2br(htmlspecialchars($respuesta['Cuerpo'])) ?>
                        </div>

                        <div class="user-info">
                            <div class="user-avatar">
                                <?= strtoupper(substr($respuesta['Nombre'], 0, 1) . substr($respuesta['ApellidoP'], 0, 1)) ?>
                            </div>
                            <div>
                                <div class="fw-bold"><?= htmlspecialchars($respuesta['Nombre'] . ' ' . $respuesta['ApellidoP']) ?></div>
                                <div class="small text-muted">Respondió <?= calcularTiempoTranscurrido($respuesta['FechaRegistro']) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Formulario de respuesta -->
        <?php if (isLogged()): ?>
            <div class="pregunta-box mt-4">
                <h4 style="color: #17466e;" class="mb-3">Tu Respuesta</h4>
                <form method="post">
                    <div class="mb-3">
                        <textarea name="cuerpo" class="form-control" rows="6" required 
                                  placeholder="Escribe tu respuesta aquí... (mínimo 10 caracteres)"></textarea>
                    </div>
                    <button type="submit" name="crear_respuesta" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Publicar Respuesta
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="alert alert-info mt-4">
                <p class="mb-0"><a href="login.php">Inicia sesión</a> o <a href="signup.php">regístrate</a> para responder esta pregunta.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
