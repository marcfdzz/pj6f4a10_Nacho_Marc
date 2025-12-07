<?php
class Mailer {
    public static function send($to, $subject, $message) {
        // Simulation of email sending
        // In a real environment, use mail() or PHPMailer
        // For this project, we assume it works and maybe log it
        $log = __DIR__ . '/../mail_log.txt';
        $entry = date('Y-m-d H:i:s') . " | To: $to | Subject: $subject\n";
        file_put_contents($log, $entry, FILE_APPEND);
        return true;
    }
}
?>