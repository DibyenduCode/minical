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

        $siteName = self::getSetting('site_name') ?: 'MiniCal';
        
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
            
            <div style='padding: 20px; text-align: center; font-size: 11px; color: #94a3b8; border-t: 1px solid #e2e8f0; background-color: #f8fafc;'>
                Sent automatically by {$siteName} scheduling system.
            </div>
        </div>
        ";

        // Setup headers
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8';
        $headers[] = 'From: ' . $siteName . ' <noreply@' . ($_SERVER['HTTP_HOST'] ?? 'minical.local') . '>';

        // Send confirmation to Client (Suppress warning on local development if mail server is not configured)
        @mail($booking['customer_email'], $subject, $htmlBody, implode("\r\n", $headers));

        // Send notification to Host
        $hostSubject = "New Booking: " . $booking['customer_name'] . " - " . $booking['event_name'];
        @mail($booking['host_email'], $hostSubject, $htmlBody, implode("\r\n", $headers));

        return true;
    }

    private static function getSetting(string $key): string {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT `setting_value` FROM `settings` WHERE `setting_key` = :key LIMIT 1");
        $stmt->execute(['key' => $key]);
        return (string)$stmt->fetchColumn();
    }
}
