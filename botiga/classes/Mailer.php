<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class Mailer {
    private static $configPath = __DIR__ . '/../mail_config.ini';

    public static function send($to, $subject, $body) {
        // Log attempt
        $logFile = __DIR__ . '/../mail_log.txt';
        $timestamp = date('Y-m-d H:i:s');
        
        if (!file_exists(self::$configPath)) {
            $msg = "$timestamp | Error: Config file not found at " . self::$configPath . "\n";
            file_put_contents($logFile, $msg, FILE_APPEND);
            return false;
        }

        $config = parse_ini_file(self::$configPath, true);
        if (!$config || !isset($config['smtp'])) {
            $msg = "$timestamp | Error: Invalid config file structure\n";
            file_put_contents($logFile, $msg, FILE_APPEND);
            return false;
        }

        $smtpObj = $config['smtp'];
        
        // Validation of credentials
        if ($smtpObj['password'] === 'la_teva_contrasenya_d_aplicacio') {
            $msg = "$timestamp | Error: Default credentials detected. Please edit mail_config.ini\n";
            file_put_contents($logFile, $msg, FILE_APPEND);
            return false;
        }

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $smtpObj['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtpObj['username'];
            $mail->Password   = $smtpObj['password'];
            $mail->SMTPSecure = $smtpObj['security'];
            $mail->Port       = $smtpObj['port'];

            // Recipients
            $mail->setFrom($smtpObj['from_email'], $smtpObj['from_name']);
            $mail->addAddress($to);
            $mail->addReplyTo($smtpObj['from_email'], $smtpObj['from_name']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = nl2br($body);
            $mail->AltBody = strip_tags($body);

            $mail->send();
            
            $msg = "$timestamp | Success | To: $to | Subject: $subject\n";
            file_put_contents($logFile, $msg, FILE_APPEND);
            return true;
        } catch (Exception $e) {
            $errorMsg = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            // Log for dev
            file_put_contents($logFile, "$timestamp | Fail | $errorMsg\n", FILE_APPEND);
            error_log($errorMsg);
            return false;
        }
    }

    public static function getAdminEmail() {
        if (!file_exists(self::$configPath)) return 'admin@example.com';
        $config = parse_ini_file(self::$configPath, true);
        return $config['smtp']['admin_email'] ?? 'admin@
        
        
        .com';
    }
}
?>