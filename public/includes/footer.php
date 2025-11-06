<?php
// Evita el acceso directo
if (!defined('ESTADIA_INIT')) { exit('Acceso denegado'); }
?>
    <!-- El contenido principal de la página termina aquí -->
    <footer class="main-footer">
        <p>&copy; <?php echo date("Y"); ?> Estadía. Todos los derechos reservados.</p>
        <div class="footer-links">
            <a href="#">Política de Privacidad</a>
            <a href="#">Términos de Servicio</a>
        </div>
    </footer>

    <!-- Incluye el JS principal (si existe) -->
    <script src="assets/js/catalogo.js"></script> 
</body>
</html>