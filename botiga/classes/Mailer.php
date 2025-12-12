<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    private static $smtpHost = 'smtp.gmail.com';
    private static $smtpPort = 587;
    private static $smtpUser = 'marc.fernandezv@gmail.com';
    private static $smtpPass = 'ngky dlyo rwer uecf';
    private static $fromEmail = 'marc.fernandezv@gmail.com';
    private static $fromName = 'SportVibe';
    private static $adminEmail = 'marc.fernandezv@gmail.com';
    
    public static function send($to, $subject, $body) {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            self::log("Email invàlid: $to");
            return false;
        }
        
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = self::$smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = self::$smtpUser;
            $mail->Password = self::$smtpPass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = self::$smtpPort;
            $mail->CharSet = 'UTF-8';
            $mail->setFrom(self::$fromEmail, self::$fromName);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->send();
            self::log("Correu enviat a: $to");
            return true;
        } catch (Exception $e) {
            $errorMsg = isset($mail) ? $mail->ErrorInfo : $e->getMessage();
            self::log("Error: " . $errorMsg);
            return false;
        }
    }
    
    public static function getAdminEmail() {
        return self::$adminEmail;
    }
    
    private static function log($message) {
        $logFile = __DIR__ . '/../mail_log.txt';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
    }
}
?>
