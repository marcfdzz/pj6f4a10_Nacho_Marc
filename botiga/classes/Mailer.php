<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Classe Mailer - Gestió d'enviament de correus electrònics
 * Utilitza PHPMailer per enviar correus
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
            
            // Crear instància de PHPMailer
            $mail = new PHPMailer(true);
            
            // Configuració del servidor SMTP
            $mail->isSMTP();
            $mail->Host = self::$smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = self::$smtpUser;
            $mail->Password = self::$smtpPass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = self::$smtpPort;
            $mail->CharSet = 'UTF-8';
            
            // Remitent i destinatari
            $mail->setFrom(self::$fromEmail, self::$fromName);
            $mail->addAddress($to);
            
            // Contingut del correu
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            
            // Enviar correu
            $mail->send();
            
            self::log("Correu enviat correctament a: $to");
            return true;
            
        } catch (Exception $e) {
            $errorMsg = isset($mail) ? $mail->ErrorInfo : $e->getMessage();
            self::log("Error enviant correu: " . $errorMsg);
            return false;
        }
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
