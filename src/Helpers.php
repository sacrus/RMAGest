<?php
/*
*****************************
*     Desenvolvido Por      *
*      Luis Patricio        *
*      www.lplogic.net      *
* Castelo Branco, Portugal  *
*****************************
*/

namespace RmaGest;

use RmaGest\Database;
use PDO;

class Helpers {
    
    private static array $translations = [];
    private static string $currentLang = 'pt';

    /**
     * Inicializa o idioma ativo a partir de COOKIE, SESSION ou deteta o idioma do browser.
     */
    public static function initLanguage() {
        Auth::initSession();

        $route = $_GET['route'] ?? 'client/home';
        $isClientRoute = (strpos($route, 'tech/') !== 0 && $route !== 'change-lang');

        if ($isClientRoute) {
            self::$currentLang = 'pt';
        } else {
            if (isset($_GET['lang'])) {
                $lang = strtolower(trim($_GET['lang']));
                if (in_array($lang, ['pt', 'en'])) {
                    $_SESSION['lang'] = $lang;
                    setcookie('lang', $lang, time() + (30*24*60*60), '/');
                    self::$currentLang = $lang;
                }
            } elseif (isset($_SESSION['lang'])) {
                self::$currentLang = $_SESSION['lang'];
            } elseif (isset($_COOKIE['lang'])) {
                self::$currentLang = $_COOKIE['lang'];
            } else {
                // Detetar idioma do browser (se contiver 'en' escolhe 'en', senão por padrão 'pt')
                $browserLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'pt';
                $lang = (strpos($browserLang, 'en') === 0 || strpos($browserLang, ',en') !== false) ? 'en' : 'pt';
                self::$currentLang = $lang;
                $_SESSION['lang'] = $lang;
            }
        }

        // Carregar dicionário correspondente
        $langFile = dirname(__DIR__) . "/lang/" . self::$currentLang . ".php";
        if (file_exists($langFile)) {
            self::$translations = require $langFile;
        }
    }

    /**
     * Retorna a tradução associada à chave fornecida.
     */
    public static function __($key) {
        if (empty(self::$translations)) {
            self::initLanguage();
        }
        return self::$translations[$key] ?? $key;
    }

    /**
     * Retorna a sigla da linguagem ativa ('pt' ou 'en').
     */
    public static function getActiveLanguage(): string {
        if (empty(self::$translations)) {
            self::initLanguage();
        }
        return self::$currentLang;
    }
    
