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
use RmaGest\Helpers;
use RmaGest\Auth;
use RmaGest\Mailer;
use PDO;

class Controller {

    /**
     * Ponto de entrada do roteamento da aplicação.
     */
    public function handleRequest() {
        Auth::initSession();
        
        // Executar Lazy Cron para backups automáticos
        Helpers::checkAndRunLazyCron();
        
        $route = isset($_GET['route']) ? $_GET['route'] : 'client/home';
        
        // Proteção das Rotas Técnicas
        if (strpos($route, 'tech/') === 0 && $route !== 'tech/login') {
            if (!Auth::isLoggedIn()) {
                header('Location: index.php?route=tech/login');
                exit;
            }
        }

        switch ($route) {
            // --- ROTAS DO CLIENTE (PÚBLICAS) ---
            case 'client/home':
                $this->clientHome();
                break;
                
            case 'client/new-rma':
                $this->clientNewRma();
                break;
                
            case 'client/rma-view':
                $this->clientRmaView();
                break;
                
            case 'client/rma-auth':
                $this->clientRmaAuth();
                break;
                
            case 'client/chat-send':
                $this->clientChatSend();
                break;

            // --- ROTAS DO TÉCNICO (PRIVADAS) ---
            case 'tech/login':
                $this->techLogin();
                break;
                
            case 'tech/logout':
                $this->techLogout();
                break;
                
            case 'tech/dashboard':
                $this->techDashboard();
                break;
                
            case 'tech/rma-create':
                $this->techRmaCreate();
                break;
                
            case 'tech/rma-detail':
                $this->techRmaDetail();
                break;
                
            case 'tech/rma-update':
                $this->techRmaUpdate();
                break;
                
            case 'tech/rma-budget':
                $this->techRmaBudget();
                break;
                
            case 'tech/rma-add-component':
                $this->techRmaAddComponent();
                break;
                
            case 'tech/rma-delete-component':
                $this->techRmaDeleteComponent();
                break;

            case 'tech/chat-send':
                $this->techChatSend();
                break;

            case 'tech/rma-forget':
                $this->techRmaForget();
                break;

            case 'tech/rma-delete':
                $this->techRmaDelete();
                break;

            case 'tech/stock':
                $this->techStock();
                break;
                
            case 'tech/stock-delete':
                $this->techStockDelete();
                break;
                
            case 'tech/reports':
                $this->techReports();
                break;
                
            case 'tech/settings':
                $this->techSettings();
                break;

            case 'tech/chat-delete':
                $this->techChatDelete();
                break;

            case 'change-lang':
                $this->changeLang();
                break;

            case 'cron/backup':
                $this->cronBackup();
                break;

            default:
                header("HTTP/1.0 404 Not Found");
                die("Página não encontrada.");
        }
    }

    private function changeLang() {
        $lang = $_GET['lang'] ?? 'pt';
        if (in_array($lang, ['pt', 'en'])) {
            $_SESSION['lang'] = $lang;
            setcookie('lang', $lang, time() + (30*24*60*60), '/');
        }
        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
        header("Location: " . $referer);
        exit;
    }

