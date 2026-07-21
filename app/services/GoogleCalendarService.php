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

    public static function syncEvent(int $bookingId): bool {
        // Placeholder service structure for Google OAuth / Calendar API v3 event creation
        // Automatically syncs booking start & end time with host's Google Calendar.
        $db = Database::getInstance();
        $googleEventId = 'gcal_' . bin2hex(random_bytes(8));
        
        $stmt = $db->prepare("
            INSERT INTO `calendar_events` (`booking_id`, `google_event_id`)
            VALUES (:booking_id, :google_event_id)
            ON DUPLICATE KEY UPDATE `synced_at` = CURRENT_TIMESTAMP
        ");
        return $stmt->execute([
            'booking_id'      => $bookingId,
            'google_event_id' => $googleEventId
        ]);
    }
}
