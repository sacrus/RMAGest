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

class Auth {
    
    /**
     * Inicializa a sessão de forma segura se ainda não estiver iniciada.
     */
    public static function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            // Se estiver a correr sob HTTPS, forçar cookies seguros
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                ini_set('session.cookie_secure', 1);
            }
            session_start();
        }
    }

    /**
     * Tenta autenticar um utilizador (técnico/admin).
     */
    public static function login(string $username, string $password): bool {
        self::initSession();
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_permissions'] = json_decode($user['permissions'] ?? '[]', true);
            return true;
        }

        return false;
    }

    /**
     * Termina a sessão do utilizador.
     */
    public static function logout() {
        self::initSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    /**
     * Verifica se existe um utilizador autenticado no painel técnico.
     */
    public static function isLoggedIn(): bool {
        self::initSession();
        return isset($_SESSION['user_id']);
    }

    /**
     * Verifica se o utilizador logado é Administrador.
     */
    public static function isAdmin(): bool {
        self::initSession();
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    /**
     * Verifica se o utilizador atual possui uma permissão específica.
     * Administradores têm sempre acesso total a tudo.
     */
    public static function checkPermission(string $permission): bool {
        self::initSession();
        if (!self::isLoggedIn()) {
            return false;
        }
        
        // Administrador tem superpoderes automáticos
        if (self::isAdmin()) {
            return true;
        }

        $permissions = $_SESSION['user_permissions'] ?? [];
        return in_array($permission, $permissions);
    }

    /**
     * Retorna os dados completos do utilizador logado.
     */
    public static function getCurrentUser(): ?array {
        self::initSession();
        if (!self::isLoggedIn()) {
            return null;
        }

        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT id, name, username, email, role, permissions FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            if ($user) {
                $user['permissions'] = json_decode($user['permissions'] ?? '[]', true);
                return $user;
            }
        } catch (\Exception $e) {
            // Se der erro ao ler a BD (ex: no instalador), retorna dados de sessão
        }

        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'username' => $_SESSION['user_username'],
            'role' => $_SESSION['user_role'],
            'permissions' => $_SESSION['user_permissions'] ?? []
        ];
    }
    
    /**
     * Lista de todas as permissões disponíveis no sistema.
     */
    public static function getAvailablePermissions(): array {
        return [
            'view_rmas' => 'Visualizar RMAs',
            'create_rmas' => 'Criar novos RMAs',
            'edit_rmas' => 'Alterar estado e editar RMAs',
            'delete_rmas' => 'Eliminar RMAs (Apenas Admin/Técnicos seniores)',
            'manage_stock' => 'Gerir Stock/Inventário de Peças',
            'manage_users' => 'Gerir Utilizadores/Técnicos (Criar, Alterar Permissões)',
            'view_reports' => 'Visualizar Relatórios e Faturação',
            'privacy_rgpd' => 'Executar Direito ao Esquecimento (Eliminar dados pessoais)',
            'manage_settings' => 'Gerir Definições Globais do Site (SMTP, Estados, Nome, Logo)'
        ];
    }
}