    private function cronBackup() {
        $enabled = Helpers::getSetting('backup_cron_enabled', '0');
        if ($enabled !== '1') {
            header("HTTP/1.0 403 Forbidden");
            die("Backup automatico desativado.");
        }
        
        $token = Helpers::getSetting('backup_cron_token', '');
        $requestToken = $_GET['token'] ?? '';
        if (empty($token) || $requestToken !== $token) {
            header("HTTP/1.0 403 Forbidden");
            die("Token de seguranca invalido.");
        }
        
        $folder = Helpers::getSetting('backup_cron_folder', '');
        $result = Helpers::createBackup($folder);
        if ($result) {
            $db = Database::getInstance();
            $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
            $now = date('Y-m-d H:i:s');
            
            if ($driver === 'sqlite') {
                $stmt = $db->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('backup_cron_last_run', ?)");
            } else {
                $stmt = $db->prepare("INSERT INTO settings (`key`, `value`) VALUES ('backup_cron_last_run', ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
            }
            $stmt->execute([$now]);
            
            echo "Copia de seguranca criada com sucesso: " . basename($result);
        } else {
            header("HTTP/1.0 500 Internal Server Error");
            echo "Erro ao criar copia de seguranca.";
        }
        exit;
    }

    // ==========================================
    // --- LÓGICA DO CLIENTE ---
    // ==========================================

    private function clientHome() {
        Helpers::renderView('client/home');
    }

    private function clientNewRma() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $form_mode = Helpers::getSetting('form_mode', 'default');
            $custom_form_json = Helpers::getSetting('custom_form_json', '');

            $client_name = $_POST['client_name'] ?? '';
            $client_email = $_POST['client_email'] ?? '';
            $client_contact = $_POST['client_contact'] ?? '';
            $client_address = $_POST['client_address'] ?? '';
            $device_type = $_POST['device_type'] ?? '';
            $serial_number = $_POST['serial_number'] ?? '';
            $device_condition = $_POST['device_condition'] ?? '';

            if (empty($client_name) || empty($client_email) || empty($client_contact) || empty($device_type) || empty($device_condition)) {
                $error = "Por favor, preencha todos os campos obrigatórios (*).";
            } else {
                $db = Database::getInstance();
                
                $rma_number = Helpers::generateRmaNumber();
                $access_code = Helpers::generateAccessCode();
                $initial_status = "Em análise";

                // Upload inicial de foto
                $attachment_path = null;
                if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                    $attachment_path = Helpers::uploadFile($_FILES['attachment'], 'rmas');
                }

                // Processar campos personalizados
                $customFormData = null;
                if ($form_mode === 'custom' && !empty($custom_form_json)) {
                    $fields = json_decode($custom_form_json, true) ?: [];
                    $customResponses = [];
                    foreach ($fields as $field) {
                        if (!empty($field['mapped_to'])) {
                            continue; // Campo núcleo do sistema, já tratado
                        }
                        $fieldId = $field['id'];
                        $fieldLabel = $field['label'] ?? '';
                        $fieldType = $field['type'] ?? 'text';
                        $value = '';

                        if ($fieldType === 'file') {
                            if (isset($_FILES["custom_field_{$fieldId}"]) && $_FILES["custom_field_{$fieldId}"]["error"] === UPLOAD_ERR_OK) {
                                $value = Helpers::uploadFile($_FILES["custom_field_{$fieldId}"], 'rmas');
                            }
                        } elseif ($fieldType === 'checkbox') {
                            $value = isset($_POST["custom_field_{$fieldId}"]) ? implode(', ', (array)$_POST["custom_field_{$fieldId}"]) : '';
                        } else {
                            $value = $_POST["custom_field_{$fieldId}"] ?? '';
                        }

                        $customResponses[] = [
                            'label' => $fieldLabel,
                            'value' => $value,
                            'type' => $fieldType
                        ];
                    }
                    $customFormData = json_encode($customResponses);
                }

                $allow_sms_whatsapp = isset($_POST['allow_sms_whatsapp']) ? 1 : 0;

                $stmt = $db->prepare("INSERT INTO rmas (rma_number, access_code, client_name, client_email, client_contact, client_address, device_type, serial_number, device_condition, current_status, allow_sms_whatsapp, custom_form_data) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                if ($stmt->execute([$rma_number, $access_code, $client_name, $client_email, $client_contact, $client_address, $device_type, $serial_number, $device_condition, $initial_status, $allow_sms_whatsapp, $customFormData])) {
                    $rma_id = $db->lastInsertId();

                    // Se carregou anexo, insere o anexo na conversa/histórico inicial do RMA
                    if ($attachment_path) {
                        $stmtChat = $db->prepare("INSERT INTO rma_chat (rma_id, sender_type, sender_name, message, file_path) VALUES (?, 'system', 'Sistema', 'Documento/Foto de entrada carregada.', ?)");
                        $stmtChat->execute([$rma_id, $attachment_path]);
                    }

                    // Se carregou anexos dinâmicos personalizados, regista-os no chat do processo
                    if ($customFormData) {
                        $customResponsesDecoded = json_decode($customFormData, true) ?: [];
                        foreach ($customResponsesDecoded as $resp) {
                            if (($resp['type'] ?? '') === 'file' && !empty($resp['value'])) {
                                $stmtChatCustom = $db->prepare("INSERT INTO rma_chat (rma_id, sender_type, sender_name, message, file_path) VALUES (?, 'system', 'Sistema', ?, ?)");
                                $stmtChatCustom->execute([$rma_id, "Ficheiro anexo submetido no campo: " . $resp['label'], $resp['value']]);
                            }
                        }
                    }

                    $rmaData = [
                        'id' => $rma_id,
                        'rma_number' => $rma_number,
                        'access_code' => $access_code,
                        'client_name' => $client_name,
                        'client_email' => $client_email,
                        'client_contact' => $client_contact,
                        'device_type' => $device_type,
                        'device_condition' => $device_condition,
                        'current_status' => $initial_status,
                        'allow_sms_whatsapp' => $allow_sms_whatsapp
                    ];
                    $this->sendRmaNotifications($rmaData, 'client_new');
                    $this->sendAdminNotifications('repair_request', $rmaData);

                    // Auto-autenticar o cliente imediatamente para ver a página de sucesso
                    $_SESSION['rma_auth_' . $rma_id] = true;

                    header("Location: index.php?route=client/rma-view&rma={$rma_number}");
                    exit;
                } else {
                    $error = "Ocorreu um erro ao registar o pedido de reparação no sistema. Tente de novo.";
                }
            }
        }

        Helpers::renderView('client/new_rma', ['error' => $error, 'deviceTypes' => Helpers::getDeviceTypes()]);
    }

    private function clientRmaView() {
        $rmaNumber = $_GET['rma'] ?? '';
        if (empty($rmaNumber)) {
            header('Location: index.php');
            exit;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM rmas WHERE rma_number = ?");
        $stmt->execute([$rmaNumber]);
        $rma = $stmt->fetch();

        if (!$rma) {
            header('Location: index.php?error=not_found');
            exit;
        }

        // Ler mensagens do chat
        $stmtChat = $db->prepare("SELECT * FROM rma_chat WHERE rma_id = ? ORDER BY created_at ASC");
        $stmtChat->execute([$rma['id']]);
        $chatMessages = $stmtChat->fetchAll();

        Helpers::renderView('client/rma_view', [
            'rma' => $rma,
            'chatMessages' => $chatMessages
        ]);
    }

    private function clientRmaAuth() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rma_id = (int)$_POST['rma_id'];
            $access_code = strtoupper(trim($_POST['access_code'] ?? ''));

            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT rma_number, access_code FROM rmas WHERE id = ?");
            $stmt->execute([$rma_id]);
            $rma = $stmt->fetch();

            if ($rma && $rma['access_code'] === $access_code) {
                $_SESSION['rma_auth_' . $rma_id] = true;
                header("Location: index.php?route=client/rma-view&rma=" . $rma['rma_number']);
                exit;
            } else {
                header("Location: index.php?route=client/rma-view&rma=" . ($rma['rma_number'] ?? '') . "&error=auth_failed");
                exit;
            }
        }
        header('Location: index.php');
        exit;
    }

    private function clientChatSend() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rma_id = (int)$_POST['rma_id'];
            $message = $_POST['message'] ?? '';

            if (empty($message)) {
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            }

            // Validar se o cliente está de facto autenticado para este chat
            if (!isset($_SESSION['rma_auth_' . $rma_id]) || $_SESSION['rma_auth_' . $rma_id] !== true) {
                die("Acesso não autorizado ao chat.");
            }

            $db = Database::getInstance();

            // Obter dados do RMA
            $stmtRma = $db->prepare("SELECT rma_number, client_name FROM rmas WHERE id = ?");
            $stmtRma->execute([$rma_id]);
            $rma = $stmtRma->fetch();

            if (!$rma) {
                die("Processo inválido.");
            }

            $clientName = $rma['client_name'] ?? 'Cliente';

            // Upload de anexo se existir
            $attachment_path = null;
            if (isset($_FILES['chat_attachment']) && $_FILES['chat_attachment']['error'] === UPLOAD_ERR_OK) {
                $attachment_path = Helpers::uploadFile($_FILES['chat_attachment'], 'chats');
            }

            $stmt = $db->prepare("INSERT INTO rma_chat (rma_id, sender_type, sender_name, message, file_path) VALUES (?, 'client', ?, ?, ?)");
            $stmt->execute([$rma_id, $clientName, $message, $attachment_path]);

            // Enviar e-mail de alerta aos administradores
            $chatData = [
                'rma_id' => $rma_id,
                'rma_number' => $rma['rma_number'],
                'client_name' => $clientName,
                'message' => $message
            ];
            $this->sendAdminNotifications('chat_message', $chatData);

            header("Location: index.php?route=client/rma-view&rma=" . $rma['rma_number']);
            exit;
        }
        header('Location: index.php');
        exit;
    }

    // ==========================================
    // --- LÓGICA DO TÉCNICO ---
    // ==========================================

    private function techLogin() {
        if (Auth::isLoggedIn()) {
            header('Location: index.php?route=tech/dashboard');
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $error = "Por favor, preencha todos os campos.";
            } else {
                if (Auth::login($username, $password)) {
                    header('Location: index.php?route=tech/dashboard');
                    exit;
                } else {
                    $error = "Nome de utilizador ou palavra-passe incorretos.";
                }
            }
        }

        Helpers::renderView('tech/login', ['error' => $error], false);
    }

    private function techLogout() {
        Auth::logout();
        header('Location: index.php?route=tech/login');
        exit;
    }

    private function techDashboard() {
        $db = Database::getInstance();
        
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';

        $query = "SELECT * FROM rmas WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $query .= " AND (rma_number LIKE ? OR client_name LIKE ? OR client_contact LIKE ? OR device_type LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        if (!empty($status)) {
            $query .= " AND current_status = ?";
            $params[] = $status;
        }

        $query .= " ORDER BY created_at DESC";
        
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $rmas = $stmt->fetchAll();

        // Calcular estatísticas rápidas
        $activeRmas = $db->query("SELECT COUNT(*) FROM rmas WHERE current_status NOT IN ('Finalizado', 'Devolvido Cliente')")->fetchColumn();
        $waitingRmas = $db->query("SELECT COUNT(*) FROM rmas WHERE current_status IN ('Aguarda Cliente', 'Aguarda Fornecedor')")->fetchColumn();
        $repairedRmas = $db->query("SELECT COUNT(*) FROM rmas WHERE current_status = 'Reparado'")->fetchColumn();
        $lowStock = $db->query("SELECT COUNT(*) FROM stock WHERE quantity <= 3")->fetchColumn();

        Helpers::renderView('tech/dashboard', [
            'rmas' => $rmas,
            'stats' => [
                'active_rmas' => $activeRmas,
                'waiting_rmas' => $waitingRmas,
                'repaired_rmas' => $repairedRmas,
                'low_stock' => $lowStock
            ],
            'filters' => [
                'search' => $search,
                'status' => $status
            ]
        ]);
    }

    private function techRmaCreate() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $client_name = $_POST['client_name'] ?? '';
            $client_email = $_POST['client_email'] ?? '';
            $client_contact = $_POST['client_contact'] ?? '';
            $client_address = $_POST['client_address'] ?? '';
            $device_type = $_POST['device_type'] ?? '';
            $serial_number = $_POST['serial_number'] ?? '';
            $device_condition = $_POST['device_condition'] ?? '';

            if (empty($client_name) || empty($client_email) || empty($client_contact) || empty($device_type) || empty($device_condition)) {
                $error = "Por favor, preencha todos os campos obrigatórios (*).";
            } else {
                $db = Database::getInstance();
                
                $rma_number = Helpers::generateRmaNumber();
                $access_code = Helpers::generateAccessCode();
                $initial_status = "Em análise";

                // Upload
                $attachment_path = null;
                if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                    $attachment_path = Helpers::uploadFile($_FILES['attachment'], 'rmas');
                }

                $allow_sms_whatsapp = isset($_POST['allow_sms_whatsapp']) ? 1 : 0;

                $stmt = $db->prepare("INSERT INTO rmas (rma_number, access_code, client_name, client_email, client_contact, client_address, device_type, serial_number, device_condition, current_status, allow_sms_whatsapp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                if ($stmt->execute([$rma_number, $access_code, $client_name, $client_email, $client_contact, $client_address, $device_type, $serial_number, $device_condition, $initial_status, $allow_sms_whatsapp])) {
                    $rma_id = $db->lastInsertId();

                    if ($attachment_path) {
                        $stmtChat = $db->prepare("INSERT INTO rma_chat (rma_id, sender_type, sender_name, message, file_path) VALUES (?, 'system', 'Sistema', 'Documento/Foto de entrada carregada pelo Técnico.', ?)");
                        $stmtChat->execute([$rma_id, $attachment_path]);
                    }

                    $rmaData = [
                        'rma_number' => $rma_number,
                        'access_code' => $access_code,
                        'client_name' => $client_name,
                        'client_email' => $client_email,
                        'client_contact' => $client_contact,
                        'device_type' => $device_type,
                        'current_status' => $initial_status,
                        'allow_sms_whatsapp' => $allow_sms_whatsapp
                    ];
                    $this->sendRmaNotifications($rmaData, 'tech_create');

                    header("Location: index.php?route=tech/rma-detail&id={$rma_id}");
                    exit;
                } else {
                    $error = "Erro ao registar RMA.";
                }
            }
        }

        Helpers::renderView('tech/rma_create', ['error' => $error, 'deviceTypes' => Helpers::getDeviceTypes()]);
    }

    private function techRmaDetail() {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: index.php?route=tech/dashboard');
            exit;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM rmas WHERE id = ?");
        $stmt->execute([$id]);
        $rma = $stmt->fetch();

        if (!$rma) {
            header('Location: index.php?route=tech/dashboard');
            exit;
        }

        // Obter mensagens do chat
        $stmtChat = $db->prepare("SELECT * FROM rma_chat WHERE rma_id = ? ORDER BY created_at ASC");
        $stmtChat->execute([$id]);
        $chatMessages = $stmtChat->fetchAll();

        // Obter itens de stock para dropdown
        $stockItems = $db->query("SELECT * FROM stock ORDER BY name ASC")->fetchAll();

        // Obter componentes já usados no RMA
        $stmtComp = $db->prepare("SELECT * FROM rma_components WHERE rma_id = ? ORDER BY created_at ASC");
        $stmtComp->execute([$id]);
        $rmaComponents = $stmtComp->fetchAll();

        $predefinedResponses = $db->query("SELECT * FROM predefined_responses ORDER BY title ASC")->fetchAll();

        Helpers::renderView('tech/rma_detail', [
            'rma' => $rma,
            'chatMessages' => $chatMessages,
            'stockItems' => $stockItems,
            'rmaComponents' => $rmaComponents,
            'predefinedResponses' => $predefinedResponses
        ]);
    }

    private function techRmaUpdate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rma_id = (int)$_POST['rma_id'];
            $current_status = $_POST['current_status'] ?? '';
            $status_comment = $_POST['status_comment'] ?? '';
            $tech_report = $_POST['tech_report'] ?? '';

            if ($rma_id <= 0) {
                die("Processo inválido.");
            }

            $db = Database::getInstance();

            // Obter dados atuais do RMA para verificar se o estado de facto mudou
            $stmt = $db->prepare("SELECT rma_number, current_status, client_name, client_email, client_contact, access_code, device_type, allow_sms_whatsapp FROM rmas WHERE id = ?");
            $stmt->execute([$rma_id]);
            $rma = $stmt->fetch();

            if (!$rma) {
                die("RMA inexistente.");
            }

            $oldStatus = $rma['current_status'];
            $statusChanged = $oldStatus !== $current_status;
            
            $allow_sms_whatsapp = isset($_POST['allow_sms_whatsapp']) ? 1 : 0;

            // Atualizar o RMA
            $stmtUpdate = $db->prepare("UPDATE rmas SET current_status = ?, tech_report = ?, allow_sms_whatsapp = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmtUpdate->execute([$current_status, $tech_report, $allow_sms_whatsapp, $rma_id]);

            if ($statusChanged) {
                // Inserir atualização no Chat/Log do Sistema
                $techName = $_SESSION['user_name'];
                $systemMessage = "Estado alterado de '{$oldStatus}' para '{$current_status}'.";
                if (!empty($status_comment)) {
                    $systemMessage .= " Comentário: " . $status_comment;
                }

                $stmtChat = $db->prepare("INSERT INTO rma_chat (rma_id, sender_type, sender_name, message, is_status_change) VALUES (?, 'system', 'Sistema', ?, 1)");
                $stmtChat->execute([$rma_id, $systemMessage]);

                $rmaUpdated = [
                    'rma_number' => $rma['rma_number'],
                    'access_code' => $rma['access_code'],
                    'client_name' => $rma['client_name'],
                    'client_email' => $rma['client_email'],
                    'client_contact' => $rma['client_contact'],
                    'device_type' => $rma['device_type'],
                    'current_status' => $current_status,
                    'allow_sms_whatsapp' => $allow_sms_whatsapp
                ];
                $this->sendRmaNotifications($rmaUpdated, 'status_update', ['status_comment' => $status_comment]);
            }

            header("Location: index.php?route=tech/rma-detail&id={$rma_id}");
            exit;
        }
        header('Location: index.php?route=tech/dashboard');
        exit;
    }

    private function techRmaBudget() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rma_id = (int)$_POST['rma_id'];
            $budget_amount = (float)$_POST['budget_amount'];
            $budget_paid = (int)$_POST['budget_paid'];

            $db = Database::getInstance();
            $stmt = $db->prepare("UPDATE rmas SET budget_amount = ?, budget_paid = ? WHERE id = ?");
            $stmt->execute([$budget_amount, $budget_paid, $rma_id]);

            // Adiciona log de alteração de orçamento no chat
            $text = "Orçamento atualizado para " . number_format($budget_amount, 2, ',', ' ') . " € (" . ($budget_paid === 1 ? 'Pago/Aprovado' : 'Pendente de Pagamento') . ").";
            $stmtLog = $db->prepare("INSERT INTO rma_chat (rma_id, sender_type, sender_name, message) VALUES (?, 'system', 'Sistema', ?)");
            $stmtLog->execute([$rma_id, $text]);

            header("Location: index.php?route=tech/rma-detail&id={$rma_id}");
            exit;
        }
        header('Location: index.php?route=tech/dashboard');
        exit;
    }

    private function techRmaAddComponent() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rma_id = (int)$_POST['rma_id'];
            $stock_id = $_POST['stock_id'] !== "" ? (int)$_POST['stock_id'] : null;
            $manual_name = $_POST['manual_name'] ?? '';
            $quantity = (int)$_POST['quantity'];
            $price_per_unit = (float)$_POST['price_per_unit'];

            if ($rma_id <= 0 || $quantity <= 0) {
                die("Dados inválidos.");
            }

            $db = Database::getInstance();
            
            $component_name = '';

            if ($stock_id !== null) {
                // Obter dados da peça no stock
                $stmtStock = $db->prepare("SELECT name, quantity FROM stock WHERE id = ?");
                $stmtStock->execute([$stock_id]);
                $stockItem = $stmtStock->fetch();

                if (!$stockItem || $stockItem['quantity'] < $quantity) {
                    die("Erro: Quantidade indisponível em stock.");
                }

                $component_name = $stockItem['name'];

                // Reduzir quantidade do stock
                $stmtUpdateStock = $db->prepare("UPDATE stock SET quantity = quantity - ? WHERE id = ?");
                $stmtUpdateStock->execute([$quantity, $stock_id]);
            } else {
                $component_name = $manual_name;
                if (empty($component_name)) {
                    die("O nome da peça manual é obrigatório.");
                }
            }

            // Inserir componente associado
            $stmtInsert = $db->prepare("INSERT INTO rma_components (rma_id, stock_id, component_name, quantity, price_per_unit) VALUES (?, ?, ?, ?, ?)");
            $stmtInsert->execute([$rma_id, $stock_id, $component_name, $quantity, $price_per_unit]);

            header("Location: index.php?route=tech/rma-detail&id={$rma_id}");
            exit;
        }
        header('Location: index.php?route=tech/dashboard');
        exit;
    }

    private function techRmaDeleteComponent() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $comp_id = (int)$_POST['component_id'];
            $rma_id = (int)$_POST['rma_id'];

            $db = Database::getInstance();

            // Ler o componente para ver se pertence ao stock (para podermos devolver a quantidade)
            $stmt = $db->prepare("SELECT stock_id, quantity FROM rma_components WHERE id = ?");
            $stmt->execute([$comp_id]);
            $comp = $stmt->fetch();

            if ($comp) {
                if ($comp['stock_id'] !== null) {
                    // Devolver quantidade ao stock
                    $stmtReturn = $db->prepare("UPDATE stock SET quantity = quantity + ? WHERE id = ?");
                    $stmtReturn->execute([$comp['quantity'], $comp['stock_id']]);
                }

                // Apagar
                $stmtDelete = $db->prepare("DELETE FROM rma_components WHERE id = ?");
                $stmtDelete->execute([$comp_id]);
            }

            header("Location: index.php?route=tech/rma-detail&id={$rma_id}");
            exit;
        }
        header('Location: index.php?route=tech/dashboard');
        exit;
    }

    private function techChatSend() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rma_id = (int)$_POST['rma_id'];
            $message = $_POST['message'] ?? '';

            if (empty($message)) {
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            }

            $db = Database::getInstance();
            
            // Obter dados do cliente
            $stmt = $db->prepare("SELECT rma_number, client_name, client_email, client_contact, access_code, device_type, current_status, allow_sms_whatsapp FROM rmas WHERE id = ?");
            $stmt->execute([$rma_id]);
            $rma = $stmt->fetch();

            if (!$rma) {
                die("RMA inexistente.");
            }

            $techName = $_SESSION['user_name'];

            // Upload
            $attachment_path = null;
            if (isset($_FILES['chat_attachment']) && $_FILES['chat_attachment']['error'] === UPLOAD_ERR_OK) {
                $attachment_path = Helpers::uploadFile($_FILES['chat_attachment'], 'chats');
            }

            $stmtInsert = $db->prepare("INSERT INTO rma_chat (rma_id, sender_type, sender_name, message, file_path) VALUES (?, 'tech', ?, ?, ?)");
            $stmtInsert->execute([$rma_id, $techName, $message, $attachment_path]);

            $this->sendRmaNotifications($rma, 'chat_new', ['message' => $message, 'tech_name' => $techName]);

            header("Location: index.php?route=tech/rma-detail&id={$rma_id}");
            exit;
        }
        header('Location: index.php?route=tech/dashboard');
        exit;
    }

    private function techChatDelete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $message_id = (int)$_POST['message_id'];
            $rma_id = (int)$_POST['rma_id'];

            if ($message_id <= 0 || $rma_id <= 0) {
                die("Processo inválido.");
            }

            $db = Database::getInstance();
            
            // Obter info da mensagem para apagar o anexo do disco, se aplicável
            $stmtMsg = $db->prepare("SELECT file_path FROM rma_chat WHERE id = ? AND rma_id = ?");
            $stmtMsg->execute([$message_id, $rma_id]);
            $msg = $stmtMsg->fetch();
            
            if ($msg) {
                if (!empty($msg['file_path']) && file_exists(dirname(__DIR__) . '/' . $msg['file_path'])) {
                    @unlink(dirname(__DIR__) . '/' . $msg['file_path']);
                }

                $stmtDelete = $db->prepare("DELETE FROM rma_chat WHERE id = ? AND rma_id = ?");
                $stmtDelete->execute([$message_id, $rma_id]);
            }

            header("Location: index.php?route=tech/rma-detail&id={$rma_id}");
            exit;
        }
        header("Location: index.php?route=tech/dashboard");
        exit;
    }

    private function techRmaForget() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::checkPermission('privacy_rgpd')) {
                die("Permissão negada (Direito ao Esquecimento RGPD).");
            }

            $rma_id = (int)$_POST['rma_id'];
            $db = Database::getInstance();

            // Fazer a limpeza/anonimização dos dados de cliente na tabela de rma
            $stmt = $db->prepare("UPDATE rmas SET client_name = '', client_email = '', client_contact = '', client_address = '' WHERE id = ?");
            $stmt->execute([$rma_id]);

            // Limpar também possíveis menções no chat para manter conformidade
            $stmtChat = $db->prepare("UPDATE rma_chat SET sender_name = 'Cliente (Anónimo)' WHERE rma_id = ? AND sender_type = 'client'");
            $stmtChat->execute([$rma_id]);

            // Regista evento de anonimização na ocorrência
            $stmtLog = $db->prepare("INSERT INTO rma_chat (rma_id, sender_type, sender_name, message) VALUES (?, 'system', 'Sistema', 'Os dados pessoais do cliente associado foram eliminados em conformidade com o RGPD (Direito ao Esquecimento).')");
            $stmtLog->execute([$rma_id]);

            header("Location: index.php?route=tech/rma-detail&id={$rma_id}");
            exit;
        }
        header('Location: index.php?route=tech/dashboard');
        exit;
    }

    private function techRmaDelete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::checkPermission('delete_rmas')) {
                die("Sem permissão para eliminar registos.");
            }

            $rma_id = (int)$_POST['rma_id'];
            $db = Database::getInstance();

            // O cascade automático na chave estrangeira irá apagar chat e componentes associados.
            $stmt = $db->prepare("DELETE FROM rmas WHERE id = ?");
            $stmt->execute([$rma_id]);

            header('Location: index.php?route=tech/dashboard');
            exit;
        }
        header('Location: index.php?route=tech/dashboard');
        exit;
    }

    private function techStock() {
        if (!Auth::checkPermission('manage_stock')) {
            die("Sem permissões para aceder ao stock.");
        }

        $db = Database::getInstance();
        $error = '';
        $editingItem = null;

        // Tratar Edição se vier por GET
        if (isset($_GET['edit'])) {
            $editId = (int)$_GET['edit'];
            $stmtEdit = $db->prepare("SELECT * FROM stock WHERE id = ?");
            $stmtEdit->execute([$editId]);
            $editingItem = $stmtEdit->fetch();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $sku = strtoupper(trim($_POST['sku'] ?? ''));
            $quantity = (int)$_POST['quantity'];
            $price = (float)$_POST['price'];

            if (empty($name) || empty($sku)) {
                $error = "Todos os campos são obrigatórios.";
            } else {
                try {
                    if (isset($_GET['id'])) {
                        // Atualizar item existente
                        $id = (int)$_GET['id'];
                        $stmt = $db->prepare("UPDATE stock SET name = ?, sku = ?, quantity = ?, price = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                        $stmt->execute([$name, $sku, $quantity, $price, $id]);
                    } else {
                        // Criar novo item
                        $stmt = $db->prepare("INSERT INTO stock (name, sku, quantity, price) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$name, $sku, $quantity, $price]);
                    }
                    
                    header('Location: index.php?route=tech/stock');
                    exit;
                } catch (\PDOException $e) {
                    if ($e->getCode() == 23000 || strpos($e->getMessage(), 'UNIQUE') !== false) {
                        $error = "A Referência / SKU inserida já existe no inventário.";
                    } else {
                        $error = "Erro ao registar: " . $e->getMessage();
                    }
                }
            }
        }

        $stock = $db->query("SELECT * FROM stock ORDER BY created_at DESC")->fetchAll();

        Helpers::renderView('tech/stock', [
            'stock' => $stock,
            'editingItem' => $editingItem,
            'error' => $error
        ]);
    }

    private function techStockDelete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::checkPermission('manage_stock')) {
                die("Sem permissão.");
            }

            $id = (int)$_POST['id'];
            $db = Database::getInstance();

            $stmt = $db->prepare("DELETE FROM stock WHERE id = ?");
            $stmt->execute([$id]);

            header('Location: index.php?route=tech/stock');
            exit;
        }
        header('Location: index.php?route=tech/stock');
        exit;
    }

    private function techReports() {
        if (!Auth::checkPermission('view_reports')) {
            die("Sem acesso a relatórios.");
        }

        $db = Database::getInstance();

        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';
        $status = $_GET['status'] ?? '';
        $techId = $_GET['tech_id'] ?? '';

        $query = "SELECT * FROM rmas WHERE 1=1";
        $params = [];

        if (!empty($startDate)) {
            $query .= " AND created_at >= ?";
            $params[] = $startDate . ' 00:00:00';
        }

        if (!empty($endDate)) {
            $query .= " AND created_at <= ?";
            $params[] = $endDate . ' 23:59:59';
        }

        if (!empty($status)) {
            $query .= " AND current_status = ?";
            $params[] = $status;
        }

        // Se quiser filtrar por técnico (neste esquema associamos rma_chat a técnicos)
        // Para simplificar, mostramos apenas o filtro por dados gerais,
        // mas pode expandir para a tabela se desejar

        $query .= " ORDER BY created_at ASC";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $rmas = $stmt->fetchAll();

        // Calcular somatórios
        $count = count($rmas);
        $paidAmount = 0.00;
        $pendingAmount = 0.00;

        foreach ($rmas as $item) {
            if ($item['budget_amount'] > 0) {
                if ($item['budget_paid'] == 1) {
                    $paidAmount += $item['budget_amount'];
                } else {
                    $pendingAmount += $item['budget_amount'];
                }
            }
        }

        // Listar todos os técnicos para o filtro
        $technicians = $db->query("SELECT id, name FROM users ORDER BY name ASC")->fetchAll();

        Helpers::renderView('tech/reports', [
            'rmas' => $rmas,
            'technicians' => $technicians,
            'totals' => [
                'count' => $count,
                'paid_amount' => $paidAmount,
                'pending_amount' => $pendingAmount
            ],
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $status,
                'tech_id' => $techId
            ]
        ]);
    }

    private function techSettings() {
        if (!Auth::checkPermission('manage_settings')) {
            die("Sem permissão para gerir definições.");
        }

        $db = Database::getInstance();
        $error = '';
        $success = '';

        $action = $_GET['action'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($action === 'general') {
                // Gravar Geral
                $siteName = $_POST['site_name'] ?? 'RMA Gest';
                Helpers::setSetting('site_name', $siteName);

                // Tratar logotipo
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                    $uploadedPath = Helpers::uploadFile($_FILES['logo'], 'logos');
                    if ($uploadedPath) {
                        Helpers::setSetting('logo_path', $uploadedPath);
                    }
                }

                // Tratar favicon/icon do site
                if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
                    $uploadedFaviconPath = Helpers::uploadFile($_FILES['favicon'], 'logos');
                    if ($uploadedFaviconPath) {
                        Helpers::setSetting('favicon_path', $uploadedFaviconPath);
                    }
                }
                $success = "Definições gerais atualizadas com sucesso.";
            } elseif ($action === 'email') {
                // Gravar Email
                $enabled = isset($_POST['smtp_enabled']) ? '1' : '0';
                Helpers::setSetting('smtp_enabled', $enabled);
                Helpers::setSetting('smtp_host', $_POST['smtp_host'] ?? '');
                Helpers::setSetting('smtp_port', $_POST['smtp_port'] ?? '587');
                Helpers::setSetting('smtp_encryption', $_POST['smtp_encryption'] ?? 'tls');
                Helpers::setSetting('smtp_user', $_POST['smtp_user'] ?? '');
                Helpers::setSetting('smtp_pass', $_POST['smtp_pass'] ?? '');
                Helpers::setSetting('smtp_from_email', $_POST['smtp_from_email'] ?? '');
                Helpers::setSetting('smtp_from_name', $_POST['smtp_from_name'] ?? '');
                
                $success = "Configurações de email atualizadas.";
            } elseif ($action === 'status-add') {
                // Adicionar Estado
                $newStatus = trim($_POST['new_status'] ?? '');
                if (!empty($newStatus)) {
                    $statuses = Helpers::getStatuses();
                    if (!in_array($newStatus, $statuses)) {
                        $statuses[] = $newStatus;
                        Helpers::setSetting('rma_statuses', json_encode($statuses));
                        $success = "Novo estado '{$newStatus}' adicionado com sucesso.";
                    } else {
                        $error = "Esse estado já existe.";
                    }
                }
            } elseif ($action === 'status-delete') {
                // Apagar Estado
                $statusName = $_POST['status_name'] ?? '';
                if (!empty($statusName)) {
                    $statuses = Helpers::getStatuses();
                    $index = array_search($statusName, $statuses);
                    if ($index !== false) {
                        unset($statuses[$index]);
                        $statuses = array_values($statuses); // reindexar
                        Helpers::setSetting('rma_statuses', json_encode($statuses));
                        $success = "Estado removido do fluxo.";
                    }
                }
            } elseif ($action === 'device-type-add') {
                // Adicionar Tipo de Equipamento
                $newDtype = trim($_POST['new_dtype'] ?? '');
                if (!empty($newDtype)) {
                    $dtypes = Helpers::getDeviceTypes();
                    if (!in_array($newDtype, $dtypes)) {
                        $dtypes[] = $newDtype;
                        Helpers::setSetting('device_types', json_encode($dtypes));
                        $success = "Novo tipo de equipamento '{$newDtype}' adicionado.";
                    } else {
                        $error = "Esse tipo de equipamento já existe.";
                    }
                }
            } elseif ($action === 'device-type-edit') {
                // Editar Tipo de Equipamento
                $oldName = trim($_POST['old_name'] ?? '');
                $newName = trim($_POST['new_name'] ?? '');
                if (!empty($oldName) && !empty($newName)) {
                    $dtypes = Helpers::getDeviceTypes();
                    $idx = array_search($oldName, $dtypes);
                    if ($idx !== false) {
                        $dtypes[$idx] = $newName;
                        Helpers::setSetting('device_types', json_encode($dtypes));
                        $success = "Tipo de equipamento atualizado para '{$newName}'.";
                    }
                }
            } elseif ($action === 'device-type-delete') {
                // Eliminar Tipo de Equipamento
                $dtypeName = $_POST['dtype_name'] ?? '';
                if (!empty($dtypeName)) {
                    $dtypes = Helpers::getDeviceTypes();
                    $idx = array_search($dtypeName, $dtypes);
                    if ($idx !== false) {
                        unset($dtypes[$idx]);
                        $dtypes = array_values($dtypes);
                        Helpers::setSetting('device_types', json_encode($dtypes));
                        $success = "Tipo de equipamento removido.";
                    }
                }
            } elseif ($action === 'user-add') {
                // Criar utilizador
                if (!Auth::checkPermission('manage_users')) {
                    die("Sem permissão para gerir utilizadores.");
                }

                $name = $_POST['name'] ?? '';
                $username = trim($_POST['username'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';
                $role = $_POST['role'] ?? 'tech';
                $permissions = $_POST['permissions'] ?? [];

                if (empty($name) || empty($username) || empty($email) || empty($password)) {
                    $error = "Todos os campos do utilizador são obrigatórios.";
                } else {
                    try {
                        $passHash = password_hash($password, PASSWORD_DEFAULT);
                        $permissionsJson = $role === 'admin' ? null : json_encode($permissions);

                        $stmt = $db->prepare("INSERT INTO users (name, username, password_hash, email, role, permissions) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$name, $username, $passHash, $email, $role, $permissionsJson]);
                        $success = "Novo utilizador técnico '{$name}' criado com sucesso.";
                    } catch (\PDOException $e) {
                        $error = "O nome de utilizador já existe no sistema.";
                    }
                }
            } elseif ($action === 'user-delete') {
                // Eliminar utilizador
                if (!Auth::checkPermission('manage_users')) {
                    die("Sem permissão.");
                }

                $user_id = (int)$_POST['user_id'];
                if ($user_id === (int)$_SESSION['user_id']) {
                    $error = "Não pode eliminar o seu próprio utilizador técnico ativo.";
                } else {
                    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $success = "Utilizador técnico eliminado do sistema.";
                }
            } elseif ($action === 'whatsapp') {
                // Gravar WhatsApp
                $enabled = isset($_POST['whatsapp_enabled']) ? '1' : '0';
                Helpers::setSetting('whatsapp_enabled', $enabled);
                Helpers::setSetting('whatsapp_url', $_POST['whatsapp_url'] ?? '');
                Helpers::setSetting('whatsapp_token_header', $_POST['whatsapp_token_header'] ?? 'Authorization');
                Helpers::setSetting('whatsapp_token', $_POST['whatsapp_token'] ?? '');
                Helpers::setSetting('whatsapp_payload', $_POST['whatsapp_payload'] ?? '{"number": "{phone}", "message": "{message}"}');
                $success = "Configurações de WhatsApp atualizadas.";
            } elseif ($action === 'templates') {
                // Gravar Modelos de Comunicação
                $types = ['client_new', 'tech_create', 'status_update', 'chat_new'];
                foreach ($types as $t) {
                    $subjKey = "email_tpl_{$t}_subject";
                    $bodyKey = "email_tpl_{$t}_body";
                    $waKey = "whatsapp_tpl_{$t}";

                    if (isset($_POST[$subjKey])) {
                        Helpers::setSetting($subjKey, $_POST[$subjKey]);
                    }
                    if (isset($_POST[$bodyKey])) {
                        Helpers::setSetting($bodyKey, $_POST[$bodyKey]);
                    }
                    if (isset($_POST[$waKey])) {
                        Helpers::setSetting($waKey, $_POST[$waKey]);
                    }
                }
                $success = "Modelos de mensagem atualizados com sucesso.";
            } elseif ($action === 'form-builder') {
                if (!Auth::checkPermission('manage_settings')) {
                    die("Sem permissão para gerir definições.");
                }
                $formMode = $_POST['form_mode'] ?? 'default';
                $customFormJson = $_POST['custom_form_json'] ?? '';
                Helpers::setSetting('form_mode', $formMode);
                Helpers::setSetting('custom_form_json', $customFormJson);
                $success = "Configurações do formulário atualizadas com sucesso.";
            } elseif ($action === 'notifications-add') {
                if (!Auth::checkPermission('manage_settings')) {
                    die("Sem permissão.");
                }
                $event = $_POST['event'] ?? '';
                $target_type = $_POST['target_type'] ?? '';
                $target_value = '';
                if ($target_type === 'email') {
                    $target_value = trim($_POST['target_email'] ?? '');
                } elseif ($target_type === 'user') {
                    $target_value = trim($_POST['target_user'] ?? '');
                }

                if (empty($event) || empty($target_type) || empty($target_value)) {
                    $error = "Todos os campos da notificação são obrigatórios.";
                } elseif ($target_type === 'email' && !filter_var($target_value, FILTER_VALIDATE_EMAIL)) {
                    $error = "Endereço de email inválido.";
                } else {
                    $rulesJson = Helpers::getSetting('notification_rules', '[]');
                    $rules = json_decode($rulesJson, true) ?: [];
                    $rules[] = [
                        'id' => uniqid('notif_'),
                        'event' => $event,
                        'target_type' => $target_type,
                        'target_value' => $target_value
                    ];
                    Helpers::setSetting('notification_rules', json_encode($rules));
                    $success = "Regra de notificação adicionada.";
                }
            } elseif ($action === 'notifications-delete') {
                if (!Auth::checkPermission('manage_settings')) {
                    die("Sem permissão.");
                }
                $ruleId = $_POST['rule_id'] ?? '';
                if (!empty($ruleId)) {
                    $rulesJson = Helpers::getSetting('notification_rules', '[]');
                    $rules = json_decode($rulesJson, true) ?: [];
                    $rules = array_filter($rules, function($rule) use ($ruleId) {
                        return ($rule['id'] ?? '') !== $ruleId;
                    });
                    $rules = array_values($rules);
                    Helpers::setSetting('notification_rules', json_encode($rules));
                    $success = "Notificação removida com sucesso.";
                }
            } elseif ($action === 'backup-manual') {
                if (!Auth::checkPermission('manage_settings')) {
                    die("Sem permissão.");
                }
                $folder = trim($_POST['backup_folder'] ?? '');
                $result = Helpers::createBackup($folder);
                if ($result) {
                    $success = Helpers::__('msg_backup_success') . " (" . basename($result) . ")";
                } else {
                    $error = Helpers::__('msg_backup_error');
                }
            } elseif ($action === 'backup-restore') {
                if (!Auth::checkPermission('manage_settings')) {
                    die("Sem permissão.");
                }
                $mode = $_POST['restore_mode'] ?? 'full';
                if (isset($_FILES['backup_file']) && $_FILES['backup_file']['error'] === UPLOAD_ERR_OK) {
                    $tmpPath = $_FILES['backup_file']['tmp_name'];
                    try {
                        if (Helpers::restoreBackup($tmpPath, $mode)) {
                            $success = Helpers::__('msg_restore_success');
                        } else {
                            $error = Helpers::__('msg_restore_error');
                        }
                    } catch (\Exception $e) {
                        $error = Helpers::__('msg_restore_error') . $e->getMessage();
                    }
                } else {
                    $error = "Por favor, selecione um ficheiro de backup válido.";
                }
            } elseif ($action === 'backup-cron-save') {
                if (!Auth::checkPermission('manage_settings')) {
                    die("Sem permissão.");
                }
                $enabled = isset($_POST['backup_cron_enabled']) ? '1' : '0';
                $frequency = $_POST['backup_cron_frequency'] ?? 'daily';
                $folder = trim($_POST['backup_cron_folder'] ?? '');
                
                Helpers::setSetting('backup_cron_enabled', $enabled);
                Helpers::setSetting('backup_cron_frequency', $frequency);
                Helpers::setSetting('backup_cron_folder', $folder);
                
                $token = Helpers::getSetting('backup_cron_token', '');
                if (empty($token)) {
                    $token = bin2hex(random_bytes(16));
                    Helpers::setSetting('backup_cron_token', $token);
                }
                
                $success = Helpers::__('msg_cron_saved');
            } elseif ($action === 'predefined-response-add') {
                if (!Auth::checkPermission('manage_settings')) {
                    die("Sem permissão.");
                }
                $title = trim($_POST['title'] ?? '');
                $message = trim($_POST['message'] ?? '');
                if (empty($title) || empty($message)) {
                    $error = "O título e a mensagem são obrigatórios.";
                } else {
                    $stmt = $db->prepare("INSERT INTO predefined_responses (title, message) VALUES (?, ?)");
                    $stmt->execute([$title, $message]);
                    $success = "Resposta pré-definida adicionada com sucesso.";
                }
            } elseif ($action === 'predefined-response-edit') {
                if (!Auth::checkPermission('manage_settings')) {
                    die("Sem permissão.");
                }
                $response_id = (int)($_POST['response_id'] ?? 0);
                $title = trim($_POST['title'] ?? '');
                $message = trim($_POST['message'] ?? '');
                if ($response_id <= 0 || empty($title) || empty($message)) {
                    $error = "Dados inválidos para edição.";
                } else {
                    $stmt = $db->prepare("UPDATE predefined_responses SET title = ?, message = ? WHERE id = ?");
                    $stmt->execute([$title, $message, $response_id]);
                    $success = "Resposta pré-definida atualizada com sucesso.";
                }
            } elseif ($action === 'predefined-response-delete') {
                if (!Auth::checkPermission('manage_settings')) {
                    die("Sem permissão.");
                }
                $response_id = (int)($_POST['response_id'] ?? 0);
                if ($response_id <= 0) {
                    $error = "ID inválido.";
                } else {
                    $stmt = $db->prepare("DELETE FROM predefined_responses WHERE id = ?");
                    $stmt->execute([$response_id]);
                    $success = "Resposta pré-definida eliminada.";
                }
            }
        }

        // Ler Definições Atuais para popular campos
        $settings = [
            'site_name' => Helpers::getSetting('site_name', 'RMA Gest'),
            'logo_path' => Helpers::getSetting('logo_path', ''),
            'favicon_path' => Helpers::getSetting('favicon_path', ''),
            'smtp_enabled' => Helpers::getSetting('smtp_enabled', '0'),
            'smtp_host' => Helpers::getSetting('smtp_host', ''),
            'smtp_port' => Helpers::getSetting('smtp_port', '587'),
            'smtp_encryption' => Helpers::getSetting('smtp_encryption', 'tls'),
            'smtp_user' => Helpers::getSetting('smtp_user', ''),
            'smtp_pass' => Helpers::getSetting('smtp_pass', ''),
            'smtp_from_email' => Helpers::getSetting('smtp_from_email', ''),
            'smtp_from_name' => Helpers::getSetting('smtp_from_name', ''),
            'whatsapp_enabled' => Helpers::getSetting('whatsapp_enabled', '0'),
            'whatsapp_url' => Helpers::getSetting('whatsapp_url', ''),
            'whatsapp_token_header' => Helpers::getSetting('whatsapp_token_header', 'Authorization'),
            'whatsapp_token' => Helpers::getSetting('whatsapp_token', ''),
            'whatsapp_payload' => Helpers::getSetting('whatsapp_payload', '{"number": "{phone}", "message": "{message}"}'),
            'backup_cron_enabled' => Helpers::getSetting('backup_cron_enabled', '0'),
            'backup_cron_frequency' => Helpers::getSetting('backup_cron_frequency', 'daily'),
            'backup_cron_folder' => Helpers::getSetting('backup_cron_folder', ''),
            'backup_cron_token' => Helpers::getSetting('backup_cron_token', '')
        ];

        $users = $db->query("SELECT * FROM users ORDER BY role ASC, name ASC")->fetchAll();
        $rmaStatuses = Helpers::getStatuses();
        $deviceTypes = Helpers::getDeviceTypes();
        $predefinedResponses = $db->query("SELECT * FROM predefined_responses ORDER BY title ASC")->fetchAll();

        Helpers::renderView('tech/settings', [
            'settings' => $settings,
            'users' => $users,
            'rmaStatuses' => $rmaStatuses,
            'deviceTypes' => $deviceTypes,
            'predefinedResponses' => $predefinedResponses,
            'success' => $success,
            'error' => $error
        ]);
    }

    /**
     * Envia notificações multilingues por email e WhatsApp.
     */
    private function sendRmaNotifications(array $rma, string $type, array $extra = []) {
        $lang = Helpers::getActiveLanguage();
        $siteName = Helpers::getSetting('site_name', 'RMA Gest');
        $trackingUrl = ($_SERVER['REQUEST_SCHEME'] ?? 'http') . "://{$_SERVER['HTTP_HOST']}" . dirname($_SERVER['PHP_SELF']) . "/index.php?route=client/rma-view&rma=" . $rma['rma_number'];
        
        $techName = $extra['tech_name'] ?? $_SESSION['user_name'] ?? 'Técnico';
        $statusComment = $extra['status_comment'] ?? '';
        $chatMessage = $extra['message'] ?? '';
        
        $placeholders = [
            '{client_name}' => $rma['client_name'] ?? '',
            '{rma_number}' => $rma['rma_number'] ?? '',
            '{access_code}' => $rma['access_code'] ?? '',
            '{device_type}' => Helpers::__($rma['device_type'] ?? ''),
            '{current_status}' => Helpers::__($rma['current_status'] ?? ''),
            '{status_comment}' => $statusComment,
            '{tech_name}' => $techName,
            '{message}' => $chatMessage,
            '{site_name}' => $siteName,
            '{tracking_url}' => $trackingUrl
        ];

        // 1. Enviar Email
        if (!empty($rma['client_email'])) {
            $subjectTpl = Helpers::getSetting("email_tpl_{$type}_subject", Helpers::getSetting("email_tpl_{$type}_subject_pt", Helpers::__("default_email_{$type}_subject")));
            $bodyTpl = Helpers::getSetting("email_tpl_{$type}_body", Helpers::getSetting("email_tpl_{$type}_body_pt", Helpers::__("default_email_{$type}_body")));

            $subject = str_replace(array_keys($placeholders), array_values($placeholders), $subjectTpl);
            $bodyContent = str_replace(array_keys($placeholders), array_values($placeholders), $bodyTpl);
            
            $emailBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05);'>
                <div style='background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); padding: 30px; text-align: center; color: white;'>
                    <h2 style='margin: 0; font-size: 24px; font-weight: 800;'>{$siteName}</h2>
                </div>
                <div style='padding: 30px; background: #ffffff; color: #1f2937; line-height: 1.6;'>
                    {$bodyContent}
                </div>
                <div style='background-color: #f9fafb; padding: 20px; text-align: center; font-size: 12px; color: #6b7280; border-top: 1px solid #e5e7eb;'>
                    &copy; " . date('Y') . " {$siteName} - Todos os direitos reservados.
                </div>
            </div>";

            Mailer::send($rma['client_email'], $subject, $emailBody);
        }

        // 2. Enviar WhatsApp
        $allowSmsWa = (int)($rma['allow_sms_whatsapp'] ?? 0);
        if ($allowSmsWa === 1 && !empty($rma['client_contact'])) {
            $waTpl = Helpers::getSetting("whatsapp_tpl_{$type}", Helpers::getSetting("whatsapp_tpl_{$type}_pt", Helpers::__("default_whatsapp_{$type}")));
            $waMessage = str_replace(array_keys($placeholders), array_values($placeholders), $waTpl);
            WhatsApp::send($rma['client_contact'], $waMessage);
        }
    }

    /**
     * Envia notificações administrativas/técnicas para utilizadores/emails configurados.
     */
    private function sendAdminNotifications(string $event, array $data) {
        $rulesJson = Helpers::getSetting('notification_rules', '[]');
        $rules = json_decode($rulesJson, true) ?: [];
        if (empty($rules)) {
            return;
        }

        $siteName = Helpers::getSetting('site_name', 'REP Gest');
        $baseUrl = ($_SERVER['REQUEST_SCHEME'] ?? 'http') . "://{$_SERVER['HTTP_HOST']}" . dirname($_SERVER['PHP_SELF']);
        
        $subject = '';
        $bodyContent = '';

        if ($event === 'repair_request') {
            $subject = "[{$siteName}] Novo Pedido de Reparação - " . ($data['rma_number'] ?? '');
            $detailUrl = $baseUrl . "/index.php?route=tech/rma-detail&id=" . ($data['id'] ?? '');
            
            $bodyContent = "
                <p>Olá,</p>
                <p>Foi registado um novo pedido de reparação no site:</p>
                <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;'>Número:</td><td style='padding: 8px; border-bottom: 1px solid #eee;'>{$data['rma_number']}</td></tr>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;'>Cliente:</td><td style='padding: 8px; border-bottom: 1px solid #eee;'>{$data['client_name']}</td></tr>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;'>Email:</td><td style='padding: 8px; border-bottom: 1px solid #eee;'>{$data['client_email']}</td></tr>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;'>Contacto:</td><td style='padding: 8px; border-bottom: 1px solid #eee;'>{$data['client_contact']}</td></tr>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;'>Equipamento:</td><td style='padding: 8px; border-bottom: 1px solid #eee;'>{$data['device_type']}</td></tr>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;'>Avaria:</td><td style='padding: 8px; border-bottom: 1px solid #eee;'>{$data['device_condition']}</td></tr>
                </table>
                <p><a href='{$detailUrl}' style='display: inline-block; background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold;'>Ver Detalhes da Reparação</a></p>
            ";
        } elseif ($event === 'chat_message') {
            $subject = "[{$siteName}] Nova Mensagem no Processo " . ($data['rma_number'] ?? '');
            $detailUrl = $baseUrl . "/index.php?route=tech/rma-detail&id=" . ($data['rma_id'] ?? '');
            
            $bodyContent = "
                <p>Olá,</p>
                <p>O cliente <strong>{$data['client_name']}</strong> enviou uma nova mensagem no chat do processo <strong>{$data['rma_number']}</strong>:</p>
                <blockquote style='border-left: 4px solid #4f46e5; padding-left: 15px; margin: 20px 0; color: #555; font-style: italic;'>
                    " . nl2br(htmlspecialchars($data['message'])) . "
                </blockquote>
                <p><a href='{$detailUrl}' style='display: inline-block; background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold;'>Ir para a Conversa</a></p>
            ";
        }

        $emailBody = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05);'>
            <div style='background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); padding: 25px; text-align: center; color: white;'>
                <h2 style='margin: 0; font-size: 22px; font-weight: 800;'>{$siteName} - Notificação Admin</h2>
            </div>
            <div style='padding: 30px; background: #ffffff; color: #1f2937; line-height: 1.6;'>
                {$bodyContent}
            </div>
            <div style='background-color: #f9fafb; padding: 15px; text-align: center; font-size: 11px; color: #6b7280; border-top: 1px solid #e5e7eb;'>
                Esta é uma mensagem automática gerada pelo sistema de notificações do {$siteName}.
            </div>
        </div>";

        $emails = [];
        $db = Database::getInstance();

        foreach ($rules as $rule) {
            if (($rule['event'] ?? '') !== $event) {
                continue;
            }

            if (($rule['target_type'] ?? '') === 'email') {
                $emails[] = $rule['target_value'];
            } elseif (($rule['target_type'] ?? '') === 'user') {
                $userId = (int)$rule['target_value'];
                $stmtUser = $db->prepare("SELECT email FROM users WHERE id = ?");
                $stmtUser->execute([$userId]);
                $userEmail = $stmtUser->fetchColumn();
                if ($userEmail) {
                    $emails[] = $userEmail;
                }
            }
        }

        $emails = array_unique(array_filter($emails));
        foreach ($emails as $email) {
            Mailer::send($email, $subject, $emailBody);
        }
    }
}

