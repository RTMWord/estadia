<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/helpers/auth.php';

// Procesar nueva incidencia (comentario/pregunta simple)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLogged()) {
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $usuarioId = getUserId();
    
    if (!empty($titulo) && !empty($descripcion)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO Incidencia (Usuario_idUsuario, Titulo, Descripcion, Estado, FechaRegistro)
                VALUES (:usuario_id, :titulo, :descripcion, 'ABIERTA', NOW())
            ");
            $stmt->execute([
                ':usuario_id' => $usuarioId,
                ':titulo' => $titulo,
                ':descripcion' => $descripcion
            ]);
            
            $_SESSION['mensaje'] = 'Pregunta publicada correctamente';
            header('Location: comunidad.php');
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al publicar: ' . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = 'Por favor completa título y descripción';
    }
}

// Procesar actualización de estado (solo admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_estado']) && isLogged()) {
    $idIncidencia = intval($_POST['idIncidencia'] ?? 0);
    $estado = $_POST['estado'] ?? '';
    
    if (in_array($estado, ['ABIERTA', 'EN_PROGRESO', 'RESUELTA', 'CERRADA'])) {
        try {
            $stmt = $pdo->prepare("UPDATE Incidencia SET Estado = :estado WHERE idIncidencia = :id");
            $stmt->execute([':estado' => $estado, ':id' => $idIncidencia]);
            $_SESSION['mensaje'] = 'Estado actualizado';
            header('Location: comunidad.php');
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al actualizar: ' . $e->getMessage();
        }
    }
}

// Obtener todas las incidencias (preguntas/comentarios)
$stmt = $pdo->query("
    SELECT i.*, u.Nombre, u.ApellidoP, u.Email
    FROM Incidencia i
    LEFT JOIN Usuario u ON i.Usuario_idUsuario = u.idUsuario
    ORDER BY i.FechaRegistro DESC
");
$incidencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contar estadísticas
$estadisticas = $pdo->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN Estado = 'ABIERTA' THEN 1 ELSE 0 END) as abiertas,
        SUM(CASE WHEN Estado = 'RESUELTA' THEN 1 ELSE 0 END) as resueltas,
        SUM(CASE WHEN Estado = 'CERRADA' THEN 1 ELSE 0 END) as cerradas
    FROM Incidencia
