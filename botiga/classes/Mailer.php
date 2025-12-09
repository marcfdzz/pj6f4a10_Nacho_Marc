<?php
class Mailer {
    public static function send($to, $subject, $message) {
        // 1. Log to file (always useful for debugging)
        $log = __DIR__ . '/../mail_log.txt';
        $entry = date('Y-m-d H:i:s') . " | To: $to | Subject: $subject\n";
        file_put_contents($log, $entry, FILE_APPEND);

        // 2. Try to send real email using PHP mail()
        // Note: This requires SMTP configuration in php.ini and sendmail.ini on XAMPP
        $headers = 'From: noreply@botiga.local' . "\r\n" .
                   'Reply-To: noreply@botiga.local' . "\r\n" .
                   'X-Mailer: PHP/' . phpversion();

        // Suppress errors with @ to avoid breaking the page if config is missing
        return @mail($to, $subject, $message, $headers);
    }
}
?>