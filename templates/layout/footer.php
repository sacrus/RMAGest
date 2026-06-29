<?php
/*
*****************************
*     Desenvolvido Por      *
*      Luis Patricio        *
*      www.lplogic.net      *
* Castelo Branco, Portugal  *
*****************************
*/
?>
    </main>

    <footer class="main-footer no-print">
        <div class="container" style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
            <p>&copy; <?php echo date('Y'); ?> <strong><?php echo htmlspecialchars($siteName); ?></strong> - <?php echo \RmaGest\Helpers::__('footer_copyright'); ?></p>
            <?php if (!\RmaGest\Auth::isLoggedIn()): ?>
                <div>
                    <a href="index.php?route=tech/login" style="color: var(--text-secondary); text-decoration: none; font-size: 0.85rem; opacity: 0.7; transition: opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">🔒 <?php echo \RmaGest\Helpers::__('nav_tech'); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </footer>

    <!-- Scripts Globais -->
    <script src="assets/js/app.js"></script>
    <script>
        // Função para alternar o tema Claro/Escuro de forma persistente
        function toggleTheme() {
            var html = document.documentElement;
            var currentTheme = html.getAttribute('data-theme');
            var newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            html.setAttribute('data-theme', newTheme);
            
            // Gravar nos cookies por 30 dias
            var d = new Date();
            d.setTime(d.getTime() + (30*24*60*60*1000));
            document.cookie = "theme=" + newTheme + ";expires=" + d.toUTCString() + ";path=/";
        }
    </script>

    <?php if (!\RmaGest\Auth::isLoggedIn()): ?>
    <!-- Google Translate Script -->
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'pt',
                autoDisplay: false
            }, 'google_translate_element');
        }

        // Ocultar agressivamente elementos indesejados do Google Translate
        (function() {
            const hideTranslateElements = () => {
                const banner = document.querySelector('.goog-te-banner-frame, iframe[class*="goog-te-banner-frame"], .VIpgJd-ZVi9od-ORHb-OEVmcd, iframe[class*="VIpgJd"]');
                if (banner) {
                    if (banner.style.display !== 'none' || banner.style.visibility !== 'hidden') {
                        banner.style.setProperty('display', 'none', 'important');
                        banner.style.setProperty('visibility', 'hidden', 'important');
                    }
                }

                const tooltip = document.getElementById('goog-gt-tt');
                if (tooltip && tooltip.style.display !== 'none') {
                    tooltip.style.setProperty('display', 'none', 'important');
                    tooltip.style.setProperty('visibility', 'hidden', 'important');
                }

                const balloon = document.querySelector('.goog-te-balloon-frame');
                if (balloon && balloon.style.display !== 'none') {
                    balloon.style.setProperty('display', 'none', 'important');
                    balloon.style.setProperty('visibility', 'hidden', 'important');
                }

                if (document.body && (document.body.style.top !== '0px' || document.body.style.marginTop !== '0px')) {
                    document.body.style.setProperty('top', '0px', 'important');
                    document.body.style.setProperty('margin-top', '0px', 'important');
                }
                if (document.documentElement && (document.documentElement.style.top !== '0px' || document.documentElement.style.marginTop !== '0px')) {
                    document.documentElement.style.setProperty('top', '0px', 'important');
                    document.documentElement.style.setProperty('margin-top', '0px', 'important');
                }
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', hideTranslateElements);
            } else {
                hideTranslateElements();
            }

            const observer = new MutationObserver(hideTranslateElements);
            observer.observe(document.documentElement, {
                attributes: true,
                childList: true,
                subtree: true,
                attributeFilter: ['style', 'class']
            });
        })();
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <?php endif; ?>
</body>
</html>

