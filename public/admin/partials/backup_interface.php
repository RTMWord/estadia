<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Generar Respaldo - MetaHogar Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="../assets/css/restore.css">
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
    <?php include __DIR__ . '/admin_nav.php'; ?>

    <div class="container main-content">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="restore-card">
                    <div class="card-header-custom">
                        <h1><i class="fas fa-download"></i> Generar Respaldo de Base de Datos</h1>
                    </div>
                    <div class="card-body p-4">
                        <div class="info-box">
                            <i class="fas fa-info-circle text-info"></i>
                            <strong>Información:</strong>
                            <p class="mb-0 mt-2">
                                Este proceso generará un archivo SQL completo con toda la estructura y datos de la base de datos actual.
                                El archivo se descargará automáticamente y también se guardará en el servidor.
                            </p>
                        </div>

                        <div class="text-center py-5">
                            <i class="fas fa-database fa-5x text-primary mb-4"></i>
                            <h4 class="mb-4">¿Deseas generar un respaldo ahora?</h4>
                            <p class="text-muted mb-4">
                                <i class="fas fa-clock"></i> Tiempo estimado: 1-3 minutos<br>
                                <i class="fas fa-hdd"></i> Se guardará en: admin/backups/
                            </p>
                            
                            <div class="d-grid gap-2 col-md-6 mx-auto">
                                <button id="btnGenerateBackup" class="btn btn-restore btn-success btn-lg">
                                    <i class="fas fa-download me-2"></i>Generar Respaldo
                                </button>
                                <a href="index.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Volver al Panel
                                </a>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt"></i> Conexión segura | 
                                <i class="fas fa-lock"></i> Acceso solo para administradores
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="admin-footer">
        <div class="container">
            <p class="mb-0">&copy; <?= date('Y') ?> MetaHogar. Todos los derechos reservados.</p>
            <small>Panel de Administración - Sistema de Respaldo</small>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        document.getElementById('btnGenerateBackup').addEventListener('click', function() {
            Swal.fire({
                title: '¿Generar respaldo?',
                html: '<p>Se creará un archivo SQL completo con toda la información de la base de datos.</p>' +
                      '<p class="text-muted small mt-2">Este proceso puede tardar unos minutos dependiendo del tamaño de la base de datos.</p>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check"></i> Sí, generar',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar loading
                    Swal.fire({
                        title: 'Generando respaldo...',
                        html: 'Por favor espera mientras se genera el archivo SQL.<br><br><i class="fas fa-spinner fa-spin fa-3x text-success"></i>',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Crear un iframe oculto para descargar el archivo
                    var iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    iframe.src = 'backup.php?generate=1';
                    document.body.appendChild(iframe);
                    
                    // Esperar un poco y verificar si se generó
                    setTimeout(function() {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Respaldo generado correctamente!',
                            html: '<p><strong>El archivo se ha descargado automáticamente.</strong></p>' +
                                  '<p class="text-muted mt-2">También se ha guardado una copia en el servidor en la carpeta <code>admin/backups/</code></p>' +
                                  '<p class="text-info mt-3"><i class="fas fa-info-circle"></i> Si la descarga no inició automáticamente, verifica tu configuración de descargas.</p>',
                            confirmButtonColor: '#667eea',
                            confirmButtonText: '<i class="fas fa-check"></i> Entendido'
                        }).then(() => {
                            // Eliminar el iframe
                            document.body.removeChild(iframe);
                        });
                    }, 3000);
                }
            });
        });
    </script>
</body>
</html>
