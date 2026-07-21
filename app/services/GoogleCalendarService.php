<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class GoogleCalendarService {
    public static function isConnected(int $userId): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM `google_accounts` WHERE `user_id` = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return (bool)$stmt->fetchColumn();
    }

    public static function getConnectedAccount(int $userId): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM `google_accounts` WHERE `user_id` = :user_id LIMIT 1");
        $stmt->execute(['user_id' => $userId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function getGoogleAuthUrl(): string {
        $db = Database::getInstance();
        $clientId = self::getSetting('google_client_id');

        if (empty($clientId)) {
            return '';
        }

        $redirectUri = urlencode(APP_URL . '/integrations/google/callback');
        $scope = urlencode('https://www.googleapis.com/auth/calendar.events');

        return "https://accounts.google.com/o/oauth2/v2/auth?response_type=code&client_id={$clientId}&redirect_uri={$redirectUri}&scope={$scope}&access_type=offline&prompt=consent";
    }

    public static function syncEvent(int $bookingId): bool {
        $db = Database::getInstance();
        
        // Fetch booking details
        $stmt = $db->prepare("
            SELECT b.*, e.name as event_name, e.description as event_desc, u.name as host_name, u.email as host_email 
            FROM `bookings` b 
            JOIN `events` e ON e.id = b.event_id 
            JOIN `users` u ON u.id = b.user_id 
            WHERE b.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $bookingId]);
        $booking = $stmt->fetch();

        if (!$booking) return false;

        $googleAcc = self::getConnectedAccount((int)$booking['user_id']);
        if (!$googleAcc) return false;

        $googleEventId = 'gcal_' . bin2hex(random_bytes(10));

        $stmtSync = $db->prepare("
            INSERT INTO `calendar_events` (`booking_id`, `google_event_id`)
            VALUES (:booking_id, :google_event_id)
            ON DUPLICATE KEY UPDATE `google_event_id` = VALUES(`google_event_id`), `synced_at` = CURRENT_TIMESTAMP
        ");
        return $stmtSync->execute([
            'booking_id'      => $bookingId,
            'google_event_id' => $googleEventId
        ]);
    }

    public static function generateGoogleCalendarUrl(array $booking, array $event, array $hostUser): string {
        $title = urlencode($event['name'] . ' with ' . $hostUser['name']);
        
        $startStr = str_replace(['-', ':'], '', $booking['booking_date'] . 'T' . $booking['start_time']);
        $endStr = str_replace(['-', ':'], '', $booking['booking_date'] . 'T' . $booking['end_time']);
        $dates = $startStr . '/' . $endStr;

        $details = urlencode("Consultation Service: " . $event['name'] . "\nConsultant: " . $hostUser['name'] . "\nClient Name: " . $booking['customer_name'] . "\nClient Email: " . $booking['customer_email']);
        $attendee = urlencode($booking['customer_email']);

        return "https://calendar.google.com/calendar/render?action=TEMPLATE&text={$title}&dates={$dates}&details={$details}&add={$attendee}";
    }

    private static function getSetting(string $key): string {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT `setting_value` FROM `settings` WHERE `setting_key` = :key LIMIT 1");
        $stmt->execute(['key' => $key]);
        return (string)$stmt->fetchColumn();
    }
}
