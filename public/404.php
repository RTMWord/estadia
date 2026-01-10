<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/helpers/auth.php';

$user = null;
$userName = "Usuario";
$userRole = null;

if (isLogged()) {
    $stmt = $pdo->prepare('SELECT Nombre, ApellidoP FROM Usuario WHERE idUsuario = ? LIMIT 1');
    $stmt->execute([getUserId()]);
    $user = $stmt->fetch();
    if ($user) {
        $userName = $user['Nombre'];
    }
    
    $stmt = $pdo->prepare('SELECT r.Nombre FROM UsuarioRol ur JOIN Rol r ON ur.Rol_idRol = r.idRol WHERE ur.Usuario_idUsuario = ? LIMIT 1');
    $stmt->execute([getUserId()]);
    $userRole = $stmt->fetchColumn();
}

$reason = $_GET['reason'] ?? 'page_not_found';
$requested_page = $_GET['page'] ?? 'desconocida';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.userway.org/widget.js" data-account="kjnkkEfZy1"></script>
    <style>
        .userway-icon {
            position: fixed !important;
            right: 20px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            z-index: 9999 !important;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(180deg, #1f5f86 0%, #2b87aa 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }
        
        .error-container {
            text-align: center;
            color: white;
            padding: 20px;
        }
        
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            line-height: 1;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            margin-bottom: 20px;
            animation: float 3s ease-in-out infinite;
        }
        
        .error-icon {
            font-size: 5rem;
            margin-bottom: 20px;
            animation: swing 2s ease-in-out infinite;
        }
        
        .error-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .error-message {
            font-size: 1.2rem;
            margin-bottom: 30px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
            opacity: 0.95;
        }
        
        .user-info {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            border: 1px solid rgba(255,255,255,0.3);
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .user-greeting {
            font-size: 1.3rem;
            margin-bottom: 10px;
        }
        
        .user-role {
            font-size: 0.95rem;
            opacity: 0.9;
        }
        
        .permission-info {
            background: rgba(255, 193, 7, 0.2);
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
            text-align: left;
            font-size: 0.95rem;
        }
        
        .btn-group-404 {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .btn-404 {
            padding: 12px 30px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-404-primary {
            background: white;
            color: #667eea;
        }
        
        .btn-404-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            color: #667eea;
            text-decoration: none;
        }
        
        .btn-404-secondary {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid white;
        }
        
        .btn-404-secondary:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
            text-decoration: none;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        @keyframes swing {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(5deg); }
            75% { transform: rotate(-5deg); }
        }
        
        .footer-404 {
            margin-top: 50px;
            opacity: 0.8;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <?php if ($reason === 'insufficient_privileges'): ?>
                <i class="fas fa-lock"></i>
            <?php else: ?>
                <i class="fas fa-question-circle"></i>
            <?php endif; ?>
        </div>
        
        <div class="error-code">404</div>
        
        <div class="error-title">
            <?php if ($reason === 'insufficient_privileges'): ?>
                Acceso Denegado
            <?php else: ?>
                Página no encontrada
            <?php endif; ?>
        </div>
        
        <?php if (isLogged()): ?>
            <div class="user-info">
                <div class="user-greeting">
                    <i class="fas fa-user-circle"></i> Hola, <strong><?= htmlspecialchars($userName) ?></strong>
                </div>
                <?php if ($userRole): ?>
                    <div class="user-role">
                        <i class="fas fa-badge"></i> Rol: <strong><?= htmlspecialchars($userRole) ?></strong>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="error-message">
            <?php if ($reason === 'insufficient_privileges'): ?>
                No tienes permisos suficientes para acceder a esta página.
                <br><small style="opacity: 0.85;">Solo administradores pueden ver esta sección.</small>
            <?php else: ?>
                La página que buscas no existe o ha sido movida.
                <br><small style="opacity: 0.85;">Verifica la URL e intenta de nuevo.</small>
            <?php endif; ?>
        </div>
        
        <?php if ($reason === 'insufficient_privileges'): ?>
            <div class="permission-info">
                <i class="fas fa-info-circle"></i> <strong>Información de permisos:</strong>
                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                    <li>Solo los administradores pueden acceder a esta sección</li>
                    <li>Si necesitas acceso, contacta al administrador del sistema</li>
                    <li>Puedes continuar navegando en las secciones disponibles para tu rol</li>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="btn-group-404">
            <a href="index.php" class="btn-404 btn-404-primary">
                <i class="fas fa-home"></i> Ir al Inicio
            </a>
            
            <?php if (isLogged()): ?>
                <?php if ($userRole === 'administrador'): ?>
                    <a href="admin/index.php" class="btn-404 btn-404-secondary">
                        <i class="fas fa-chart-bar"></i> Panel Admin
                    </a>
                <?php else: ?>
                <?php endif; ?>
            <?php else: ?>
                <a href="login.php" class="btn-404 btn-404-secondary">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </a>
            <?php endif; ?>
        </div>
        
        <div class="footer-404">
            <p>
                <i class="fas fa-clock"></i> 
                <?= date('d/m/Y H:i:s') ?>
            </p>
        </div>
    </div>
</body>
</html>
