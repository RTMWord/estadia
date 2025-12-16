/**
 * restore.js - Lógica para el módulo de restauración de base de datos
 * Incluye validaciones, SweetAlert2 y manejo de archivos
 */

document.addEventListener('DOMContentLoaded', function() {
    const sqlFileInput = document.getElementById('sqlfile');
    const restoreForm = document.getElementById('restoreForm');
    const fileNameDisplay = document.getElementById('fileName');

    // Mostrar nombre y tamaño del archivo seleccionado
    if (sqlFileInput) {
        sqlFileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (!file) {
                fileNameDisplay.innerHTML = '';
                return;
            }

            const fileName = file.name;
            const fileSize = file.size;
            const fileSizeMB = (fileSize / (1024 * 1024)).toFixed(2);
            const fileExtension = fileName.split('.').pop().toLowerCase();

            // Validar extensión
            if (fileExtension !== 'sql') {
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo no válido',
                    text: 'Solo se permiten archivos con extensión .sql',
                    confirmButtonColor: '#667eea'
                });
                sqlFileInput.value = '';
                fileNameDisplay.innerHTML = '';
                return;
            }

            // Validar tamaño (50MB)
            const maxSizeMB = 50;
            if (fileSize > maxSizeMB * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo muy grande',
                    text: `El archivo excede el tamaño máximo permitido de ${maxSizeMB}MB`,
                    confirmButtonColor: '#667eea'
                });
                sqlFileInput.value = '';
                fileNameDisplay.innerHTML = '';
                return;
            }

            // Mostrar información del archivo válido
            fileNameDisplay.innerHTML = 
                `<i class="fas fa-file-code text-success"></i> ${fileName} <span class="text-muted">(${fileSizeMB} MB)</span>`;
        });
    }

    // Manejo del formulario con confirmación SweetAlert2
    if (restoreForm) {
        restoreForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validar que se haya seleccionado un archivo
            if (!sqlFileInput.files.length) {
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo no seleccionado',
                    html: `<div style="text-align:center">
                        <p><strong>No es posible realizar la restauración</strong></p>
                        <p class="text-muted mt-3">Debes subir un archivo <strong>.sql</strong> antes de poder continuar con el proceso de restauración.</p>
                        <hr class="my-3">
                        <div class="text-start">
                            <p class="mb-2"><i class="fas fa-info-circle text-info"></i> <strong>Pasos a seguir:</strong></p>
                            <ol class="text-muted">
                                <li>Haz clic en el área de carga arriba</li>
                                <li>Selecciona un archivo con extensión .sql</li>
                                <li>Verifica que el archivo sea válido</li>
                                <li>Intenta restaurar nuevamente</li>
                            </ol>
                        </div>
                    </div>`,
                    confirmButtonColor: '#667eea',
                    confirmButtonText: '<i class="fas fa-check"></i> Entendido',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
                return;
            }

            const file = sqlFileInput.files[0];
            const fileExtension = file.name.split('.').pop().toLowerCase();

            // Doble verificación de extensión
            if (fileExtension !== 'sql') {
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo no válido',
                    text: 'Solo se permiten archivos con extensión .sql',
                    confirmButtonColor: '#667eea'
                });
                return;
            }

            // Confirmación con advertencias
            Swal.fire({
                title: '¿Estás completamente seguro?',
                html: `<div style="text-align:left">
                    <p><strong>Esta acción:</strong></p>
                    <ul>
                        <li>Sobrescribirá <strong>TODOS</strong> los datos actuales</li>
                        <li>No se puede deshacer fácilmente</li>
                        <li>Puede tardar varios minutos</li>
                    </ul>
                    <p class="text-danger mt-3">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Asegúrate de tener un respaldo actualizado antes de continuar
                    </p>
                    <p class="text-info mt-2">
                        <strong>Archivo a restaurar:</strong> ${file.name}
                    </p>
                </div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check"></i> Sí, restaurar ahora',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    confirmButton: 'btn btn-danger btn-lg',
                    cancelButton: 'btn btn-secondary btn-lg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar spinner de carga
                    Swal.fire({
                        title: 'Restaurando base de datos...',
                        html: `
                            <p>Por favor espera. Este proceso puede tardar varios minutos.</p>
                            <div class="mt-4">
                                <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                            </div>
                            <p class="text-muted mt-3 small">No cierres esta ventana ni refresques la página</p>
                        `,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Enviar el formulario
                    e.target.submit();
                }
            });
        });
    }
});

/**
 * Función para mostrar alertas desde PHP
 * Se ejecuta cuando hay mensajes del servidor
 */
function mostrarAlerta(tipo, titulo, texto, timer = null) {
    Swal.fire({
        icon: tipo, // 'success', 'error', 'warning', 'info'
        title: titulo,
        text: texto,
        confirmButtonColor: '#667eea',
        timer: timer,
        showConfirmButton: true
    });
}