    /**
     * Higieniza dados de entrada para proteção XSS.
     */
    public static function sanitize($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        return htmlspecialchars(trim($data ?? ''), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Gera um código de Reparação único no formato REP-ANO-XXXXX (ex: REP-2026-98124).
     */
    public static function generateRmaNumber(): string {
        $db = Database::getInstance();
        $year = date('Y');
        
        do {
            $randomNum = mt_rand(10000, 99999);
            $rmaNumber = "REP-{$year}-{$randomNum}";
            
            $stmt = $db->prepare("SELECT COUNT(*) FROM rmas WHERE rma_number = ?");
            $stmt->execute([$rmaNumber]);
            $exists = $stmt->fetchColumn() > 0;
        } while ($exists);

        return $rmaNumber;
    }

    /**
     * Gera um código de acesso aleatório de 6 caracteres maiúsculos/números (ex: T8Y2K7).
     */
    public static function generateAccessCode(): string {
        $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ'; // Evita 0, 1, O, I para leitura fácil
        $code = '';
        for ($i = 0; $i < 6; $i++) {
            $code .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $code;
    }

    /**
     * Obtém uma definição da base de dados.
     */
    public static function getSetting(string $key, string $default = ''): string {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT value FROM settings WHERE key = ?");
            $stmt->execute([$key]);
            $value = $stmt->fetchColumn();
            return $value !== false ? $value : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * Grava ou atualiza uma definição na base de dados.
     */
    public static function setSetting(string $key, string $value): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES (?, ?)");
        
        // Se for MySQL, o INSERT OR REPLACE não existe diretamente. Usamos padrão compatível
        $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'mysql') {
            $stmt = $db->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
        }
        
        return $stmt->execute([$key, $value]);
    }

    /**
     * Obtém os estados de RMA configurados ou os padrão se não existirem.
     */
    public static function getStatuses(): array {
        $defaultStatuses = [
            "Em análise", 
            "Aguarda Receção", 
            "Recebido", 
            "Em Diagnóstico", 
            "Aguarda Cliente", 
            "Aguarda Fornecedor", 
            "Em Reparação", 
            "Reparado", 
            "Aguarda Pagamento", 
            "Devolvido Cliente", 
            "Finalizado"
        ];
        
        $json = self::getSetting('rma_statuses');
        if (empty($json)) {
            return $defaultStatuses;
        }
        
        $decoded = json_decode($json, true);
        return is_array($decoded) && !empty($decoded) ? $decoded : $defaultStatuses;
    }

    /**
     * Obtém os tipos de equipamento configurados ou os padrão se não existirem.
     */
    public static function getDeviceTypes(): array {
        $defaultTypes = [
            "Computador Desktop",
            "Portátil / Laptop",
            "Smartphone",
            "Tablet",
            "Consola de Jogos",
            "Smartwatch",
            "Componente (Placa Gráfica, Motherboard, etc.)",
            "Outro Equipamento"
        ];

        $json = self::getSetting('device_types');
        if (empty($json)) {
            return $defaultTypes;
        }

        $decoded = json_decode($json, true);
        return is_array($decoded) && !empty($decoded) ? $decoded : $defaultTypes;
    }

    /**
     * Renderiza uma vista PHP, passando dados opcionais e incluindo cabeçalhos/rodapés.
     */
    public static function renderView(string $viewName, array $data = [], bool $includeLayout = true) {
        // Extrai variáveis para estarem disponíveis na vista
        extract(self::sanitize($data)); 
        
        // Variável especial não limpa para HTML do Chat/Mensagens seguras
        $raw_data = $data; 

        $theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'dark';
        $siteName = self::getSetting('site_name', 'RMA Gest');
        
        $logoPath = self::getSetting('logo_path', '');
        $logoUrl = !empty($logoPath) ? $logoPath : '';

        $faviconPath = self::getSetting('favicon_path', '');
        $faviconUrl = !empty($faviconPath) ? $faviconPath : '';

        $viewFile = dirname(__DIR__) . "/templates/{$viewName}.php";
        
        if (!file_exists($viewFile)) {
            die("Vista templates/{$viewName}.php não encontrada.");
        }

        if ($includeLayout) {
            require dirname(__DIR__) . '/templates/layout/header.php';
        }
        
        require $viewFile;
        
        if ($includeLayout) {
            require dirname(__DIR__) . '/templates/layout/footer.php';
        }
    }

    /**
     * Faz upload seguro de ficheiros.
     */
    public static function uploadFile(array $file, string $subFolder = 'rmas'): ?string {
        if (!isset($file['error']) || is_array($file['error'])) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // Limitar tamanho a 10MB
        if ($file['size'] > 10 * 1024 * 1024) {
            return null;
        }

        // Validar tipo Mime
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        
        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        ];

        if (!array_key_exists($mime, $allowedTypes)) {
            return null;
        }

        $extension = $allowedTypes[$mime];
        
        // Criar nome único para evitar colisões
        $fileName = sprintf('%s_%s.%s', sha1_file($file['tmp_name']), uniqid(), $extension);
        
        $targetDir = dirname(__DIR__) . "/assets/uploads/{$subFolder}";
        
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $targetPath = $targetDir . '/' . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return "assets/uploads/{$subFolder}/{$fileName}";
        }

        return null;
    }
    
    /**
     * Retorna a badge correspondente ao estado atual.
     */
    public static function getStatusBadge(string $status): string {
        $statusLower = mb_strtolower($status);
        $translatedStatus = self::__($status);
        
        if (in_array($statusLower, ['reparado', 'finalizado', 'recebido'])) {
            return "<span class='badge badge-success'>{$translatedStatus}</span>";
        }
        
        if (in_array($statusLower, ['aguarda cliente', 'aguarda fornecedor', 'aguarda pagamento'])) {
            return "<span class='badge badge-warning'>{$translatedStatus}</span>";
        }
        
        if (in_array($statusLower, ['devolvido cliente', 'cancelado'])) {
            return "<span class='badge badge-danger'>{$translatedStatus}</span>";
        }
        
        return "<span class='badge badge-info'>{$translatedStatus}</span>";
    }

