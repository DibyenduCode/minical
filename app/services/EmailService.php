<?php

namespace App\Services;

use App\Core\Database;

class EmailService {

    public static function sendBookingConfirmation(int $bookingId): bool {
        $db = Database::getInstance();

        // Fetch full booking, event, host, and system settings
        $stmt = $db->prepare("
            SELECT b.*, e.name as event_name, e.location_type, u.name as host_name, u.email as host_email 
            FROM `bookings` b 
            JOIN `events` e ON e.id = b.event_id 
            JOIN `users` u ON u.id = b.user_id 
            WHERE b.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $bookingId]);
        $booking = $stmt->fetch();

        if (!$booking) return false;

        $siteName = self::getSetting('site_name') ?: 'DayCal';
        
        // Format Email Body
        $subject = "Booking Confirmed: " . $booking['event_name'] . " with " . $booking['host_name'];

        $meetSection = '';
        if (!empty($booking['meeting_link'])) {
            $meetSection = "
                <div style='margin: 20px 0; padding: 15px; background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 12px;'>
                    <p style='margin: 0 0 8px 0; font-size: 13px; font-weight: bold; color: #065f46;'>🎥 Google Meet Video Call</p>
                    <a href='{$booking['meeting_link']}' target='_blank' style='display: inline-block; padding: 10px 20px; background-color: #059669; color: #ffffff; text-decoration: none; font-size: 13px; font-weight: bold; border-radius: 8px;'>Join Google Meet Call</a>
                    <p style='margin: 8px 0 0 0; font-size: 11px; color: #047857;'>Meeting Link: {$booking['meeting_link']}</p>
                </div>
            ";
        }

        $htmlBody = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; color: #333333; line-height: 1.6;'>
            <div style='padding: 20px; text-align: center; background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;'>
                <h1 style='margin: 0; font-size: 22px; color: #0f172a;'>Appointment Confirmed!</h1>
                <p style='margin: 5px 0 0 0; font-size: 14px; color: #64748b;'>Thank you for scheduling with {$siteName}.</p>
            </div>
            
            <div style='padding: 20px;'>
                <p>Hi {$booking['customer_name']},</p>
                <p>Your appointment has been successfully scheduled. Here are the meeting details:</p>
                
                <table style='width: 100%; border-collapse: collapse; margin-top: 15px;'>
                     <tr>
                         <td style='padding: 8px 0; font-weight: bold; color: #64748b; font-size: 13px;'>Consultation</td>
                         <td style='padding: 8px 0; font-weight: bold; color: #0f172a; font-size: 13px;'>{$booking['event_name']}</td>
                     </tr>
                     <tr>
                         <td style='padding: 8px 0; font-weight: bold; color: #64748b; font-size: 13px;'>Host/Consultant</td>
                         <td style='padding: 8px 0; color: #334155; font-size: 13px;'>{$booking['host_name']}</td>
                     </tr>
                     <tr>
                         <td style='padding: 8px 0; font-weight: bold; color: #64748b; font-size: 13px;'>Date</td>
                         <td style='padding: 8px 0; color: #334155; font-size: 13px;'>" . date('F j, Y', strtotime($booking['booking_date'])) . "</td>
                     </tr>
                     <tr>
                         <td style='padding: 8px 0; font-weight: bold; color: #64748b; font-size: 13px;'>Time</td>
                         <td style='padding: 8px 0; color: #334155; font-size: 13px;'>" . date('h:i A', strtotime($booking['start_time'])) . " - " . date('h:i A', strtotime($booking['end_time'])) . "</td>
                     </tr>
                </table>
                
                {$meetSection}
                
                <p style='margin-top: 25px; font-size: 13px; color: #64748b;'>Need to make changes? Reach out to your consultant directly at <a href='mailto:{$booking['host_email']}'>{$booking['host_email']}</a>.</p>
            </div>
            
            <div style='padding: 20px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0; background-color: #f8fafc;'>
                Sent automatically by {$siteName} scheduling system.
            </div>
        </div>
        ";

        // Send confirmation to Client
        self::sendSmtp($booking['customer_email'], $subject, $htmlBody);

        // Send notification to Host
        $hostSubject = "New Booking: " . $booking['customer_name'] . " - " . $booking['event_name'];
        self::sendSmtp($booking['host_email'], $hostSubject, $htmlBody);

