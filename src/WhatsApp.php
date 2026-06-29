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

class WhatsApp {
    
    /**
     * Envia uma mensagem via WhatsApp se o serviço estiver ativo e o utilizador tiver autorizado.
     */
    public static function send(string $to, string $message): bool {
        $enabled = Helpers::getSetting('whatsapp_enabled', '0');
        if ($enabled !== '1') {
            return false;
        }

        $url = Helpers::getSetting('whatsapp_url', '');
        $token = Helpers::getSetting('whatsapp_token', '');
        $tokenHeader = Helpers::getSetting('whatsapp_token_header', 'Authorization');
        $payloadTemplate = Helpers::getSetting('whatsapp_payload', '{"number": "{phone}", "message": "{message}"}');

        if (empty($url)) {
            return false;
        }

        // Limpar telefone (manter apenas dígitos, e opcionalmente o prefixo +)
        $cleanPhone = preg_replace('/[^0-9+]/', '', $to);

        // Se o número de telefone começar com o indicativo nacional sem +, ou se for menor que o necessário, deixamos passar
        // mas é ideal enviar limpo para o gateway.
        
        // Fazer escape apropriado da mensagem para JSON
        $escapedMessage = json_encode($message, JSON_UNESCAPED_UNICODE);
        // Remover as aspas duplas iniciais/finais geradas pelo json_encode
        $escapedMessage = substr($escapedMessage, 1, -1);

        // Substituir os placeholders no payload
        $payload = str_replace(
            ['{phone}', '{message}'],
            [$cleanPhone, $escapedMessage],
            $payloadTemplate
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $headers = [
            'Content-Type: application/json'
        ];

        if (!empty($token)) {
            if (strtolower($tokenHeader) === 'authorization') {
                $headers[] = "Authorization: Bearer " . $token;
            } else {
                $headers[] = $tokenHeader . ": " . $token;
            }
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Permitir certificados auto-assinados em desenvolvimento

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode >= 200 && $httpCode < 300;
    }
}