    /**
     * Cria um backup da base de dados (SQLite ou MySQL).
     */
    public static function createBackup(string $targetFolder = ''): ?string {
        try {
            $db = Database::getInstance();
            $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
            
            // Determinar a pasta de destino
            if (empty($targetFolder)) {
                $targetFolder = dirname(__DIR__); // raiz do site
            } else {
                $targetFolder = rtrim($targetFolder, '/\\');
                if (!is_dir($targetFolder)) {
                    if (!mkdir($targetFolder, 0755, true)) {
                        return null;
                    }
                }
            }
            
            $timestamp = date('Ymd_His');
            
            if ($driver === 'sqlite') {
                $configFile = dirname(__DIR__) . '/config.php';
                if (!file_exists($configFile)) {
                    return null;
                }
                $config = require $configFile;
                $dbFile = $config['db']['database'];
                
                $backupFile = $targetFolder . '/backup_' . $timestamp . '.sqlite';
                if (copy($dbFile, $backupFile)) {
                    return $backupFile;
                }
            } else {
                // MySQL: Gerar script SQL de dump em PHP puro
                $backupFile = $targetFolder . '/backup_' . $timestamp . '.sql';
                
                $tables = [];
                $stmt = $db->query("SHOW TABLES");
                while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                    $tables[] = $row[0];
                }
                
                $sql = "-- Database Backup\n";
                $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
                $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
                
                foreach ($tables as $table) {
                    $stmtCreate = $db->query("SHOW CREATE TABLE `{$table}`");
                    $rowCreate = $stmtCreate->fetch();
                    $sql .= $rowCreate['Create Table'] . ";\n\n";
                    
                    $stmtRows = $db->query("SELECT * FROM `{$table}`");
                    while ($row = $stmtRows->fetch(PDO::FETCH_ASSOC)) {
                        $keys = array_map(function($key) { return "`$key`"; }, array_keys($row));
                        $values = array_map(function($val) use ($db) {
                            if ($val === null) return "NULL";
                            return $db->quote($val);
                        }, array_values($row));
                        
                        $sql .= "INSERT INTO `{$table}` (" . implode(", ", $keys) . ") VALUES (" . implode(", ", $values) . ");\n";
                    }
                    $sql .= "\n";
                }
                
                $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
                
                if (file_put_contents($backupFile, $sql) !== false) {
                    return $backupFile;
                }
            }
        } catch (\Exception $e) {
            return null;
        }
        
