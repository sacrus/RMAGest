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
 * RMA Gest - Sistema de Gestão de Fichas de Reparação e RMA
 * 
 * Este ficheiro atua como o Front Controller principal da aplicação.
 * Redireciona para o instalador se o sistema não estiver configurado,
 * e despacha os restantes pedidos para o núcleo lógico (Controller).
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$configFile = __DIR__ . '/config.php';

// Se o ficheiro de configuração não existe, redireciona para o instalador.
if (!file_exists($configFile)) {
    header('Location: install.php');
    exit;
}

// Carregar as classes núcleo do sistema
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Helpers.php';
require_once __DIR__ . '/src/Auth.php';
require_once __DIR__ . '/src/Mailer.php';
require_once __DIR__ . '/src/WhatsApp.php';
require_once __DIR__ . '/src/Controller.php';

// Instanciar e processar o pedido
$controller = new \RmaGest\Controller();
$controller->handleRequest();