")->fetch(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comunidad Digital - MetaHogar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.userway.org/widget.js" data-account="kjnkkEfZy1"></script>
    <style>
        .userway-icon {
            position: fixed !important;
            right: 20px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            z-index: 9999 !important;
        }
        body { background: #f5f7fb; font-family: 'Segoe UI', sans-serif; }
        .hero { background: linear-gradient(180deg, #17466e 0%, #4b96c3 100%); color: white; padding: 60px 0 40px; }
        .stat-card { background: white; border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .stat-number { font-size: 2.5rem; font-weight: 700; color: #17466e; }
        .incidencia-card { background: white; border-left: 4px solid #17466e; border-radius: 8px; padding: 20px; margin-bottom: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s; }
        .incidencia-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
        .titulo-incidencia { color: #17466e; font-weight: 600; font-size: 1.1rem; margin-bottom: 8px; }
        .meta { font-size: 0.9rem; color: #666; }
        .badge-abierta { background: #ffc107; color: #000; }
        .badge-en_progreso { background: #0dcaf0; color: #000; }
        .badge-resuelta { background: #198754; color: #fff; }
        .badge-cerrada { background: #6c757d; color: #fff; }
        .btn-ask { background: #17466e; color: white; padding: 12px 30px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-block; border: none; cursor: pointer; }
        .btn-ask:hover { background: #4b96c3; color: white; text-decoration: none; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/partials/bs-navbar.php'; ?>

    <div class="hero text-center">
        <div class="container">
            <h1 class="display-5 fw-bold mb-2">Comunidad Digital</h1>
            <p class="lead mb-4">Comparte preguntas, dudas y experiencias</p>
            <?php if (isLogged()): ?>
                <button type="button" class="btn-ask" data-bs-toggle="modal" data-bs-target="#modalNuevaIncidencia">
                    <i class="fas fa-plus"></i> Hacer Pregunta
                </button>
            <?php else: ?>
                <p><a href="login.php" style="color: white; text-decoration: underline; font-weight: 600;">Inicia sesión</a> para participar</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="container py-5">
        <!-- Mensajes -->
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['mensaje']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?= $estadisticas['total'] ?? 0 ?></div>
                    <small class="text-muted">Total Preguntas</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?= $estadisticas['abiertas'] ?? 0 ?></div>
                    <small class="text-muted">Abiertas</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?= $estadisticas['resueltas'] ?? 0 ?></div>
                    <small class="text-muted">Resueltas</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?= $estadisticas['cerradas'] ?? 0 ?></div>
                    <small class="text-muted">Cerradas</small>
                </div>
            </div>
        </div>

        <!-- Listado de Incidencias -->
        <div class="row">
            <div class="col-lg-8">
                <h3 class="mb-4" style="color: #17466e;">Preguntas de la Comunidad</h3>

                <?php if (empty($incidencias)): ?>
                    <div class="alert alert-info">
                        <p class="mb-0">No hay preguntas todavía. ¡Sé el primero en preguntar!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($incidencias as $inc): ?>
                        <div class="incidencia-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div style="flex: 1;">
                                    <div class="titulo-incidencia">
                                        <?= htmlspecialchars($inc['Titulo']) ?>
                                    </div>
                                    <div class="meta">
                                        <?php 
                                        $fecha = new DateTime($inc['FechaRegistro']);
                                        $ahora = new DateTime();
                                        $diff = $ahora->diff($fecha);
                                        
                                        if ($diff->d > 0) {
                                            echo 'Hace ' . $diff->d . ' día(s)';
                                        } elseif ($diff->h > 0) {
                                            echo 'Hace ' . $diff->h . ' hora(s)';
                                        } else {
                                            echo 'Hace poco';
                                        }
                                        ?>
                                        • por <strong><?= htmlspecialchars($inc['Nombre'] . ' ' . ($inc['ApellidoP'] ?? '')) ?></strong>
                                    </div>
                                    <p class="mt-2 mb-0" style="color: #555; font-size: 0.95rem;">
                                        <?= htmlspecialchars(substr($inc['Descripcion'], 0, 150)) ?>...
                                    </p>
                                </div>
                                <div style="margin-left: 15px;">
                                    <span class="badge badge-<?= strtolower($inc['Estado']) ?>">
                                        <?= ucfirst(strtolower($inc['Estado'])) ?>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Botones de admin para cambiar estado -->
                            <?php if (isLogged()): // Simplificar: permitir a usuarios cambiar estado
                            ?>
                                <div class="mt-3 pt-3 border-top">
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="idIncidencia" value="<?= $inc['idIncidencia'] ?>">
                                        <select name="estado" class="form-select form-select-sm" style="width: 200px; display: inline-block;">
                                            <option value="ABIERTA" <?= $inc['Estado'] === 'ABIERTA' ? 'selected' : '' ?>>Abierta</option>
                                            <option value="EN_PROGRESO" <?= $inc['Estado'] === 'EN_PROGRESO' ? 'selected' : '' ?>>En Progreso</option>
                                            <option value="RESUELTA" <?= $inc['Estado'] === 'RESUELTA' ? 'selected' : '' ?>>Resuelta</option>
                                            <option value="CERRADA" <?= $inc['Estado'] === 'CERRADA' ? 'selected' : '' ?>>Cerrada</option>
                                        </select>
                                        <button type="submit" name="actualizar_estado" class="btn btn-sm btn-primary">Actualizar</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <h5 style="color: #17466e; margin-bottom: 15px;">💡 Consejos</h5>
                    <ul class="small text-muted" style="list-style: none; padding: 0;">
                        <li class="mb-2">✓ Sé claro en tu pregunta</li>
                        <li class="mb-2">✓ Proporciona detalles</li>
                        <li class="mb-2">✓ Sé respetuoso</li>
                        <li class="mb-2">✓ Busca antes de preguntar</li>
                        <li>✓ Ayuda a otros usuarios</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Modal Nueva Incidencia -->
    <?php if (isLogged()): ?>
        <div class="modal fade" id="modalNuevaIncidencia" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nueva Pregunta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Título</label>
                                <input type="text" name="titulo" class="form-control" required 
                                       placeholder="¿Cuál es tu pregunta?" minlength="5" maxlength="150">
                                <small class="text-muted">Mínimo 5 caracteres</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="6" required
                                          placeholder="Describe tu pregunta con detalle..." minlength="10" maxlength="5000"></textarea>
                                <small class="text-muted">Mínimo 10 caracteres</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Publicar Pregunta</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</body>
</html>
