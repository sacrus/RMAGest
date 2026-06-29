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
/**
 * RMA Gest - Instalador Web Guiado (Multilanguage)
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$configFile = __DIR__ . '/config.php';

// Se já estiver instalado, redireciona para a página inicial
if (file_exists($configFile)) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/src/Database.php';

// ─── Dicionário de instalação (sem depender de Helpers/BD) ───────────────────
$installLangs = [
    'pt' => [
        'lang_name'          => 'PT',
        'page_title'         => 'Instalador RMA Gest',
        'subtitle'           => 'Instalação do Sistema de Gestão de Assistência',
        'step_label'         => 'Passo',
        'step1_title'        => '1. Base de Dados',
        'step1_desc'         => 'Escolha o motor de base de dados preferido. O SQLite é recomendado por não precisar de servidores adicionais. O MySQL é ideal para ambientes de rede estáveis.',
        'lbl_driver'         => 'Motor de Base de Dados',
        'opt_sqlite'         => 'SQLite (Recomendado - Ficheiro Local)',
        'opt_mysql'          => 'MySQL',
        'lbl_host'           => 'Endereço do Servidor (Host)',
        'lbl_port'           => 'Porta',
        'lbl_dbname'         => 'Nome da Base de Dados',
        'lbl_dbuser'         => 'Utilizador da Base de Dados',
        'lbl_dbpass'         => 'Senha da Base de Dados',
        'btn_next'           => 'Seguinte →',
        'step2_title'        => '2. Configurações do Sistema & Administrador',
        'step2_desc'         => 'Introduza o nome do seu negócio, carregue um logótipo e configure o administrador do sistema.',
        'lbl_site_name'      => 'Nome da Oficina / Empresa',
        'lbl_logo'           => 'Logótipo da Empresa (PNG, JPG, WEBP)',
        'sec_email'          => 'Configuração de Email',
        'lbl_smtp_enabled'   => 'Ativar Envio por SMTP (Preferencial)',
        'lbl_smtp_host'      => 'Servidor SMTP (Host)',
        'lbl_smtp_port'      => 'Porta SMTP',
        'lbl_smtp_enc'       => 'Segurança',
        'opt_tls'            => 'TLS (Recomendado)',
        'opt_ssl'            => 'SSL',
        'opt_none'           => 'Nenhuma',
        'lbl_smtp_user'      => 'Utilizador SMTP',
        'lbl_smtp_pass'      => 'Senha SMTP',
        'sec_admin'          => 'Conta do Administrador',
        'lbl_admin_name'     => 'Nome Completo',
        'lbl_admin_user'     => 'Nome de Utilizador (Username)',
        'lbl_admin_email'    => 'Email Principal (Usado para envio e recuperação)',
        'lbl_admin_pass'     => 'Senha do Administrador',
        'ph_admin_name'      => 'Ex: João Silva',
        'ph_admin_user'      => 'admin',
        'ph_admin_email'     => 'exemplo@gmail.com',
        'ph_smtp_host'       => 'smtp.exemplo.com',
        'ph_smtp_user'       => 'nome@exemplo.com',
        'btn_install'        => 'Instalar →',
        'step3_title'        => 'Instalado com Sucesso!',
        'step3_desc'         => 'O RMA Gest foi configurado e está pronto a funcionar. Por motivos de segurança, o ficheiro do instalador <strong>install.php</strong> continuará presente, mas desativado por ter sido gerado o <strong>config.php</strong>.',
        'btn_goto_site'      => 'Ir para o Website',
        'btn_goto_tech'      => 'Painel do Técnico',
        'err_db_empty'       => 'O nome da base de dados MySQL não pode estar vazio.',
        'err_conn'           => 'Erro de ligação: ',
        'err_admin_fields'   => 'Todos os campos do administrador são obrigatórios.',
        'err_save'           => 'Erro ao guardar configurações: ',
        'err_step1_missing'  => 'Sessão expirada. Por favor, reinicie o processo.',
        'ph_db_name'         => 'rma_gest',
        'ph_db_user'         => 'root',
        'lbl_restore_backup' => 'Restaurar de Cópia de Segurança (Opcional - .sql/.sqlite)',
        'lbl_restore_desc'   => 'Se pretender restaurar o sistema a partir de um backup, selecione o ficheiro de cópia de segurança.',
        'btn_choose_file'    => 'Escolher Ficheiro',
        'lbl_no_file_chosen' => 'Não foi escolhido nenhum ficheiro',
    ],
    'en' => [
        'lang_name'          => 'EN',
        'page_title'         => 'RMA Gest Installer',
        'subtitle'           => 'Repair Management System — Setup',
        'step_label'         => 'Step',
        'step1_title'        => '1. Database',
        'step1_desc'         => 'Choose your preferred database engine. SQLite is recommended as it requires no additional servers. MySQL is ideal for stable network environments.',
        'lbl_driver'         => 'Database Engine',
        'opt_sqlite'         => 'SQLite (Recommended - Local File)',
        'opt_mysql'          => 'MySQL',
        'lbl_host'           => 'Server Address (Host)',
        'lbl_port'           => 'Port',
        'lbl_dbname'         => 'Database Name',
        'lbl_dbuser'         => 'Database User',
        'lbl_dbpass'         => 'Database Password',
        'btn_next'           => 'Next →',
        'step2_title'        => '2. System Settings & Administrator',
        'step2_desc'         => 'Enter your business name, upload a logo, and configure the system administrator.',
        'lbl_site_name'      => 'Workshop / Company Name',
        'lbl_logo'           => 'Company Logo (PNG, JPG, WEBP)',
        'sec_email'          => 'Email Configuration',
        'lbl_smtp_enabled'   => 'Enable SMTP Sending (Recommended)',
        'lbl_smtp_host'      => 'SMTP Server (Host)',
        'lbl_smtp_port'      => 'SMTP Port',
        'lbl_smtp_enc'       => 'Security',
        'opt_tls'            => 'TLS (Recommended)',
        'opt_ssl'            => 'SSL',
        'opt_none'           => 'None',
        'lbl_smtp_user'      => 'SMTP Username',
        'lbl_smtp_pass'      => 'SMTP Password',
        'sec_admin'          => 'Administrator Account',
        'lbl_admin_name'     => 'Full Name',
        'lbl_admin_user'     => 'Username',
        'lbl_admin_email'    => 'Main Email (Used for sending and recovery)',
        'lbl_admin_pass'     => 'Administrator Password',
        'ph_admin_name'      => 'E.g. John Smith',
        'ph_admin_user'      => 'admin',
        'ph_admin_email'     => 'example@gmail.com',
        'ph_smtp_host'       => 'smtp.example.com',
        'ph_smtp_user'       => 'name@example.com',
        'btn_install'        => 'Install →',
        'step3_title'        => 'Successfully Installed!',
        'step3_desc'         => 'RMA Gest has been configured and is ready to use. For security reasons, the installer file <strong>install.php</strong> will remain but will be disabled since <strong>config.php</strong> has been generated.',
        'btn_goto_site'      => 'Go to Website',
        'btn_goto_tech'      => 'Technician Panel',
        'err_db_empty'       => 'The MySQL database name cannot be empty.',
        'err_conn'           => 'Connection error: ',
        'err_admin_fields'   => 'All administrator fields are required.',
        'err_save'           => 'Error saving settings: ',
        'err_step1_missing'  => 'Session expired. Please restart the process.',
        'ph_db_name'         => 'rma_gest',
        'ph_db_user'         => 'root',
        'lbl_restore_backup' => 'Restore from Backup (Optional - .sql/.sqlite)',
        'lbl_restore_desc'   => 'If you wish to restore the system from a backup, select the backup file.',
        'btn_choose_file'    => 'Choose File',
        'lbl_no_file_chosen' => 'No file chosen',
    ],
];

// ─── Gestão de idioma ─────────────────────────────────────────────────────────
session_start();

if (isset($_GET['lang']) && array_key_exists($_GET['lang'], $installLangs)) {
    $_SESSION['install_lang'] = $_GET['lang'];
}

$lang = $_SESSION['install_lang'] ?? 'pt';
if (!array_key_exists($lang, $installLangs)) {
    $lang = 'pt';
}

$t = $installLangs[$lang]; // shorthand translator array

// Helper: traduzir
function t(string $key): string {
    global $t;
    return $t[$key] ?? $key;
}

// Helper: URL com idioma preservado
function langUrl(string $base): string {
    global $lang;
    $sep = strpos($base, '?') !== false ? '&' : '?';
    return $base . $sep . 'lang=' . urlencode($lang);
}

// ─── Lógica de instalação ─────────────────────────────────────────────────────
$step  = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 1) {
        $driver = $_POST['driver'] ?? 'sqlite';

        $dbConfig = [
            'driver'   => $driver,
            'host'     => $_POST['host']     ?? '127.0.0.1',
            'port'     => $_POST['port']     ?? '3306',
            'database' => $_POST['database'] ?? '',
            'username' => $_POST['username'] ?? '',
            'password' => $_POST['password'] ?? '',
        ];

        if ($driver === 'sqlite') {
            $dbConfig['database'] = __DIR__ . '/src/rmagest.sqlite';
        }

        try {
            $backupUploaded = false;
            if (isset($_FILES['backup_file']) && $_FILES['backup_file']['error'] === UPLOAD_ERR_OK) {
                $backupUploaded = true;
                $backupTmp = $_FILES['backup_file']['tmp_name'];
            }

            if ($driver === 'sqlite') {
                if ($backupUploaded) {
                    $handle = fopen($backupTmp, 'rb');
                    $header = fread($handle, 16);
                    fclose($handle);
                    $isSqliteBinary = (strpos($header, "SQLite format 3\000") === 0);
                    
                    if ($isSqliteBinary) {
                        copy($backupTmp, $dbConfig['database']);
                        $pdo = new PDO('sqlite:' . $dbConfig['database']);
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    } else {
                        $pdo = new PDO('sqlite:' . $dbConfig['database']);
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        $pdo->exec('PRAGMA foreign_keys = OFF;');
                        $sql = file_get_contents($backupTmp);
                        $pdo->exec($sql);
                        $pdo->exec('PRAGMA foreign_keys = ON;');
                    }
                } else {
                    $pdo = new PDO('sqlite:' . $dbConfig['database']);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $pdo->exec('PRAGMA foreign_keys = ON;');
                    \RmaGest\Database::createSchema($pdo, $driver);
                }
            } else {
                if (empty($dbConfig['database'])) {
                    throw new Exception(t('err_db_empty'));
                }
                $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};charset=utf8mb4";
                $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbConfig['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $pdo->exec("USE `{$dbConfig['database']}`");
                
                if ($backupUploaded) {
                    $pdo->exec("SET FOREIGN_KEY_CHECKS=0;");
                    $sql = file_get_contents($backupTmp);
                    $pdo->exec($sql);
                    $pdo->exec("SET FOREIGN_KEY_CHECKS=1;");
                } else {
                    \RmaGest\Database::createSchema($pdo, $driver);
                }
            }

            if ($backupUploaded) {
                // Escrever config.php diretamente
                $dbPathCode = $dbConfig['driver'] === 'sqlite' 
                    ? "__DIR__ . '/src/rmagest.sqlite'" 
                    : "'" . addslashes($dbConfig['database']) . "'";

                $configContent  = "<?php\n";
                $configContent .= "return [\n";
                $configContent .= "    'db' => [\n";
                $configContent .= "        'driver'   => '" . addslashes($dbConfig['driver'])   . "',\n";
                $configContent .= "        'host'     => '" . addslashes($dbConfig['host'])     . "',\n";
                $configContent .= "        'port'     => '" . addslashes($dbConfig['port'])     . "',\n";
                $configContent .= "        'database' => " . $dbPathCode . ",\n";
                $configContent .= "        'username' => '" . addslashes($dbConfig['username']) . "',\n";
                $configContent .= "        'password' => '" . addslashes($dbConfig['password']) . "',\n";
                $configContent .= "    ]\n";
                $configContent .= "];\n";

                file_put_contents($configFile, $configContent);
                
                header('Location: ' . langUrl('install.php?step=3'));
                exit;
            } else {
                $_SESSION['temp_db'] = $dbConfig;
                header('Location: ' . langUrl('install.php?step=2'));
                exit;
            }
        } catch (\Exception $e) {
            $error = t('err_conn') . $e->getMessage();
        }

    } elseif ($step === 2) {
        $siteName      = $_POST['site_name']    ?? 'RMA Gest';
        $adminName     = $_POST['admin_name']   ?? '';
        $adminUser     = $_POST['admin_user']   ?? '';
        $adminPass     = $_POST['admin_pass']   ?? '';
        $adminEmail    = $_POST['admin_email']  ?? '';
        $smtpEnabled   = isset($_POST['smtp_enabled']) ? '1' : '0';
        $smtpHost      = $_POST['smtp_host']    ?? '';
        $smtpPort      = $_POST['smtp_port']    ?? '587';
        $smtpUser      = $_POST['smtp_user']    ?? '';
        $smtpPass      = $_POST['smtp_pass']    ?? '';
        $smtpEncryption = $_POST['smtp_encryption'] ?? 'tls';

        if (empty($adminName) || empty($adminUser) || empty($adminPass) || empty($adminEmail)) {
            $error = t('err_admin_fields');
        } else {
            $dbConfig = $_SESSION['temp_db'] ?? null;
            if (!$dbConfig) {
                header('Location: ' . langUrl('install.php?step=1'));
                exit;
            }

            try {
                if ($dbConfig['driver'] === 'sqlite') {
                    $pdo = new PDO('sqlite:' . $dbConfig['database']);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $pdo->exec('PRAGMA foreign_keys = ON;');
                } else {
                    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
                    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                }

                $settings = [
                    'site_name'       => $siteName,
                    'smtp_enabled'    => $smtpEnabled,
                    'smtp_host'       => $smtpHost,
                    'smtp_port'       => $smtpPort,
                    'smtp_user'       => $smtpUser,
                    'smtp_pass'       => $smtpPass,
                    'smtp_encryption' => $smtpEncryption,
                    'smtp_from_email' => $adminEmail,
                    'smtp_from_name'  => $siteName,
                    'logo_path'       => '',
                ];

                $driver = $dbConfig['driver'];
                foreach ($settings as $k => $v) {
                    if ($driver === 'sqlite') {
                        $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES (?, ?)");
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
                    }
                    $stmt->execute([$k, $v]);
                }

                // Logótipo opcional
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                    $exts  = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/gif' => 'gif', 'image/webp' => 'webp'];
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mime  = $finfo->file($_FILES['logo']['tmp_name']);

                    if (array_key_exists($mime, $exts)) {
                        $logoDir = __DIR__ . '/assets/uploads/logos';
                        if (!is_dir($logoDir)) {
                            mkdir($logoDir, 0755, true);
                        }
                        $logoName = 'logo.' . $exts[$mime];
                        if (move_uploaded_file($_FILES['logo']['tmp_name'], $logoDir . '/' . $logoName)) {
                            $logoPath = 'assets/uploads/logos/' . $logoName;
                            if ($driver === 'sqlite') {
                                $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('logo_path', ?)");
                            } else {
                                $stmt = $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES ('logo_path', ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
                            }
                            $stmt->execute([$logoPath]);
                        }
                    }
                }

                // Criar utilizador admin
                $passHash = password_hash($adminPass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, username, password_hash, email, role, permissions) VALUES (?, ?, ?, ?, 'admin', ?)");
                $stmt->execute([$adminName, $adminUser, $passHash, $adminEmail, json_encode([])]);

                // Escrever config.php
                $dbPathCode = $dbConfig['driver'] === 'sqlite' 
                    ? "__DIR__ . '/src/rmagest.sqlite'" 
                    : "'" . addslashes($dbConfig['database']) . "'";

                $configContent  = "<?php\n";
                $configContent .= "return [\n";
                $configContent .= "    'db' => [\n";
                $configContent .= "        'driver'   => '" . addslashes($dbConfig['driver'])   . "',\n";
                $configContent .= "        'host'     => '" . addslashes($dbConfig['host'])     . "',\n";
                $configContent .= "        'port'     => '" . addslashes($dbConfig['port'])     . "',\n";
                $configContent .= "        'database' => " . $dbPathCode . ",\n";
                $configContent .= "        'username' => '" . addslashes($dbConfig['username']) . "',\n";
                $configContent .= "        'password' => '" . addslashes($dbConfig['password']) . "',\n";
                $configContent .= "    ]\n";
                $configContent .= "];\n";

                file_put_contents($configFile, $configContent);

                unset($_SESSION['temp_db']);
                $step = 3;

            } catch (\Exception $e) {
                $error = t('err_save') . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang); ?>" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(t('page_title')); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 40px 16px;
            background: linear-gradient(135deg, #0b0f19 0%, #1e1b4b 100%);
        }
        .installer-container {
            width: 100%;
            max-width: 620px;
            margin: auto;
        }
        .step-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 30px;
        }
        .step-badge {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
            color: var(--text-secondary);
            transition: background 0.3s;
        }
        .step-badge.active {
            background: var(--accent-gradient);
            color: #fff;
            box-shadow: var(--accent-glow) 0 0 10px;
        }
        .step-connector {
            flex: 1;
            height: 2px;
            background-color: var(--border-color);
            max-width: 60px;
            border-radius: 2px;
        }
        .error-box {
            background-color: var(--color-error-bg);
            border: 1px solid var(--color-error);
            color: var(--color-error);
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .smtp-settings {
            display: none;
            border-left: 2px solid var(--border-color);
            padding-left: 16px;
            margin-top: 10px;
        }
        .lang-switcher {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-bottom: 20px;
        }
        .lang-btn {
            padding: 4px 12px;
            border-radius: var(--radius-sm);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            border: 1px solid var(--border-color);
            background: transparent;
            color: var(--text-secondary);
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .lang-btn:hover {
            background: var(--bg-card);
            color: var(--text-main);
        }
        .lang-btn.active-lang {
            background: var(--accent-gradient);
            color: #fff;
            border-color: transparent;
            box-shadow: 0 0 8px var(--accent-glow);
        }
        .step-label-text {
            font-size: 0.72rem;
            color: var(--text-muted);
            text-align: center;
            margin-top: -20px;
            margin-bottom: 20px;
            letter-spacing: 0.05em;
        }
    </style>
</head>
<body>
    <div class="installer-container">

        <!-- Seletor de idioma -->
        <div class="lang-switcher">
            <?php foreach ($installLangs as $code => $dict): ?>
                <a href="install.php?step=<?php echo $step; ?>&lang=<?php echo urlencode($code); ?>"
                   class="lang-btn <?php echo $lang === $code ? 'active-lang' : ''; ?>">
                    <?php echo htmlspecialchars($dict['lang_name']); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="card">
            <!-- Cabeçalho -->
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="display: inline-flex; align-items: center; justify-content: center; width: 60px; height: 60px; background: var(--accent-gradient); border-radius: var(--radius-md); margin-bottom: 12px; color:#fff; font-size: 1.8rem; font-weight:800; box-shadow:var(--accent-glow) 0 6px 15px;">RG</div>
                <h2>RMA Gest</h2>
                <p style="color: var(--text-secondary);"><?php echo htmlspecialchars(t('subtitle')); ?></p>
            </div>

            <!-- Indicador de passos -->
            <div class="step-indicator">
                <div class="step-badge <?php echo $step === 1 ? 'active' : ''; ?>">1</div>
                <div class="step-connector"></div>
                <div class="step-badge <?php echo $step === 2 ? 'active' : ''; ?>">2</div>
                <div class="step-connector"></div>
                <div class="step-badge <?php echo $step === 3 ? 'active' : ''; ?>">3</div>
            </div>

            <!-- Mensagem de erro -->
            <?php if (!empty($error)): ?>
                <div class="error-box">⚠️ <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($step === 1): ?>
            <!-- ══════════════════════════════════════════ PASSO 1 ══ -->
                <form action="<?php echo htmlspecialchars(langUrl('install.php?step=1')); ?>" method="post" enctype="multipart/form-data">
                    <h3><?php echo htmlspecialchars(t('step1_title')); ?></h3>
                    <p style="color: var(--text-secondary); margin-bottom: 20px; font-size:0.9rem; line-height:1.5;">
                        <?php echo htmlspecialchars(t('step1_desc')); ?>
                    </p>

                    <div class="form-group">
                        <label class="form-label"><?php echo htmlspecialchars(t('lbl_driver')); ?></label>
                        <select name="driver" id="db_driver" class="form-control" onchange="toggleDbFields()">
                            <option value="sqlite"><?php echo htmlspecialchars(t('opt_sqlite')); ?></option>
                            <option value="mysql"><?php echo htmlspecialchars(t('opt_mysql')); ?></option>
                        </select>
                    </div>

                    <div id="mysql_fields" style="display: none;">
                        <div class="form-group">
                            <label class="form-label"><?php echo htmlspecialchars(t('lbl_host')); ?></label>
                            <input type="text" name="host" class="form-control" value="127.0.0.1">
                        </div>
                        <div class="form-group">
                            <label class="form-label"><?php echo htmlspecialchars(t('lbl_port')); ?></label>
                            <input type="text" name="port" class="form-control" value="3306">
                        </div>
                        <div class="form-group">
                            <label class="form-label"><?php echo htmlspecialchars(t('lbl_dbname')); ?></label>
                            <input type="text" name="database" class="form-control" placeholder="<?php echo htmlspecialchars(t('ph_db_name')); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label"><?php echo htmlspecialchars(t('lbl_dbuser')); ?></label>
                            <input type="text" name="username" class="form-control" placeholder="<?php echo htmlspecialchars(t('ph_db_user')); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label"><?php echo htmlspecialchars(t('lbl_dbpass')); ?></label>
                            <input type="password" name="password" class="form-control">
                        </div>
                    </div>

                    <!-- Restauro de Backup Opcional -->
                    <div style="margin-top: 20px; border-top: 1px solid var(--border-color); padding-top: 20px;">
                        <h4 style="margin-bottom: 8px; font-size: 0.95rem;"><?php echo htmlspecialchars(t('lbl_restore_backup')); ?></h4>
                        <p style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 12px;"><?php echo htmlspecialchars(t('lbl_restore_desc')); ?></p>
                        
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="file" name="backup_file" id="install_backup_file" style="display: none;" accept=".sql,.sqlite,.db" onchange="document.getElementById('install-backup-file-name').textContent = this.files[0].name">
                            <label for="install_backup_file" class="btn btn-secondary" style="padding: 8px 14px; font-size: 0.82rem; height: 34px; cursor: pointer; margin-bottom: 0; display: inline-flex; align-items: center; gap: 6px;">
                                📁 <?php echo htmlspecialchars(t('btn_choose_file')); ?>
                            </label>
                            <span id="install-backup-file-name" style="font-size: 0.82rem; color: var(--text-secondary);"><?php echo htmlspecialchars(t('lbl_no_file_chosen')); ?></span>
                        </div>
                    </div>

                    <div style="text-align: right; margin-top: 30px;">
                        <button type="submit" class="btn btn-primary"><?php echo htmlspecialchars(t('btn_next')); ?></button>
                    </div>
                </form>

            <?php elseif ($step === 2): ?>
            <!-- ══════════════════════════════════════════ PASSO 2 ══ -->
                <form action="<?php echo htmlspecialchars(langUrl('install.php?step=2')); ?>" method="post" enctype="multipart/form-data">
                    <h3><?php echo htmlspecialchars(t('step2_title')); ?></h3>
                    <p style="color: var(--text-secondary); margin-bottom: 20px; font-size:0.9rem; line-height:1.5;">
                        <?php echo htmlspecialchars(t('step2_desc')); ?>
                    </p>

                    <div class="form-group">
                        <label class="form-label"><?php echo htmlspecialchars(t('lbl_site_name')); ?></label>
                        <input type="text" name="site_name" class="form-control" value="RMA Gest" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?php echo htmlspecialchars(t('lbl_logo')); ?></label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                    </div>

                    <!-- Email -->
                    <h4 style="margin-top: 30px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin-bottom:16px;">
                        <?php echo htmlspecialchars(t('sec_email')); ?>
                    </h4>
                    <div class="form-group" style="display:flex; align-items:center; gap: 10px;">
                        <input type="checkbox" name="smtp_enabled" id="smtp_enabled" value="1"
                               onchange="toggleSmtpFields()" style="width: 18px; height: 18px; cursor: pointer;">
                        <label for="smtp_enabled" class="form-label" style="margin-bottom:0; cursor: pointer;">
                            <?php echo htmlspecialchars(t('lbl_smtp_enabled')); ?>
                        </label>
                    </div>

                    <div id="smtp_fields" class="smtp-settings">
                        <div class="form-group">
                            <label class="form-label"><?php echo htmlspecialchars(t('lbl_smtp_host')); ?></label>
                            <input type="text" name="smtp_host" class="form-control" placeholder="<?php echo htmlspecialchars(t('ph_smtp_host')); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label"><?php echo htmlspecialchars(t('lbl_smtp_port')); ?></label>
                            <input type="text" name="smtp_port" class="form-control" value="587">
                        </div>
                        <div class="form-group">
                            <label class="form-label"><?php echo htmlspecialchars(t('lbl_smtp_enc')); ?></label>
                            <select name="smtp_encryption" class="form-control">
                                <option value="tls"><?php echo htmlspecialchars(t('opt_tls')); ?></option>
                                <option value="ssl"><?php echo htmlspecialchars(t('opt_ssl')); ?></option>
                                <option value="none"><?php echo htmlspecialchars(t('opt_none')); ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><?php echo htmlspecialchars(t('lbl_smtp_user')); ?></label>
                            <input type="text" name="smtp_user" class="form-control" placeholder="<?php echo htmlspecialchars(t('ph_smtp_user')); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label"><?php echo htmlspecialchars(t('lbl_smtp_pass')); ?></label>
                            <input type="password" name="smtp_pass" class="form-control">
                        </div>
                    </div>

                    <!-- Administrador -->
                    <h4 style="margin-top: 30px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin-bottom:16px;">
                        <?php echo htmlspecialchars(t('sec_admin')); ?>
                    </h4>
                    <div class="form-group">
                        <label class="form-label"><?php echo htmlspecialchars(t('lbl_admin_name')); ?></label>
                        <input type="text" name="admin_name" class="form-control" placeholder="<?php echo htmlspecialchars(t('ph_admin_name')); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?php echo htmlspecialchars(t('lbl_admin_user')); ?></label>
                        <input type="text" name="admin_user" class="form-control" placeholder="<?php echo htmlspecialchars(t('ph_admin_user')); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?php echo htmlspecialchars(t('lbl_admin_email')); ?></label>
                        <input type="email" name="admin_email" class="form-control" placeholder="<?php echo htmlspecialchars(t('ph_admin_email')); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?php echo htmlspecialchars(t('lbl_admin_pass')); ?></label>
                        <input type="password" name="admin_pass" class="form-control" required minlength="6">
                    </div>

                    <div style="text-align: right; margin-top: 30px;">
                        <button type="submit" class="btn btn-primary"><?php echo htmlspecialchars(t('btn_install')); ?></button>
                    </div>
                </form>

            <?php elseif ($step === 3): ?>
            <!-- ══════════════════════════════════════════ PASSO 3 ══ -->
                <div style="text-align: center; padding: 20px 0;">
                    <div style="color: var(--color-success); font-size: 4rem; margin-bottom: 20px;">✓</div>
                    <h3><?php echo htmlspecialchars(t('step3_title')); ?></h3>
                    <p style="color: var(--text-secondary); margin-bottom: 30px; font-size: 0.95rem; line-height: 1.6;">
                        <?php echo t('step3_desc'); /* HTML allowed here */ ?>
                    </p>
                    <div style="display: flex; flex-direction: column; gap:12px; max-width:250px; margin:0 auto;">
                        <a href="index.php" class="btn btn-primary"><?php echo htmlspecialchars(t('btn_goto_site')); ?></a>
                        <a href="index.php?route=tech/login" class="btn btn-secondary"><?php echo htmlspecialchars(t('btn_goto_tech')); ?></a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleDbFields() {
            var driver = document.getElementById('db_driver').value;
            var fields = document.getElementById('mysql_fields');
            fields.style.display = (driver === 'mysql') ? 'block' : 'none';
        }

        function toggleSmtpFields() {
            var enabled = document.getElementById('smtp_enabled').checked;
            var fields  = document.getElementById('smtp_fields');
            fields.style.display = enabled ? 'block' : 'none';
        }
    </script>
</body>
</html>

