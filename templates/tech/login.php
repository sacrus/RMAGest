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
<?php
use RmaGest\Helpers;
$pageTitle = Helpers::__('login_tech_title');
$theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'dark';
$siteName = Helpers::getSetting('site_name', 'RMA Gest');
$faviconPath = Helpers::getSetting('favicon_path', '');
$faviconUrl = !empty($faviconPath) ? $faviconPath : '';
?>
<!DOCTYPE html>
<html lang="<?php echo Helpers::getActiveLanguage(); ?>" data-theme="<?php echo htmlspecialchars($theme); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - <?php echo htmlspecialchars($siteName); ?></title>
    
    <link rel="stylesheet" href="assets/css/style.css">
    <?php if (!empty($faviconUrl)): ?>
        <link rel="icon" href="<?php echo htmlspecialchars($faviconUrl); ?>">
    <?php endif; ?>
    
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: var(--bg-main);
            margin: 0;
            padding: 0;
            transition: background-color 0.3s ease;
        }
    </style>
</head>
<body>

<div style="max-width: 450px; width: 100%; padding: 0 16px; margin: 40px auto;">
    <!-- Cartão Premium de Login com Glassmorphism / Sombra Acentuada -->
    <div class="card" style="border: 1px solid var(--border-color); box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25); border-radius: var(--radius-md); overflow: hidden; position: relative; background: var(--bg-card); transition: all 0.3s ease;">
        
        <!-- Controlos do Topo Direito (Língua e Tema) -->
        <div style="position: absolute; top: 16px; right: 16px; z-index: 10; display: flex; align-items: center; gap: 8px;">
            <!-- Seletor de Língua -->
            <div style="display: inline-flex; align-items: center; gap: 2px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 2px; background: rgba(255, 255, 255, 0.02); height: 32px; box-sizing: border-box;">
                <a href="index.php?route=change-lang&lang=pt" style="padding: 0 8px; font-size: 0.75rem; font-weight: 700; text-decoration: none; border-radius: 4px; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; height: 100%; color: <?php echo Helpers::getActiveLanguage() === 'pt' ? 'var(--text-main)' : 'var(--text-muted)'; ?>; background: <?php echo Helpers::getActiveLanguage() === 'pt' ? 'var(--border-color)' : 'transparent'; ?>;" title="Português">
                    PT
                </a>
                <a href="index.php?route=change-lang&lang=en" style="padding: 0 8px; font-size: 0.75rem; font-weight: 700; text-decoration: none; border-radius: 4px; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; height: 100%; color: <?php echo Helpers::getActiveLanguage() === 'en' ? 'var(--text-main)' : 'var(--text-muted)'; ?>; background: <?php echo Helpers::getActiveLanguage() === 'en' ? 'var(--border-color)' : 'transparent'; ?>;" title="English">
                    EN
                </a>
            </div>

            <!-- Botão de Tema -->
            <button onclick="toggleTheme()" class="btn btn-secondary" style="padding: 6px 10px; font-size: 0.8rem; height: 32px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); cursor: pointer; display: flex; align-items: center; gap: 4px; background: rgba(255,255,255,0.03);" title="Alternar Tema">
                🌓
            </button>
        </div>

        <!-- Header com Logo Decorativo Estilo Premium -->
        <div style="text-align: center; padding: 35px 24px 20px 24px; border-bottom: 1px solid var(--border-color); background: rgba(255, 255, 255, 0.01);">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; background: var(--accent-gradient); border-radius: var(--radius-md); margin-bottom: 16px; color: #fff; font-size: 1.8rem; font-weight: 800; box-shadow: var(--accent-glow) 0 6px 20px;">
                RG
            </div>
            <h2 style="font-size: 1.6rem; font-weight: 800; margin-bottom: 8px; color: var(--text-main); letter-spacing: -0.025em;">
                <?php echo Helpers::__('login_tech_title'); ?>
            </h2>
            <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.4; max-width: 290px; margin: 0 auto;">
                <?php echo Helpers::__('login_subtitle'); ?>
            </p>
        </div>

        <div style="padding: 30px 24px 35px 24px;">
            <!-- Notificação de Erro com Estilo Premium -->
            <?php if (!empty($error)): ?>
                <div style="background-color: var(--color-error-bg); border: 1px solid var(--color-error); color: var(--color-error); padding: 12px 16px; border-radius: var(--radius-sm); margin-bottom: 24px; font-weight: 500; font-size: 0.88rem; display: flex; align-items: center; gap: 10px; line-height: 1.4;">
                    <span style="font-size: 1.1rem; line-height: 1;">⚠️</span>
                    <div><?php echo htmlspecialchars($error); ?></div>
                </div>
            <?php endif; ?>

            <!-- Formulário com transição suave nos inputs -->
            <form action="index.php?route=tech/login" method="post" style="display: flex; flex-direction: column; gap: 20px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="username" style="font-weight: 600; font-size: 0.88rem; color: var(--text-secondary); margin-bottom: 8px; display: block;">
                        👤 <?php echo Helpers::__('login_lbl_username'); ?>
                    </label>
                    <input type="text" name="username" id="username" class="form-control" required placeholder="admin" autocomplete="username" autofocus style="width: 100%; padding: 12px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: var(--bg-input); font-size: 0.95rem; color: var(--text-main); transition: all 0.2s;">
                </div>

                <div class="form-group" style="margin-bottom: 8px;">
                    <label class="form-label" for="password" style="font-weight: 600; font-size: 0.88rem; color: var(--text-secondary); margin-bottom: 8px; display: block;">
                        🔑 <?php echo Helpers::__('login_lbl_password'); ?>
                    </label>
                    <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••" autocomplete="current-password" style="width: 100%; padding: 12px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: var(--bg-input); font-size: 0.95rem; color: var(--text-main); transition: all 0.2s;">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 1rem; font-weight: 700; border-radius: var(--radius-sm); box-shadow: var(--accent-glow) 0 4px 12px; margin-top: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;" id="btn-login-submit">
                    <?php echo Helpers::__('login_btn'); ?> &rarr;
                </button>
            </form>
        </div>
    </div>
    
    <!-- Botão de Voltar com Estilo Discreto -->
    <div style="text-align: center; margin-top: 28px;">
        <a href="index.php" style="font-size: 0.88rem; color: var(--text-muted); text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 6px; transition: color 0.2s;" onmouseover="this.style.color='var(--text-secondary)'" onmouseout="this.style.color='var(--text-muted)'">
            &larr; <?php echo Helpers::__('login_back'); ?>
        </a>
    </div>
</div>

<script>
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
</body>
</html>
