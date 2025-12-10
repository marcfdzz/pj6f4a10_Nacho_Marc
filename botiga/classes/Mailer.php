<?php

/**
 * Classe Mailer - Gestió d'enviament de correus electrònics
 * Utilitza la funció mail() de PHP amb configuració SMTP de Gmail
 */
class Mailer {
    
    // Configuració del correu
    private static $smtpHost = 'smtp.gmail.com';
    private static $smtpPort = 587;
    private static $smtpUser = 'marc.fernandezv@gmail.com';
    private static $smtpPass = 'ngky dlyo rwer uecf';
    private static $fromEmail = 'marc.fernandezv@gmail.com';
    private static $fromName = 'SportVibe';
    private static $adminEmail = 'marc.fernandezv@gmail.com';
    
    /**
     * Envia un correu electrònic
     * 
     * @param string $to Destinatari
     * @param string $subject Assumpte
     * @param string $body Cos del missatge (HTML)
     * @return bool True si s'ha enviat correctament, false en cas contrari
     */
    public static function send($to, $subject, $body) {
        try {
            // Validar email destinatari
            if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
                self::log("Email destinatari invàlid: $to");
                return false;
            }
            
            // Crear socket de connexió SMTP
            $socket = @fsockopen(self::$smtpHost, self::$smtpPort, $errno, $errstr, 30);
            
            if (!$socket) {
                self::log("Error connectant al servidor SMTP: $errstr ($errno)");
                return false;
            }
            
            // Llegir resposta inicial
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '220') {
                self::log("Error en resposta inicial SMTP: $response");
                fclose($socket);
                return false;
            }
            
            // EHLO
            fputs($socket, "EHLO " . self::$smtpHost . "\r\n");
            $response = self::readResponse($socket);
            
            // STARTTLS
            fputs($socket, "STARTTLS\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '220') {
                self::log("Error en STARTTLS: $response");
                fclose($socket);
                return false;
            }
            
            // Activar encriptació TLS
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            
            // EHLO després de TLS
            fputs($socket, "EHLO " . self::$smtpHost . "\r\n");
            $response = self::readResponse($socket);
            
            // AUTH LOGIN
            fputs($socket, "AUTH LOGIN\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '334') {
                self::log("Error en AUTH LOGIN: $response");
                fclose($socket);
                return false;
            }
            
            // Enviar usuari (base64)
            fputs($socket, base64_encode(self::$smtpUser) . "\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '334') {
                self::log("Error en autenticació usuari: $response");
                fclose($socket);
                return false;
            }
            
            // Enviar contrasenya (base64)
            fputs($socket, base64_encode(self::$smtpPass) . "\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '235') {
                self::log("Error en autenticació contrasenya: $response");
                fclose($socket);
                return false;
            }
            
            // MAIL FROM
            fputs($socket, "MAIL FROM: <" . self::$fromEmail . ">\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '250') {
                self::log("Error en MAIL FROM: $response");
                fclose($socket);
                return false;
            }
            
            // RCPT TO
            fputs($socket, "RCPT TO: <$to>\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '250') {
                self::log("Error en RCPT TO: $response");
                fclose($socket);
                return false;
            }
            
            // DATA
            fputs($socket, "DATA\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '354') {
                self::log("Error en DATA: $response");
                fclose($socket);
                return false;
            }
            
            // Capçaleres del correu
            $headers = "From: " . self::$fromName . " <" . self::$fromEmail . ">\r\n";
            $headers .= "Reply-To: " . self::$fromEmail . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "Subject: " . self::encodeSubject($subject) . "\r\n";
            
            // Cos del missatge
            $message = $headers . "\r\n" . $body . "\r\n.\r\n";
            
            // Enviar missatge
            fputs($socket, $message);
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '250') {
                self::log("Error enviant missatge: $response");
                fclose($socket);
                return false;
            }
            
            // QUIT
            fputs($socket, "QUIT\r\n");
            fclose($socket);
            
            self::log("Correu enviat correctament a: $to");
            return true;
            
        } catch (Exception $e) {
            self::log("Excepció en enviar correu: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Llegeix la resposta completa del servidor SMTP
     */
    private static function readResponse($socket) {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) == ' ') {
                break;
            }
        }
        return $response;
    }
    
    /**
     * Codifica l'assumpte del correu
     */
    private static function encodeSubject($subject) {
        return '=?UTF-8?B?' . base64_encode($subject) . '?=';
    }
    
    /**
     * Obté l'email de l'administrador
     * 
     * @return string Email de l'administrador
     */
    public static function getAdminEmail() {
        return self::$adminEmail;
    }
    
    /**
     * Registra missatges en el log
     * 
     * @param string $message Missatge a registrar
     */
    private static function log($message) {
        $logFile = __DIR__ . '/../mail_log.txt';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}
?>
