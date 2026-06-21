<?php
class Mailer {
    public static function send(string $to, string $subject, string $body): bool {
        $smtpHost = Setting::get('smtp_host');
        $smtpUser = Setting::get('smtp_user');
        $smtpPass = Setting::get('smtp_pass');
        $smtpPort = Setting::get('smtp_port', '587');
        $fromEmail = Setting::get('smtp_from', 'noreply@crusaderworks.local');
        $fromName  = Setting::get('app_name', 'Crusader Works');

        if ($smtpHost && $smtpUser) {
            return self::sendSmtp($to, $subject, $body, $smtpHost, $smtpUser, $smtpPass, (int)$smtpPort, $fromEmail, $fromName);
        }

        $headers = "MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\nFrom: $fromName <$fromEmail>\r\n";
        return mail($to, $subject, $body, $headers);
    }

    private static function sendSmtp(string $to, string $subject, string $body, string $host, string $user, string $pass, int $port, string $from, string $fromName): bool {
        try {
            $socket = fsockopen(($port === 465 ? 'ssl://' : '') . $host, $port, $errno, $errstr, 10);
            if (!$socket) return false;

            $read = fn() => fgets($socket, 512);
            $write = function(string $cmd) use ($socket) { fwrite($socket, $cmd . "\r\n"); };

            $read(); // greeting
            $write("EHLO localhost");
            while ($line = $read()) { if (substr($line,3,1)==' ') break; }

            if ($port === 587) {
                $write("STARTTLS");
                $read();
                stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                $write("EHLO localhost");
                while ($line = $read()) { if (substr($line,3,1)==' ') break; }
            }

            $write("AUTH LOGIN");
            $read();
            $write(base64_encode($user));
            $read();
            $write(base64_encode($pass));
            $read();

            $write("MAIL FROM:<$from>");
            $read();
            $write("RCPT TO:<$to>");
            $read();
            $write("DATA");
            $read();

            $headers = "From: $fromName <$from>\r\nTo: $to\r\nSubject: $subject\r\n";
            $headers .= "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";
            $write($headers . "\r\n" . $body . "\r\n.");
            $read();
            $write("QUIT");
            fclose($socket);
            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
