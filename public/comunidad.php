<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/helpers/auth.php';

$isAdmin = false;
$isProveedor = false;
if (isLogged()) {
    $stmtRole = $pdo->prepare('SELECT r.Nombre FROM UsuarioRol ur JOIN Rol r ON ur.Rol_idRol = r.idRol WHERE ur.Usuario_idUsuario = ? LIMIT 1');
    $stmtRole->execute([getUserId()]);
    $roleName = $stmtRole->fetchColumn();
    $isAdmin = (strtolower($roleName) === 'administrador');
    $isProveedor = (strtolower($roleName) === 'proveedor');
}

// Helper: obtener preview de un URL usando meta OG, con cache de archivos (TTL 1 día)
function get_link_preview(string $url): array {
    $cacheDir = __DIR__ . '/../data/link_previews';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0755, true);
    }
    $cacheFile = $cacheDir . '/' . md5($url) . '.json';
    $ttl = 86400; // 24h
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $ttl)) {
        $data = json_decode(file_get_contents($cacheFile), true);
        if (is_array($data)) return $data;
    }

    $result = ['title'=>'', 'description'=>'', 'image'=>'', 'url'=>$url];
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 6,
        CURLOPT_USERAGENT => 'MetaHogar-LinkPreview/1.0 (+https://metahogar.local)'
    ]);
    $html = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($html && $httpCode >= 200 && $httpCode < 400) {
        // og:title
        if (preg_match('/<meta[^>]+property=["\']og:title["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m)) {
            $result['title'] = html_entity_decode(trim($m[1]));
        } elseif (preg_match('/<title>(.*?)<\/title>/is', $html, $m)) {
            $result['title'] = html_entity_decode(trim(strip_tags($m[1])));
        }
        // og:description or meta description
        if (preg_match('/<meta[^>]+property=["\']og:description["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m)) {
            $result['description'] = html_entity_decode(trim($m[1]));
        } elseif (preg_match('/<meta[^>]+name=["\']description["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m)) {
            $result['description'] = html_entity_decode(trim($m[1]));
        }
        // og:image
        if (preg_match('/<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m)) {
            $img = trim($m[1]);
            // resolve relative urls
            if (strpos($img, '//') === 0) $img = (parse_url($url, PHP_URL_SCHEME) ?: 'https') . ':' . $img;
            if (parse_url($img, PHP_URL_SCHEME) === null) {
                $base = rtrim($url, '/');
                $img = $base . '/' . ltrim($img, '/');
            }
            $result['image'] = $img;
        }
    }

    @file_put_contents($cacheFile, json_encode($result));
    return $result;
}

// Procesar nueva incidencia (comentario/pregunta simple)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLogged() && !isset($_POST['actualizar_estado']) && !isset($_POST['nueva_respuesta'])) {
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $tipo = trim($_POST['tipo'] ?? 'Publicación');
    $usuarioId = getUserId();
    
    if (!empty($titulo) && !empty($descripcion)) {
        try {
            // Guardado seguro: si la columna 'Tipo' existe, la incluimos, si no, usamos el esquema anterior
            $hasTipo = false;
            try {
                $stmtCol = $pdo->query("SHOW COLUMNS FROM Incidencia LIKE 'Tipo'");
                $hasTipo = $stmtCol && $stmtCol->rowCount() > 0;
            } catch (Exception $e) {
                $hasTipo = false;
            }

            if ($hasTipo) {
                $stmt = $pdo->prepare(
                    "INSERT INTO Incidencia (Usuario_idUsuario, Titulo, Descripcion, Tipo, Estado, FechaRegistro) VALUES (:usuario_id, :titulo, :descripcion, :tipo, 'ABIERTA', NOW())"
                );
                $stmt->execute([
                    ':usuario_id' => $usuarioId,
                    ':titulo' => $titulo,
                    ':descripcion' => $descripcion,
                    ':tipo' => $tipo
                ]);
            } else {
                $stmt = $pdo->prepare(
                    "INSERT INTO Incidencia (Usuario_idUsuario, Titulo, Descripcion, Estado, FechaRegistro) VALUES (:usuario_id, :titulo, :descripcion, 'ABIERTA', NOW())"
                );
                $stmt->execute([
                    ':usuario_id' => $usuarioId,
                    ':titulo' => $titulo,
                    ':descripcion' => $descripcion
                ]);
            }
            
            $_SESSION['mensaje'] = 'Publicación creada correctamente';
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_estado'])) {
    // Verificar a nivel servidor que el usuario es administrador
    requireRole($pdo, 'administrador');
    // si requireRole no sale, el usuario es admin
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

// Procesar nueva respuesta a una incidencia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nueva_respuesta']) && isLogged()) {
    $idIncidencia = intval($_POST['incidencia_id'] ?? 0);
    $cuerpo = trim($_POST['cuerpo_respuesta'] ?? '');
    $usuarioId = getUserId();

    if ($idIncidencia > 0 && !empty($cuerpo)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO respuesta_incidencia (Incidencia_idIncidencia, Usuario_idUsuario, Cuerpo) VALUES (:incidencia, :usuario, :cuerpo)");
            $stmt->execute([
                ':incidencia' => $idIncidencia,
                ':usuario' => $usuarioId,
                ':cuerpo' => $cuerpo
            ]);

            $_SESSION['mensaje'] = 'Respuesta publicada correctamente';
            header('Location: comunidad.php');
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al publicar respuesta: ' . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = 'Respuesta vacía o incidencia inválida';
    }
}

// Obtener todas las incidencias (ahora tratadas como publicaciones)
$stmt = $pdo->query("SELECT i.*, u.Nombre, u.ApellidoP, u.Email
    FROM Incidencia i
    LEFT JOIN Usuario u ON i.Usuario_idUsuario = u.idUsuario
    ORDER BY i.FechaRegistro DESC");
$incidencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contar estadísticas
$estadisticas = $pdo->query("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN Estado = 'ABIERTA' THEN 1 ELSE 0 END) as abiertas,
        SUM(CASE WHEN Estado = 'RESUELTA' THEN 1 ELSE 0 END) as resueltas,
        SUM(CASE WHEN Estado = 'CERRADA' THEN 1 ELSE 0 END) as cerradas
    FROM Incidencia")->fetch(PDO::FETCH_ASSOC);

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
    <link rel="stylesheet" href="assets/css/bs-navbar.css">
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
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        body { font-family: 'Inter', 'Segoe UI', sans-serif; }
        .stat-card { background: white; border-radius: 12px; padding: 18px; text-align: center; margin-bottom: 15px; box-shadow: 0 6px 18px rgba(15,23,42,0.06); }
        .stat-number { font-size: 2.1rem; font-weight: 700; color: #0b5ed7; }
        .stat-label { display:block; color:#6b7280; margin-top:6px; }
        .incidencia-card { background: white; border-radius: 12px; padding: 18px; margin-bottom: 16px; box-shadow: 0 6px 20px rgba(15,23,42,0.06); transition: transform .15s ease, box-shadow .15s ease; }
        .incidencia-card:hover { transform: translateY(-4px); box-shadow: 0 10px 30px rgba(15,23,42,0.08); }
        .titulo-incidencia { color: #0b5ed7; font-weight: 700; font-size: 1.05rem; margin-bottom: 6px; }
        .meta { font-size: 0.9rem; color: #6b7280; }
        .avatar { width:48px; height:48px; border-radius:50%; background:linear-gradient(135deg,#e6f0ff,#dbeafe); color:#0b5ed7; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:1rem; }
        .card-header-row{display:flex;align-items:center;gap:12px}
        .type-badge{padding:6px 10px;border-radius:999px;font-size:0.8rem;color:#fff;display:inline-block}
        .type-badge[data-type="Pregunta"]{background:#0d6efd}
        .type-badge[data-type="Compartir"]{background:#06b6d4}
        .type-badge[data-type="Aviso"]{background:#f59e0b}
        .type-badge[data-type="Discusión"]{background:#7c3aed}
        .post-excerpt{color:#374151}
        .post-actions{display:flex;gap:8px;align-items:center}
        .badge-abierta { background: #ffc107; color: #000; }
        .badge-en_progreso { background: #0dcaf0; color: #000; }
        .badge-resuelta { background: #198754; color: #fff; }
        .badge-cerrada { background: #6c757d; color: #fff; }
        .btn-ask { background: #17466e; color: white; padding: 12px 30px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-block; border: none; cursor: pointer; }
        .btn-ask:hover { background: #4b96c3; color: white; text-decoration: none; }
        .modal .form-hint { font-size: 0.9rem; color: #6c757d; }
        .info-box { background: #f0f7ff; border-left: 4px solid #17466e; padding: 12px; border-radius: 8px; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/partials/bs-navbar.php'; ?>

    <div class="hero text-center">
        <div class="container">
            <h1 class="display-5 fw-bold mb-2">Comunidad Digital</h1>
            <p class="lead mb-4">Comparte, pregunta, anuncia y colabora con la comunidad</p>
            <?php if (isLogged()): ?>
                <div class="d-flex justify-content-center align-items-center" style="gap:12px">
                    <button type="button" class="btn-ask" data-bs-toggle="modal" data-bs-target="#modalNuevaIncidencia">
                        <i class="fas fa-plus"></i> Crear Publicación
                    </button>
                    <?php if (!empty($isProveedor)): ?>
                        <a href="proveedor/index.php" class="btn btn-outline-light ms-2">Panel de Proveedor</a>
                    <?php endif; ?>
                </div>
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
                    <div class="d-flex align-items-center justify-content-center" style="gap:10px"><i class="fas fa-newspaper fa-2x" style="color:#0b5ed7"></i><div>
                        <div class="stat-number"><?= $estadisticas['total'] ?? 0 ?></div>
                        <span class="stat-label">Total</span>
                    </div></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-center" style="gap:10px"><i class="fas fa-folder-open fa-2x" style="color:#06b6d4"></i><div>
                        <div class="stat-number"><?= $estadisticas['abiertas'] ?? 0 ?></div>
                        <span class="stat-label">Abiertas</span>
                    </div></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-center" style="gap:10px"><i class="fas fa-check-circle fa-2x" style="color:#16a34a"></i><div>
                        <div class="stat-number"><?= $estadisticas['resueltas'] ?? 0 ?></div>
                        <span class="stat-label">Resueltas</span>
                    </div></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-center" style="gap:10px"><i class="fas fa-times-circle fa-2x" style="color:#6c757d"></i><div>
                        <div class="stat-number"><?= $estadisticas['cerradas'] ?? 0 ?></div>
                        <span class="stat-label">Cerradas</span>
                    </div></div>
                </div>
            </div>
        </div>

        <!-- Listado de Publicaciones -->
        <div class="row">
            <div class="col-lg-8">
                <h3 class="mb-4" style="color: #17466e;">Publicaciones de la Comunidad</h3>

                <?php if (empty($incidencias)): ?>
                    <div class="alert alert-info">
                        <p class="mb-0">No hay publicaciones todavía. ¡Sé el primero en compartir algo!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($incidencias as $inc): ?>
                        <?php $initials = strtoupper((!empty($inc['Nombre'])?substr($inc['Nombre'],0,1):'').(!empty($inc['ApellidoP'])?substr($inc['ApellidoP'],0,1):'')); ?>
                        <div class="incidencia-card">
                            <div class="card-header-row">
                                <div class="avatar"><?= htmlspecialchars($initials ?: 'U') ?></div>
                                <div style="flex:1">
                                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                                        <span class="type-badge" data-type="<?= htmlspecialchars($inc['Tipo'] ?? 'Publicación') ?>"><?= htmlspecialchars($inc['Tipo'] ?? 'Publicación') ?></span>
                                        <div class="titulo-incidencia"><?= htmlspecialchars($inc['Titulo']) ?></div>
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
                                    <p class="mt-2 mb-0 post-excerpt">
                                        <?= htmlspecialchars(mb_substr($inc['Descripcion'], 0, 180)) ?><?= (mb_strlen($inc['Descripcion'])>180)?'...':'' ?>
                                    </p>
                                    <?php
                                    // Detectar primer URL en la descripción y mostrar preview si hay metadatos
                                    if (preg_match('/https?:\/\/[\w\-\.\/%#?=&;:@+~]+/i', $inc['Descripcion'], $murl)) {
                                        $preview = get_link_preview($murl[0]);
                                        if (!empty($preview['title']) || !empty($preview['description']) || !empty($preview['image'])): ?>
                                            <div class="mt-3 link-preview" style="display:flex;gap:12px;align-items:flex-start;border:1px solid #eef2ff;padding:10px;border-radius:8px;background:#fbfdff">
                                                <?php if (!empty($preview['image'])): ?>
                                                    <a href="<?= htmlspecialchars($preview['url']) ?>" target="_blank" rel="noopener noreferrer"><img src="<?= htmlspecialchars($preview['image']) ?>" alt="" style="width:120px;height:80px;object-fit:cover;border-radius:6px"></a>
                                                <?php endif; ?>
                                                <div style="flex:1">
                                                    <a href="<?= htmlspecialchars($preview['url']) ?>" target="_blank" rel="noopener noreferrer" style="color:#0b5ed7;font-weight:600;text-decoration:none">
                                                        <?= htmlspecialchars($preview['title'] ?: $preview['url']) ?></a>
                                                    <div style="color:#374151;font-size:0.95rem;margin-top:6px"><?= htmlspecialchars(mb_substr($preview['description'],0,220)) ?></div>
                                                    <div style="margin-top:6px;font-size:0.8rem;color:#6b7280"><?= htmlspecialchars(parse_url($preview['url'], PHP_URL_HOST) ?: '') ?></div>
                                                </div>
                                            </div>
                                        <?php endif;
                                    }
                                    ?>
                                </div>
                                <div style="margin-left: 12px; text-align:right;">
                                    <div class="badge badge-<?= strtolower($inc['Estado']) ?>"><?= ucfirst(strtolower($inc['Estado'])) ?></div>
                                    <div class="mt-2 post-actions">
                                        <small class="text-muted"><?= htmlspecialchars($inc['Nombre'] ?? 'Usuario') ?></small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Botones de admin para cambiar estado -->
                            <?php if ($isAdmin): ?>
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

                            <?php
                            // Obtener respuestas para esta incidencia
                            $stmtR = $pdo->prepare("SELECT r.*, u.Nombre, u.ApellidoP, u.Email,
                                (SELECT COUNT(*) FROM usuariorol ur JOIN rol ro ON ur.Rol_idRol = ro.idRol WHERE ur.Usuario_idUsuario = r.Usuario_idUsuario AND ro.Nombre = 'administrador') AS es_admin
                                FROM respuesta_incidencia r
                                LEFT JOIN usuario u ON r.Usuario_idUsuario = u.idUsuario
                                WHERE r.Incidencia_idIncidencia = :id
                                ORDER BY r.Aceptada DESC, r.Puntos DESC, r.FechaCreacion ASC");
                            $stmtR->execute([':id' => $inc['idIncidencia']]);
                            $respuestas = $stmtR->fetchAll(PDO::FETCH_ASSOC);
                            ?>

                            <?php if (!empty($respuestas)): ?>
                                <div class="mt-3">
                                    <?php foreach ($respuestas as $res): ?>
                                        <div style="background:#f8f9fb; border-radius:8px; padding:12px; margin-bottom:10px;">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <strong><?= htmlspecialchars($res['Nombre'] . ' ' . ($res['ApellidoP'] ?? '')) ?></strong>
                                                    <?php if (!empty($res['es_admin'])): ?>
                                                        <span class="badge bg-success ms-2">Admin</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="mt-2" style="color:#333;"><?= nl2br(htmlspecialchars($res['Cuerpo'])) ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (isLogged()): ?>
                                <div class="mt-3">
                                    <form method="post">
                                        <input type="hidden" name="incidencia_id" value="<?= $inc['idIncidencia'] ?>">
                                        <div class="mb-2">
                                            <textarea name="cuerpo_respuesta" class="form-control form-control-sm" rows="3" placeholder="Escribe tu respuesta..." required minlength="2"></textarea>
                                        </div>
                                        <button type="submit" name="nueva_respuesta" class="btn btn-sm btn-outline-primary">Responder</button>
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
                    <h5 style="color: #17466e; margin-bottom: 15px;">Consejos</h5>
                    <ul class="small text-muted" style="list-style: none; padding: 0;">
                        <li class="mb-2">✓ Sé claro en tu publicación</li>
                        <li class="mb-2">✓ Proporciona contexto y detalles</li>
                        <li class="mb-2">✓ Usa el tipo adecuado (Pregunta, Compartir, Aviso, Discusión)</li>
                        <li class="mb-2">✓ Busca antes de publicar</li>
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
                        <h5 class="modal-title">Nueva Publicación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Título de la Publicación <span class="text-danger">*</span></label>
                                <input type="text" name="titulo" class="form-control form-control-lg" required 
                                       placeholder="Ej: Compartir: instalación de sensores" minlength="10" maxlength="200">
                                <div class="form-hint">Mínimo 10 caracteres. Sé específico y claro.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Descripción Detallada <span class="text-danger">*</span></label>
                                <textarea name="descripcion" class="form-control" rows="8" required
                                          placeholder="Describe tu publicación con detalle...\n- ¿Qué quieres compartir o preguntar?\n- ¿Qué contexto es relevante?" minlength="20" maxlength="5000"></textarea>
                                <div class="form-hint">Mínimo 20 caracteres. Cuanto más detalle, mejor.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tipo</label>
                                <select name="tipo" class="form-select">
                                    <option value="Pregunta">Pregunta</option>
                                    <option value="Compartir">Compartir</option>
                                    <option value="Aviso">Aviso</option>
                                    <option value="Discusión">Discusión</option>
                                </select>
                                <div class="form-hint">Selecciona el tipo de publicación para que la comunidad lo identifique.</div>
                            </div>
                            <div class="info-box small">
                                <strong>Consejos rápidos:</strong>
                                <ul class="mb-0 ps-3">
                                    <li>Título claro y concreto.</li>
                                    <li>Explica el contexto y lo que probaste.</li>
                                    <li>Incluye datos o capturas si aplica.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Publicar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</body>
</html>
