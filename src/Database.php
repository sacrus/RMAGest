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

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    /**
     * Retorna a instância do PDO ativa, ou cria uma nova com base no config.php.
     */
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $configFile = dirname(__DIR__) . '/config.php';

            if (!file_exists($configFile)) {
                // Se o ficheiro de configuração não existe, redireciona para o instalador.
                if (basename($_SERVER['PHP_SELF']) !== 'install.php') {
                    header('Location: install.php');
                    exit;
                }
                throw new PDOException("Ficheiro de configuração não encontrado. Por favor, corra o instalador.");
            }

            $config = require $configFile;
            self::$instance = self::connect($config['db']);
            self::checkAndUpgradeSchema(self::$instance);
        }

        return self::$instance;
    }

    /**
     * Verifica e atualiza a estrutura da base de dados se necessário.
     */
    private static function checkAndUpgradeSchema(PDO $pdo) {
        try {
            $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
            
            // Verificar se a tabela rmas existe
            $tableExists = false;
            if ($driver === 'mysql') {
                $stmt = $pdo->query("SHOW TABLES LIKE 'rmas'");
                $tableExists = $stmt->rowCount() > 0;
            } else {
                $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='rmas'");
                $tableExists = $stmt->fetch() !== false;
            }

            if ($tableExists) {
                // Verificar se a coluna allow_sms_whatsapp existe
                $hasCol = false;
                if ($driver === 'mysql') {
                    $stmtCol = $pdo->query("SHOW COLUMNS FROM rmas LIKE 'allow_sms_whatsapp'");
                    $hasCol = $stmtCol->rowCount() > 0;
                } else {
                    $stmtCol = $pdo->query("PRAGMA table_info(rmas)");
                    while ($col = $stmtCol->fetch()) {
                        if ($col['name'] === 'allow_sms_whatsapp') {
                            $hasCol = true;
                            break;
                        }
                    }
                }

                if (!$hasCol) {
                    if ($driver === 'mysql') {
                        $pdo->exec("ALTER TABLE rmas ADD COLUMN allow_sms_whatsapp TINYINT DEFAULT 0");
                    } else {
                        $pdo->exec("ALTER TABLE rmas ADD COLUMN allow_sms_whatsapp INTEGER DEFAULT 0");
                    }
                }

                // Verificar se a coluna custom_form_data existe
                $hasCustomFormCol = false;
                if ($driver === 'mysql') {
                    $stmtCol = $pdo->query("SHOW COLUMNS FROM rmas LIKE 'custom_form_data'");
                    $hasCustomFormCol = $stmtCol->rowCount() > 0;
                } else {
                    $stmtCol = $pdo->query("PRAGMA table_info(rmas)");
                    while ($col = $stmtCol->fetch()) {
                        if ($col['name'] === 'custom_form_data') {
                            $hasCustomFormCol = true;
                            break;
                        }
                    }
                }

                if (!$hasCustomFormCol) {
                    if ($driver === 'mysql') {
                        $pdo->exec("ALTER TABLE rmas ADD COLUMN custom_form_data LONGTEXT NULL");
                    } else {
                        $pdo->exec("ALTER TABLE rmas ADD COLUMN custom_form_data TEXT NULL");
                    }
                }
            }

            // Verificar se a tabela predefined_responses existe
            $predefExists = false;
            if ($driver === 'mysql') {
                $stmt = $pdo->query("SHOW TABLES LIKE 'predefined_responses'");
                $predefExists = $stmt->rowCount() > 0;
            } else {
                $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='predefined_responses'");
                $predefExists = $stmt->fetch() !== false;
            }

            if (!$predefExists) {
                if ($driver === 'sqlite') {
                    $pdo->exec("
                        CREATE TABLE predefined_responses (
                            id INTEGER PRIMARY KEY AUTOINCREMENT,
                            title TEXT NOT NULL,
                            message TEXT NOT NULL
                        )
                    ");
                } else {
                    $pdo->exec("
                        CREATE TABLE predefined_responses (
                            `id` INT AUTO_INCREMENT PRIMARY KEY,
                            `title` VARCHAR(255) NOT NULL,
                            `message` TEXT NOT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                    ");
                }
            }
        } catch (\Exception $e) {
            // Ignorar erros caso ocorra algum problema na verificação
        }
    }

    /**
     * Estabelece ligação com a base de dados com base na configuração.
     */
    private static function connect(array $dbConfig): PDO {
        try {
            if ($dbConfig['driver'] === 'sqlite') {
                $dsn = 'sqlite:' . $dbConfig['database'];
                $pdo = new PDO($dsn, null, null, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
                // Ativar chaves estrangeiras no SQLite
                $pdo->exec('PRAGMA foreign_keys = ON;');
                return $pdo;
            } else {
                $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
                return new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            }
        } catch (PDOException $e) {
            die("Erro de ligação à Base de Dados: " . $e->getMessage());
        }
    }

    /**
     * Executa as migrações iniciais para criar a estrutura das tabelas.
     */
    public static function createSchema(PDO $pdo, string $driver): bool {
        $queries = [];

        if ($driver === 'sqlite') {
            $queries[] = "
                CREATE TABLE IF NOT EXISTS settings (
                    key TEXT PRIMARY KEY,
                    value TEXT NOT NULL
                );
            ";
            $queries[] = "
                CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    username TEXT NOT NULL UNIQUE,
                    password_hash TEXT NOT NULL,
                    email TEXT NOT NULL,
                    role TEXT NOT NULL DEFAULT 'tech', -- 'admin' ou 'tech'
                    permissions TEXT, -- JSON com as permissões
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );
            ";
            $queries[] = "
                CREATE TABLE IF NOT EXISTS rmas (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    rma_number TEXT NOT NULL UNIQUE,
                    access_code TEXT NOT NULL,
                    client_name TEXT,
                    client_email TEXT,
                    client_contact TEXT,
                    client_address TEXT,
                    device_type TEXT NOT NULL,
                    serial_number TEXT,
                    device_condition TEXT NOT NULL,
                    current_status TEXT NOT NULL,
                    tech_report TEXT,
                    budget_amount REAL DEFAULT 0.00,
                    budget_paid INTEGER DEFAULT 0, -- 0 = não pago, 1 = pago
                    allow_sms_whatsapp INTEGER DEFAULT 0,
                    custom_form_data TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );
            ";
            $queries[] = "
                CREATE TABLE IF NOT EXISTS rma_chat (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    rma_id INTEGER NOT NULL,
                    sender_type TEXT NOT NULL, -- 'client', 'tech', 'system'
                    sender_name TEXT NOT NULL,
                    message TEXT NOT NULL,
                    is_status_change INTEGER DEFAULT 0,
                    file_path TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (rma_id) REFERENCES rmas(id) ON DELETE CASCADE
                );
            ";
            $queries[] = "
                CREATE TABLE IF NOT EXISTS stock (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    sku TEXT NOT NULL UNIQUE,
                    quantity INTEGER NOT NULL DEFAULT 0,
                    price REAL NOT NULL DEFAULT 0.00,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );
            ";
            $queries[] = "
                CREATE TABLE IF NOT EXISTS rma_components (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    rma_id INTEGER NOT NULL,
                    stock_id INTEGER, -- NULL se for manual
                    component_name TEXT NOT NULL,
                    quantity INTEGER NOT NULL DEFAULT 1,
                    price_per_unit REAL NOT NULL DEFAULT 0.00,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (rma_id) REFERENCES rmas(id) ON DELETE CASCADE,
                    FOREIGN KEY (stock_id) REFERENCES stock(id) ON DELETE SET NULL
                );
            ";
            $queries[] = "
                CREATE TABLE IF NOT EXISTS predefined_responses (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title TEXT NOT NULL,
                    message TEXT NOT NULL
                );
            ";
        } else {
            // MySQL Schema
            $queries[] = "
                CREATE TABLE IF NOT EXISTS settings (
                    `key` VARCHAR(255) PRIMARY KEY,
                    `value` LONGTEXT NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            $queries[] = "
                CREATE TABLE IF NOT EXISTS users (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `name` VARCHAR(255) NOT NULL,
                    `username` VARCHAR(255) NOT NULL UNIQUE,
                    `password_hash` VARCHAR(255) NOT NULL,
                    `email` VARCHAR(255) NOT NULL,
                    `role` VARCHAR(50) NOT NULL DEFAULT 'tech',
                    `permissions` LONGTEXT NULL,
                    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            $queries[] = "
                CREATE TABLE IF NOT EXISTS rmas (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `rma_number` VARCHAR(50) NOT NULL UNIQUE,
                    `access_code` VARCHAR(50) NOT NULL,
                    `client_name` VARCHAR(255) NULL,
                    `client_email` VARCHAR(255) NULL,
                    `client_contact` VARCHAR(100) NULL,
                    `client_address` TEXT NULL,
                    `device_type` VARCHAR(255) NOT NULL,
                    `serial_number` VARCHAR(255) NULL,
                    `device_condition` TEXT NOT NULL,
                    `current_status` VARCHAR(100) NOT NULL,
                    `tech_report` TEXT NULL,
                    `budget_amount` DECIMAL(10,2) DEFAULT 0.00,
                    `budget_paid` TINYINT DEFAULT 0,
                    `allow_sms_whatsapp` TINYINT DEFAULT 0,
                    `custom_form_data` LONGTEXT NULL,
                    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            $queries[] = "
                CREATE TABLE IF NOT EXISTS rma_chat (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `rma_id` INT NOT NULL,
                    `sender_type` VARCHAR(50) NOT NULL,
                    `sender_name` VARCHAR(255) NOT NULL,
                    `message` TEXT NOT NULL,
                    `is_status_change` TINYINT DEFAULT 0,
                    `file_path` VARCHAR(255) NULL,
                    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (`rma_id`) REFERENCES rmas(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            $queries[] = "
                CREATE TABLE IF NOT EXISTS stock (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `name` VARCHAR(255) NOT NULL,
                    `sku` VARCHAR(100) NOT NULL UNIQUE,
                    `quantity` INT NOT NULL DEFAULT 0,
                    `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            $queries[] = "
                CREATE TABLE IF NOT EXISTS rma_components (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `rma_id` INT NOT NULL,
                    `stock_id` INT NULL,
                    `component_name` VARCHAR(255) NOT NULL,
                    `quantity` INT NOT NULL DEFAULT 1,
                    `price_per_unit` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (`rma_id`) REFERENCES rmas(`id`) ON DELETE CASCADE,
                    FOREIGN KEY (`stock_id`) REFERENCES stock(`id`) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            $queries[] = "
                CREATE TABLE IF NOT EXISTS predefined_responses (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `title` VARCHAR(255) NOT NULL,
                    `message` TEXT NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
        }

        foreach ($queries as $query) {
            $pdo->exec($query);
        }
        
        return true;
    }
}

