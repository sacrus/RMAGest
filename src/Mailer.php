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

use RmaGest\Helpers;

class Mailer {

    /**
     * Envia um email para o destinatário utilizando SMTP (via Socket) ou a função mail() do PHP.
     */
    public static function send(string $to, string $subject, string $htmlContent): bool {
        $smtpEnabled = Helpers::getSetting('smtp_enabled', '0') === '1';
        $fromEmail = Helpers::getSetting('smtp_from_email', 'noreply@rmagest.local');
        $fromName = Helpers::getSetting('smtp_from_name', Helpers::getSetting('site_name', 'RMA Gest'));

        // Se for para usar SMTP
        if ($smtpEnabled) {
            $host = Helpers::getSetting('smtp_host', '');
            $port = (int)Helpers::getSetting('smtp_port', '25');
            $user = Helpers::getSetting('smtp_user', '');
            $pass = Helpers::getSetting('smtp_pass', '');
            $encryption = Helpers::getSetting('smtp_encryption', 'none'); // 'none', 'ssl', 'tls'

            $result = self::sendSmtp($host, $port, $encryption, $user, $pass, $fromEmail, $fromName, $to, $subject, $htmlContent);
            if ($result) {
                return true;
            }
            // Se falhar o SMTP, faz fallback para mail() nativo se estiver configurado
        }

        // Fallback: Envio usando a função mail() nativa do PHP
        return self::sendNativeMail($fromEmail, $fromName, $to, $subject, $htmlContent);
    }

    /**
     * Envia email usando a função mail() nativa do PHP.
     */
    private static function sendNativeMail(string $fromEmail, string $fromName, string $to, string $subject, string $htmlContent): bool {
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=utf-8',
            "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>",
            'Reply-To: ' . $fromEmail,
            'X-Mailer: PHP/' . phpversion()
        ];

        // Codificar o assunto para suportar caracteres especiais (ex: acentos portugueses)
        $encodedSubject = "=?UTF-8?B?" . base64_encode($subject) . "?=";

        return @mail($to, $encodedSubject, $htmlContent, implode("\r\n", $headers));
    }

    /**
     * Envia email via ligação de Socket SMTP pura (sem bibliotecas externas pesadas).
     */
    private static function sendSmtp(
        string $host, int $port, string $encryption, 
        string $user, string $pass, 
        string $fromEmail, string $fromName, 
        string $to, string $subject, string $htmlContent
    ): bool {
        if (empty($host)) {
            return false;
        }

        $socketHost = $host;
        if ($encryption === 'ssl') {
            $socketHost = 'ssl://' . $host;
        }

        $socket = @fsockopen($socketHost, $port, $errno, $errstr, 15);
        if (!$socket) {
            return false;
        }

        try {
            self::readResponse($socket, 220);

            // Envia EHLO
            self::writeCommand($socket, "EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost'), 250);

            // STARTTLS se selecionado
            if ($encryption === 'tls') {
                self::writeCommand($socket, "STARTTLS", 220);
                if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    fclose($socket);
                    return false;
                }
                // Envia EHLO novamente após criptografia ativa
                self::writeCommand($socket, "EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost'), 250);
            }

            // Autenticação se utilizador for fornecido
            if (!empty($user)) {
                self::writeCommand($socket, "AUTH LOGIN", 334);
                self::writeCommand($socket, base64_encode($user), 334);
                self::writeCommand($socket, base64_encode($pass), 235);
            }

            // MAIL FROM & RCPT TO
            self::writeCommand($socket, "MAIL FROM: <{$fromEmail}>", 250);
            self::writeCommand($socket, "RCPT TO: <{$to}>", 250);

            // DATA
            self::writeCommand($socket, "DATA", 354);

            // Construir cabeçalhos de email RFC 822
            $headers = [
                "MIME-Version: 1.0",
                "Content-Type: text/html; charset=UTF-8",
                "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>",
                "To: <{$to}>",
                "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=",
                "Date: " . date('r'),
                "X-Mailer: RMA Gest SMTP Client"
            ];

            $message = implode("\r\n", $headers) . "\r\n\r\n" . $htmlContent . "\r\n.\r\n";
            
            fwrite($socket, $message);
            self::readResponse($socket, 250);

            // QUIT
            self::writeCommand($socket, "QUIT", 221);
            fclose($socket);
            return true;
        } catch (\Exception $e) {
            fclose($socket);
            return false;
        }
    }

    private static function readResponse($socket, int $expectedCode) {
        $response = "";
        while ($str = fgets($socket, 515)) {
            $response .= $str;
            if (substr($str, 3, 1) === " ") {
                break;
            }
        }
        $code = (int)substr($response, 0, 3);
        if ($code !== $expectedCode) {
            throw new \Exception("SMTP Error: Expected {$expectedCode}, received {$response}");
        }
        return $response;
    }

    private static function writeCommand($socket, string $command, int $expectedCode) {
        fwrite($socket, $command . "\r\n");
        return self::readResponse($socket, $expectedCode);
    }
}

