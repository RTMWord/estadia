<?php
require_once '../../app/config/db.php';
require_once '../../app/helpers/auth.php';
requireRole($pdo, 'administrador');
require_once __DIR__ . '/_security_check.php';

// Procesar eliminación
if (isset($_POST['eliminar']) && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM Incidencia WHERE idIncidencia = :id");
        $stmt->execute([':id' => $id]);
        $_SESSION['mensaje'] = "Incidencia eliminada correctamente.";
        $_SESSION['tipo_mensaje'] = "success";
    } catch (Exception $e) {
        $_SESSION['mensaje'] = "Error al eliminar: " . $e->getMessage();
        $_SESSION['tipo_mensaje'] = "danger";
    }
    header('Location: comunidad.php');
    exit;
}

// Procesar actualización de estado
if (isset($_POST['actualizar_estado']) && isset($_POST['id']) && isset($_POST['estado'])) {
    $id = (int)$_POST['id'];
    $estado = $_POST['estado'];
    try {
        $stmt = $pdo->prepare("UPDATE Incidencia SET Estado = :estado WHERE idIncidencia = :id");
        $stmt->execute([':estado' => $estado, ':id' => $id]);
        $_SESSION['mensaje'] = "Estado actualizado correctamente.";
        $_SESSION['tipo_mensaje'] = "success";
    } catch (Exception $e) {
        $_SESSION['mensaje'] = "Error al actualizar: " . $e->getMessage();
        $_SESSION['tipo_mensaje'] = "danger";
    }
    header('Location: comunidad.php');
    exit;
}

// Obtener todas las incidencias con información del usuario
$stmt = $pdo->query("
    SELECT 
        i.idIncidencia,
        i.Titulo,
        i.Descripcion,
        i.Estado,
        i.FechaRegistro,
        i.Usuario_idUsuario,
        u.Nombre,
        u.ApellidoP,
        u.Email
    FROM Incidencia i
    LEFT JOIN Usuario u ON i.Usuario_idUsuario = u.idUsuario
    ORDER BY i.FechaRegistro DESC
");
$incidencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular estadísticas
$stmt = $pdo->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN Estado = 'ABIERTA' THEN 1 ELSE 0 END) as abiertas,
        SUM(CASE WHEN Estado = 'EN_PROGRESO' THEN 1 ELSE 0 END) as en_progreso,
        SUM(CASE WHEN Estado = 'RESUELTA' THEN 1 ELSE 0 END) as resueltas,
        SUM(CASE WHEN Estado = 'CERRADA' THEN 1 ELSE 0 END) as cerradas
    FROM Incidencia
");
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Función para calcular tiempo transcurrido
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff < 60) return "Hace " . $diff . " segundos";
    if ($diff < 3600) return "Hace " . floor($diff / 60) . " minutos";
    if ($diff < 86400) return "Hace " . floor($diff / 3600) . " horas";
    if ($diff < 604800) return "Hace " . floor($diff / 86400) . " días";
    return date('d/m/Y', $time);
}