        return null;
    }

    /**
     * Restaura uma cópia de segurança.
     */
    public static function restoreBackup(string $filePath, string $mode = 'full'): bool {
        try {
            $db = Database::getInstance();
            $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
            
            if ($driver === 'sqlite') {
                // Verificar se o backup é SQLite binário
                $handle = @fopen($filePath, 'rb');
                if ($handle) {
                    $header = fread($handle, 16);
                    fclose($handle);
                    $isSqliteBinary = (strpos($header, "SQLite format 3\000") === 0);
                    
                    if ($isSqliteBinary) {
                        if ($mode === 'full') {
                            // Fechar ligação PDO activa para libertar o bloqueio de ficheiro no Windows
                            self::$instance = null;
                            $db = null;
                            gc_collect_cycles();
                            
                            $configFile = dirname(__DIR__) . '/config.php';
                            $config = require $configFile;
                            $dbFile = $config['db']['database'];
                            
                            return copy($filePath, $dbFile);
                        } else {
                            // Modo de mesclagem (update)
                            $db->exec("ATTACH DATABASE '{$filePath}' AS backup_db");
                            
                            $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name != 'sqlite_sequence'");
                            $tables = [];
                            while ($row = $stmt->fetch()) {
                                $tables[] = $row['name'];
                            }
                            
                            $db->beginTransaction();
                            try {
                                foreach ($tables as $table) {
                                    $db->exec("INSERT OR REPLACE INTO `{$table}` SELECT * FROM backup_db.`{$table}`");
                                }
                                $db->commit();
                            } catch (\Exception $e) {
                                $db->rollBack();
                                $db->exec("DETACH DATABASE backup_db");
                                throw $e;
                            }
                            
                            $db->exec("DETACH DATABASE backup_db");
                            return true;
                        }
                    }
                }
            }
            
            // Se for um script SQL (.sql)
            $sqlContent = file_get_contents($filePath);
            if ($sqlContent === false) return false;
            
            if ($mode === 'full') {
                if ($driver === 'mysql') {
                    $db->exec("SET FOREIGN_KEY_CHECKS=0");
                    $stmt = $db->query("SHOW TABLES");
                    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                        $db->exec("DROP TABLE IF EXISTS `{$row[0]}`");
                    }
                    $db->exec("SET FOREIGN_KEY_CHECKS=1");
                } else {
                    $db->exec("PRAGMA foreign_keys = OFF");
                    $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name != 'sqlite_sequence'");
                    while ($row = $stmt->fetch()) {
                        $db->exec("DROP TABLE IF EXISTS `{$row['name']}`");
                    }
                    $db->exec("PRAGMA foreign_keys = ON");
                }
            }
            
            if ($driver === 'mysql') {
                $db->exec("SET FOREIGN_KEY_CHECKS=0");
            } else {
                $db->exec("PRAGMA foreign_keys = OFF");
            }
            
            if ($mode === 'update') {
                if ($driver === 'sqlite') {
                    $sqlContent = preg_replace('/^\s*INSERT INTO/im', 'INSERT OR REPLACE INTO', $sqlContent);
                } else {
                    $sqlContent = preg_replace('/^\s*INSERT INTO/im', 'REPLACE INTO', $sqlContent);
                }
            }
            
            $db->exec($sqlContent);
            
            if ($driver === 'mysql') {
                $db->exec("SET FOREIGN_KEY_CHECKS=1");
            } else {
                $db->exec("PRAGMA foreign_keys = ON");
            }
            return true;
        } catch (\Exception $e) {
            // Repor estados caso ocorra erro
            try {
                if (isset($db)) {
                    if ($driver === 'mysql') {
                        $db->exec("SET FOREIGN_KEY_CHECKS=1");
                    } else {
                        $db->exec("PRAGMA foreign_keys = ON");
                    }
                }
            } catch (\Exception $ex) {}
            throw $e;
        }
    }

    /**
     * Executa a verificação e criação do backup automático por Lazy Cron.
     */
    public static function checkAndRunLazyCron() {
        try {
            $enabled = self::getSetting('backup_cron_enabled', '0');
            if ($enabled !== '1') {
                return;
            }
            
            $frequency = self::getSetting('backup_cron_frequency', 'daily');
            $lastRun = self::getSetting('backup_cron_last_run', '');
            
            $interval = 86400; // diário
            if ($frequency === 'weekly') {
                $interval = 86400 * 7;
            } elseif ($frequency === 'monthly') {
                $interval = 86400 * 30;
            }
            
            $now = time();
            $lastRunTime = empty($lastRun) ? 0 : strtotime($lastRun);
            
            if (($now - $lastRunTime) >= $interval) {
                $folder = self::getSetting('backup_cron_folder', '');
                $result = self::createBackup($folder);
                if ($result) {
                    $db = Database::getInstance();
                    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
                    $nowStr = date('Y-m-d H:i:s');
                    if ($driver === 'sqlite') {
                        $stmt = $db->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('backup_cron_last_run', ?)");
                    } else {
                        $stmt = $db->prepare("INSERT INTO settings (`key`, `value`) VALUES ('backup_cron_last_run', ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
                    }
                    $stmt->execute([$nowStr]);
                }
            }
        } catch (\Exception $e) {
            // Falha silenciosa no Lazy Cron para não perturbar a navegação do utilizador
        }
    }
}