        return true;
    }

    public static function sendPasswordReset(string $email, string $resetLink): bool {
        $siteName = self::getSetting('site_name') ?: 'DayCal';
        $subject = "Reset Your Password - " . $siteName;

        $htmlBody = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; color: #333333; line-height: 1.6;'>
            <div style='padding: 20px; text-align: center; background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;'>
                <h1 style='margin: 0; font-size: 22px; color: #0f172a;'>Password Reset Request</h1>
                <p style='margin: 5px 0 0 0; font-size: 14px; color: #64748b;'>Requested for your {$siteName} account.</p>
            </div>
            
            <div style='padding: 20px;'>
                <p>Hello,</p>
                <p>We received a request to reset your password. Click the button below to set a new password. This link is valid for 1 hour:</p>
                
                <div style='margin: 30px 0; text-align: center;'>
                    <a href='{$resetLink}' target='_blank' style='display: inline-block; padding: 12px 24px; background-color: #000000; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: bold; border-radius: 8px;'>Reset Password</a>
                </div>
                
                <p style='font-size: 12px; color: #64748b;'>If the button doesn't work, copy and paste this link in your browser:</p>
                <p style='font-size: 11px; color: #3b82f6; word-break: break-all;'>{$resetLink}</p>
                
                <p style='margin-top: 25px; font-size: 13px; color: #64748b;'>If you did not request this, you can safely ignore this email.</p>
            </div>
            
            <div style='padding: 20px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0; background-color: #f8fafc;'>
                Sent automatically by {$siteName} scheduling platform.
            </div>
        </div>
        ";

        return self::sendSmtp($email, $subject, $htmlBody);
    }

    public static function sendSmtp(string $to, string $subject, string $htmlBody): bool {
        $db = Database::getInstance();
        $settingsStmt = $db->query("SELECT * FROM `settings`")->fetchAll();
        $settings = [];
        foreach ($settingsStmt as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        $smtpHost = $settings['smtp_host'] ?? '';
        $smtpPort = (int)($settings['smtp_port'] ?? 587);
        $smtpUser = $settings['smtp_user'] ?? '';
        $smtpPass = $settings['smtp_pass'] ?? '';
        $siteName = $settings['site_name'] ?? 'DayCal';

        if (empty($smtpHost)) {
            // Fallback to PHP's built-in mail() function
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=utf-8';
            $headers[] = 'From: ' . $siteName . ' <noreply@' . ($_SERVER['HTTP_HOST'] ?? 'daycal.in') . '>';
            return @mail($to, $subject, $htmlBody, implode("\r\n", $headers));
        }

        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);

        $hostSpec = $smtpHost;
        if ($smtpPort === 465) {
            $hostSpec = 'ssl://' . $smtpHost;
        }

        $socket = @stream_socket_client($hostSpec . ':' . $smtpPort, $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $context);
        if (!$socket) {
            error_log("SMTP Connection Error: $errstr ($errno)");
            // Fallback to PHP built-in mail() if socket fails
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=utf-8';
            $headers[] = 'From: ' . $siteName . ' <noreply@' . ($_SERVER['HTTP_HOST'] ?? 'daycal.in') . '>';
            return @mail($to, $subject, $htmlBody, implode("\r\n", $headers));
        }

        $getResponse = function($socket) {
            $response = '';
            while ($line = fgets($socket, 512)) {
                $response .= $line;
                if (substr($line, 3, 1) === ' ') {
                    break;
                }
            }
            return $response;
        };

        $sendCommand = function($socket, $cmd) use ($getResponse) {
            fputs($socket, $cmd . "\r\n");
            return $getResponse($socket);
        };

        $getResponse($socket);
        $ehlo = $sendCommand($socket, "EHLO " . ($_SERVER['HTTP_HOST'] ?? 'localhost'));

        if ($smtpPort === 587 && strpos($ehlo, 'STARTTLS') !== false) {
            $starttls = $sendCommand($socket, "STARTTLS");
            if (strpos($starttls, '220') !== false) {
                if (@stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    $sendCommand($socket, "EHLO " . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
                }
            }
        }

        if (!empty($smtpUser) && !empty($smtpPass)) {
            $auth = $sendCommand($socket, "AUTH LOGIN");
            if (strpos($auth, '334') !== false) {
                $sendCommand($socket, base64_encode($smtpUser));
                $passResp = $sendCommand($socket, base64_encode($smtpPass));
                if (strpos($passResp, '235') === false) {
                    error_log("SMTP Auth Error: Credentials rejected. " . $passResp);
                    fclose($socket);
                    return false;
                }
            }
        }

        $fromEmail = !empty($smtpUser) && filter_var($smtpUser, FILTER_VALIDATE_EMAIL) ? $smtpUser : 'noreply@' . ($_SERVER['HTTP_HOST'] ?? 'minical.local');
        $sendCommand($socket, "MAIL FROM:<" . $fromEmail . ">");

        $rcpt = $sendCommand($socket, "RCPT TO:<" . $to . ">");
        if (strpos($rcpt, '250') === false && strpos($rcpt, '251') === false) {
            error_log("SMTP RCPT TO Error: " . $rcpt);
            fclose($socket);
            return false;
        }

        $sendCommand($socket, "DATA");

        $headers = [
            "MIME-Version: 1.0",
            "Content-Type: text/html; charset=UTF-8",
            "From: =?UTF-8?B?" . base64_encode($siteName) . "?= <" . $fromEmail . ">",
            "To: <" . $to . ">",
            "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=",
            "Date: " . date('r'),
            "Message-ID: <" . uniqid('', true) . "@" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ">"
        ];

        $message = implode("\r\n", $headers) . "\r\n\r\n" . $htmlBody . "\r\n.";
        $dataResp = $sendCommand($socket, $message);

        $sendCommand($socket, "QUIT");
        fclose($socket);

        return strpos($dataResp, '250') !== false;
    }

    private static function getSetting(string $key): string {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT `setting_value` FROM `settings` WHERE `setting_key` = :key LIMIT 1");
        $stmt->execute(['key' => $key]);
        return (string)$stmt->fetchColumn();
    }
}