function getBadgeClass($estado) {
    switch($estado) {
        case 'ABIERTA': return 'bg-danger';
        case 'EN_PROGRESO': return 'bg-warning text-dark';
        case 'RESUELTA': return 'bg-success';
        case 'CERRADA': return 'bg-secondary';
        default: return 'bg-secondary';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Gestionar Comunidad - Admin MetaHogar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.userway.org/widget.js" data-account="kjnkkEfZy1"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .stat-card { border-left: 4px solid; }
        .stat-card.total { border-color: #0d6efd; }
        .stat-card.abiertas { border-color: #dc3545; }
        .stat-card.progreso { border-color: #ffc107; }
        .stat-card.resueltas { border-color: #198754; }
        .stat-card.cerradas { border-color: #6c757d; }
        .incidencia-card { 
            transition: all 0.3s; 
            border-left: 4px solid #dee2e6;
        }
        .incidencia-card:hover { 
            box-shadow: 0 4px 12px rgba(0,0,0,0.15); 
            transform: translateY(-2px);
        }
        .userway-icon {
            position: fixed !important;
            right: 20px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            z-index: 9999 !important;
        }
        .descripcion-preview {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/../partials/bs-navbar.php'; ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Gestión de Comunidad Digital</h1>
            <p class="text-muted mb-0">Administrar incidencias y preguntas de la comunidad</p>
        </div>
        <div>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver al panel
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['mensaje'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
    <?php endif; ?>

    <!-- Estadísticas -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card stat-card total h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0"><?= $stats['total'] ?></h3>
                    <small class="text-muted">Total</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card abiertas h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0"><?= $stats['abiertas'] ?></h3>
                    <small class="text-muted">Abiertas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card progreso h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0"><?= $stats['en_progreso'] ?></h3>
                    <small class="text-muted">En Progreso</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card resueltas h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0"><?= $stats['resueltas'] ?></h3>
                    <small class="text-muted">Resueltas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card cerradas h-100">
                <div class="card-body text-center">
                    <h3 class="mb-0"><?= $stats['cerradas'] ?></h3>
                    <small class="text-muted">Cerradas</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Incidencias -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-comments"></i> Todas las Incidencias</h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($incidencias)): ?>
                <div class="p-5 text-center text-muted">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>No hay incidencias registradas en la comunidad.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;">ID</th>
                                <th style="width: 25%;">Título</th>
                                <th style="width: 30%;">Descripción</th>
                                <th style="width: 12%;">Autor</th>
                                <th style="width: 10%;">Estado</th>
                                <th style="width: 10%;">Fecha</th>
                                <th style="width: 8%;" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($incidencias as $inc): ?>
                            <tr>
                                <td><strong>#<?= $inc['idIncidencia'] ?></strong></td>
                                <td>
                                    <strong><?= htmlspecialchars($inc['Titulo']) ?></strong>
                                </td>
                                <td>
                                    <div class="descripcion-preview small text-muted">
                                        <?= htmlspecialchars($inc['Descripcion']) ?>
                                    </div>
                                </td>
                                <td>
                                    <small>
                                        <?php if ($inc['Nombre']): ?>
                                            <?= htmlspecialchars($inc['Nombre'] . ' ' . $inc['ApellidoP']) ?>
                                            <br><span class="text-muted"><?= htmlspecialchars($inc['Email']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">Usuario eliminado</span>
                                        <?php endif; ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge <?= getBadgeClass($inc['Estado']) ?>">
                                        <?= $inc['Estado'] ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= timeAgo($inc['FechaRegistro']) ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEditar<?= $inc['idIncidencia'] ?>"
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEliminar<?= $inc['idIncidencia'] ?>"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Editar Estado -->
                            <div class="modal fade" id="modalEditar<?= $inc['idIncidencia'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Editar Estado - #<?= $inc['idIncidencia'] ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="post" id="formEditar<?= $inc['idIncidencia'] ?>">
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="<?= $inc['idIncidencia'] ?>">
                                                <input type="hidden" name="actualizar_estado" value="1">
                                                
                                                <div class="mb-3">
                                                    <label class="form-label"><strong>Título:</strong></label>
                                                    <p class="text-muted"><?= htmlspecialchars($inc['Titulo']) ?></p>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="estado<?= $inc['idIncidencia'] ?>" class="form-label">
                                                        <i class="fas fa-flag"></i> Nuevo Estado
                                                    </label>
                                                    <select class="form-select" name="estado" id="estado<?= $inc['idIncidencia'] ?>" required>
                                                        <option value="ABIERTA" <?= $inc['Estado'] === 'ABIERTA' ? 'selected' : '' ?>>Abierta</option>
                                                        <option value="EN_PROGRESO" <?= $inc['Estado'] === 'EN_PROGRESO' ? 'selected' : '' ?>>En Progreso</option>
                                                        <option value="RESUELTA" <?= $inc['Estado'] === 'RESUELTA' ? 'selected' : '' ?>>Resuelta</option>
                                                        <option value="CERRADA" <?= $inc['Estado'] === 'CERRADA' ? 'selected' : '' ?>>Cerrada</option>
                                                    </select>
                                                </div>

                                                <div class="alert alert-info small mb-0">
                                                    <i class="fas fa-info-circle"></i> 
                                                    Cambiar el estado ayuda a organizar y dar seguimiento a las incidencias de la comunidad.
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save"></i> Actualizar Estado
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Eliminar -->
                            <div class="modal fade" id="modalEliminar<?= $inc['idIncidencia'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Confirmar Eliminación</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="post">
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="<?= $inc['idIncidencia'] ?>">
                                                <input type="hidden" name="eliminar" value="1">
                                                
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    <strong>¿Está seguro que desea eliminar esta incidencia?</strong>
                                                </div>

                                                <p><strong>Título:</strong> <?= htmlspecialchars($inc['Titulo']) ?></p>
                                                <p><strong>Autor:</strong> <?= htmlspecialchars($inc['Nombre'] . ' ' . $inc['ApellidoP']) ?></p>
                                                <p class="text-muted small mb-0">Esta acción no se puede deshacer.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="fas fa-trash"></i> Eliminar Definitivamente
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
