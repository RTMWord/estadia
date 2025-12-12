<?php
// public/includes/footer.php
// Evita el acceso directo
if (!defined('ESTADIA_INIT')) {
    // Si se carga fuera del contexto esperado, definimos BASE_URL por seguridad
    if (!defined('BASE_URL')) {
        define('BASE_URL', '/');
    }
}
?>

    </main>

    <!-- Footer CSS -->
    <link rel="stylesheet" href="assets/css/footer.css">

    <footer class="footer-gradient-blue">
        <div class="container footer-inner">
            <div class="footer-brand">
                <img src="assets/css/images/logo_white.png" alt="MetaHogar" class="footer-logo" />
                <p class="footer-desc">MetaHogar diseña hogares seguros e inteligentes para una longevidad más digna y confortable.</p>
                <div class="social-row">
                    <a class="social-link" href="#"><i class="fab fa-twitter fa-lg"></i></a>
                    <a class="social-link" href="#"><i class="fab fa-facebook-f fa-lg"></i></a>
                    <a class="social-link" href="#"><i class="fab fa-instagram fa-lg"></i></a>
                    <a class="social-link" href="#"><i class="fab fa-linkedin-in fa-lg"></i></a>
                </div>
            </div>

            <div class="footer-col">
                <h5>Dirección</h5>
                <ul class="footer-contact">
                    <li><i class="fas fa-map-marker-alt"></i> Av. Par Vial 10, Atlacomulco, 62560 Jiutepec, Mor.</li>
                    <li><i class="fas fa-phone"></i> +52 1 777 129 4253</li>
                    <li><i class="fas fa-phone"></i> +52 1 777 804 0747</li>
                    <li><i class="fas fa-envelope"></i> contacto@metahogar.com</li>
                </ul>
                <div class="social-row">
                    <a class="social-link" href="#"><i class="fab fa-twitter"></i></a>
                    <a class="social-link" href="#"><i class="fab fa-facebook-f"></i></a>
                    <a class="social-link" href="#"><i class="fab fa-instagram"></i></a>
                    <a class="social-link" href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <div class="footer-col">
                <h5>Boletín informativo</h5>
                <p>¡Mantente informado con nuestro boletín!</p>
                <div class="footer-newsletter">
                    <div class="newsletter-pill">
                        <input type="email" placeholder="Ingresa tu Email" aria-label="Ingresa tu Email" />
                        <button class="send-btn btn btn-sm btn-light" aria-label="Enviar boletín"><i class="fas fa-paper-plane"></i></button>
                    </div>
                </div>
            </div>

            <div class="footer-col footer-hero-icon" style="display:flex; align-items:center; justify-content:center;">
                <img src="assets/css/images/hero-ico-footer.png" alt="icono" />
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container d-flex align-items-center justify-content-between py-3">
                <div>&copy; <?= date('Y') ?> MetaHogar, All Right Reserved.</div>
                <div>
                    <a href="#">Home</a>
                    <a href="#">Cookies</a>
                    <a href="#">Help</a>
                    <a href="#">FAQs</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/navbar-sticky.js"></script>

</body>
</html>