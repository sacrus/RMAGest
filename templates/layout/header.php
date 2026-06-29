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
use RmaGest\Auth;
use RmaGest\Helpers;
?>
<!DOCTYPE html>
<html lang="<?php echo Helpers::getActiveLanguage(); ?>" data-theme="<?php echo htmlspecialchars($theme ?? 'dark'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'RMA Gest'); ?> - <?php echo htmlspecialchars($siteName); ?></title>
    
    <!-- Meta Tags para SEO -->
    <meta name="description" content="Gestão e consulta de pedidos de reparação e RMAs da oficina <?php echo htmlspecialchars($siteName); ?>.">
    <meta name="robots" content="index, follow">
    
    <link rel="stylesheet" href="assets/css/style.css">
    <?php if (!empty($faviconUrl)): ?>
        <link rel="icon" href="<?php echo htmlspecialchars($faviconUrl); ?>">
    <?php endif; ?>

    <?php if (!Auth::isLoggedIn()): ?>
    <!-- Estilos Customizados para o Google Translate Dropdown Selector -->
    <style>
        /* Ocultar barra superior, tooltips e realces do Google Translate */
        iframe.goog-te-banner-frame,
        .goog-te-banner-frame,
        .goog-te-banner-frame.skiptranslate,
        iframe[class*="goog-te-banner-frame"],
        .goog-te-banner,
        .VIpgJd-ZVi9od-ORHb-OEVmcd,
        iframe.VIpgJd-ZVi9od-ORHb-OEVmcd,
        iframe[class*="VIpgJd"],
        #goog-gt-tt,
        .goog-te-balloon-frame,
        .goog-tooltip,
        .goog-tooltip:hover {
            display: none !important;
            visibility: hidden !important;
        }
        .goog-text-highlight {
            background: none !important;
            box-shadow: none !important;
        }
        html {
            top: 0px !important;
            margin-top: 0px !important;
        }
        body {
            top: 0px !important;
            margin-top: 0px !important;
            position: static !important;
        }
        html.translated-ltr,
        html.translated-rtl,
        body.translated-ltr,
        body.translated-rtl {
            top: 0px !important;
            margin-top: 0px !important;
        }
        
        #google_translate_element select.goog-te-combo {
            background-color: var(--color-bg-card) !important;
            border: 1px solid var(--border-color) !important;
            padding: 6px 12px !important;
            padding-right: 32px !important;
            border-radius: var(--radius-sm) !important;
            font-family: inherit !important;
            font-size: 0.85rem !important;
            font-weight: 500 !important;
            color: var(--text-primary) !important;
            cursor: pointer !important;
            outline: none !important;
            transition: all 0.3s ease !important;
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'></polyline></svg>") !important;
            background-repeat: no-repeat !important;
            background-position: right 10px center !important;
        }
        #google_translate_element select.goog-te-combo:hover {
            border-color: var(--color-primary) !important;
        }
        #google_translate_element select.goog-te-combo option {
            background-color: var(--color-bg-card) !important;
            color: var(--text-primary) !important;
        }
        .goog-te-gadget {
            font-size: 0px !important;
        }
        .goog-te-gadget > * {
            font-size: 0.85rem !important;
        }
        .goog-te-gadget span {
            display: none !important;
        }
        .goog-logo-link {
            display: none !important;
        }
    </style>
    <?php endif; ?>
</head>
<body>
    <header class="main-header no-print">
        <div class="container header-container">
            <a href="index.php" class="logo-link" id="site-logo-container">
                <?php if (!empty($logoUrl)): ?>
                    <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" class="logo-img">
                <?php else: ?>
                    <div class="logo-icon">RG</div>
                    <span><?php echo htmlspecialchars($siteName); ?></span>
                <?php endif; ?>
            </a>
            
            <nav>
                <ul class="nav-menu">
                    <!-- Seletor de Idioma / Google Translate -->
                    <?php 
                    $currRoute = $_GET['route'] ?? 'client/home';
                    $isTechRoute = (strpos($currRoute, 'tech/') === 0);
                    if (Auth::isLoggedIn() && $isTechRoute): 
                    ?>
                        <li>
                            <div style="display: inline-flex; gap: 2px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); padding: 3px; border-radius: var(--radius-sm);">
                                <a href="index.php?route=change-lang&lang=pt" class="btn" style="padding: 4px 8px; font-size: 0.72rem; border-radius: 4px; min-width:32px; height: 26px; border: none; background: <?php echo Helpers::getActiveLanguage() === 'pt' ? 'var(--accent-gradient)' : 'transparent'; ?>; color: <?php echo Helpers::getActiveLanguage() === 'pt' ? '#fff' : 'var(--text-secondary)'; ?>;" title="Português">PT</a>
                                <a href="index.php?route=change-lang&lang=en" class="btn" style="padding: 4px 8px; font-size: 0.72rem; border-radius: 4px; min-width:32px; height: 26px; border: none; background: <?php echo Helpers::getActiveLanguage() === 'en' ? 'var(--accent-gradient)' : 'transparent'; ?>; color: <?php echo Helpers::getActiveLanguage() === 'en' ? '#fff' : 'var(--text-secondary)'; ?>;" title="English">EN</a>
                            </div>
                        </li>
                    <?php elseif (!$isTechRoute): ?>
                        <li>
                            <div id="google_translate_element"></div>
                        </li>
                    <?php endif; ?>

                    <!-- Tema Switcher -->
                    <li>
                        <button onclick="toggleTheme()" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.85rem; height: 32px;" id="theme-toggle-btn" title="<?php echo htmlspecialchars(Helpers::__('nav_theme_desc')); ?>">
                            🌓 <?php echo Helpers::__('nav_theme'); ?>
                        </button>
                    </li>
                    
                    <?php if (Auth::isLoggedIn()): ?>
                        <!-- Menu do Técnico -->
                        <li><a href="index.php?route=tech/dashboard" class="nav-link" id="nav-dashboard"><?php echo Helpers::__('nav_dashboard'); ?></a></li>
                        <li><a href="index.php?route=tech/stock" class="nav-link" id="nav-stock"><?php echo Helpers::__('nav_stock'); ?></a></li>
                        <li><a href="index.php?route=tech/reports" class="nav-link" id="nav-reports"><?php echo Helpers::__('nav_reports'); ?></a></li>
                        <li><a href="index.php?route=tech/settings" class="nav-link" id="nav-settings"><?php echo Helpers::__('nav_settings'); ?></a></li>
                        <li>
                            <a href="index.php?route=tech/logout" class="btn btn-secondary" style="padding: 6px 16px; font-size: 0.9rem;" id="btn-logout">
                                <?php echo Helpers::__('nav_logout'); ?> (<?php echo htmlspecialchars($_SESSION['user_name']); ?>)
                            </a>
                        </li>
                    <?php else: ?>
                        <!-- Menu Público -->
                        <li><a href="index.php" class="nav-link" id="nav-home"><?php echo Helpers::__('nav_search'); ?></a></li>
                        <li><a href="index.php?route=client/new-rma" class="btn btn-primary" style="padding: 8px 16px; font-size: 0.9rem;" id="btn-request-rma"><?php echo Helpers::__('nav_request'); ?></a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container" style="padding-top: 30px; padding-bottom: 50px;">

