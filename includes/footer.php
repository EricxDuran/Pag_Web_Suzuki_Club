<div id="cookie-banner" style="display: none; position: fixed; bottom: 0; left: 0; width: 100%; background: #333; color: #fff; padding: 15px 20px; text-align: center; z-index: 9999; box-shadow: 0 -2px 10px rgba(0,0,0,0.5);">
    <p style="margin: 0 0 10px 0; font-size: 0.9rem; display: inline-block; margin-right: 15px; vertical-align: middle;">
        Esta web utiliza cookies técnicas necesarias para mantener tu sesión. 
        <a href="cookies.php" style="color: var(--accent); text-decoration: underline;">Más información</a>.
    </p>
    <div style="display: inline-block; vertical-align: middle;">
        <button id="btn-aceptar-cookies" class="btn" style="padding: 5px 15px; font-size: 0.9rem; cursor: pointer; margin-right: 10px;">Aceptar todas</button>
        <button id="btn-rechazar-cookies" class="btn btn-secondary" style="padding: 5px 15px; font-size: 0.9rem; cursor: pointer; background: transparent; border: 1px solid #777; color: #ccc;">Solo necesarias</button>
    </div>
</div>

<footer style="text-align: center;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <h3>Suzuki Samurai Team</h3>
        <p><?php echo isset($lang['footer_copy']) ? $lang['footer_copy'] : 'Suzuki Samurai Team'; ?></p>
        
        <div style="margin-top: 20px; font-size: 1.5rem;">
            <a href="https://www.instagram.com/suzukisamuraiteam" target="_blank" style="color: white; margin: 0 10px; text-decoration: none;">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="https://www.facebook.com/groups/673280500831158/" target="_blank" style="color: white; margin: 0 10px; text-decoration: none;">
                <i class="fab fa-facebook"></i>
            </a>
        </div>

        <div style="margin-top: 20px; font-size: 0.85rem;">
            <a href="privacidad.php" style="color: #bbb; margin: 0 10px;"><?php echo isset($lang['footer_privacidad']) ? $lang['footer_privacidad'] : 'Privacidad'; ?></a> |
            <a href="cookies.php" style="color: #bbb; margin: 0 10px;"><?php echo isset($lang['footer_cookies']) ? $lang['footer_cookies'] : 'Cookies'; ?></a> |
            <a href="terminos.php" style="color: #bbb; margin: 0 10px;"><?php echo isset($lang['footer_terminos']) ? $lang['footer_terminos'] : 'Términos'; ?></a>
        </div>

        <p style="font-size: 0.8rem; margin-top: 20px;">&copy; <?php echo date('Y'); ?></p>
    </div>
</footer>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- SCRIPT DE COOKIES ---
        // Comprobamos si ya hay una decisión tomada ("aceptadas" o "rechazadas")
        if (!localStorage.getItem("estado_cookies")) {
            document.getElementById("cookie-banner").style.display = "block";
        }

        // Botón Aceptar
        document.getElementById("btn-aceptar-cookies").addEventListener("click", function() {
            localStorage.setItem("estado_cookies", "aceptadas");
            document.getElementById("cookie-banner").style.display = "none";
        });

        // Botón Rechazar (Solo necesarias)
        document.getElementById("btn-rechazar-cookies").addEventListener("click", function() {
            localStorage.setItem("estado_cookies", "rechazadas");
            document.getElementById("cookie-banner").style.display = "none";
        });

        // --- SCRIPT PARA MANTENER LA POSICIÓN DEL SCROLL ---
        var scrollPos = sessionStorage.getItem('scrollPos');
        if (scrollPos) {
            window.scrollTo(0, scrollPos);
            sessionStorage.removeItem('scrollPos'); 
        }
    });

    function guardarPosicion() {
        sessionStorage.setItem('scrollPos', window.scrollY);
    }

    // Activar eventos scroll
    document.querySelectorAll('.lang-switch a, form, th a').forEach(el => {
        el.addEventListener('click', guardarPosicion);
        el.addEventListener('submit', guardarPosicion);
    });
</script>

</body>
</html>