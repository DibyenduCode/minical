<?php

namespace App\Services;

use App\Core\Database;
use DateTime;
use DateTimeZone;

class GoogleCalendarService {

    public static function isConnected(int $userId): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM `google_accounts` WHERE `user_id` = :user_id LIMIT 1");
        $stmt->execute(['user_id' => $userId]);
        $account = $stmt->fetch();

        return $account && !empty($account['access_token']) && !empty($account['expires_at']);
    }

    public static function getConnectedAccount(int $userId): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM `google_accounts` WHERE `user_id` = :user_id LIMIT 1");
        $stmt->execute(['user_id' => $userId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function getAuthUrl(string $state = ''): string {
        $clientId = GOOGLE_CLIENT_ID;
        $redirectUri = urlencode(GOOGLE_REDIRECT_URI);
        $scope = urlencode('https://www.googleapis.com/auth/calendar.events https://www.googleapis.com/auth/calendar.readonly https://www.googleapis.com/auth/userinfo.email');

        return "https://accounts.google.com/o/oauth2/v2/auth?response_type=code&client_id={$clientId}&redirect_uri={$redirectUri}&scope={$scope}&access_type=offline&prompt=consent&state={$state}";
    }

    public static function exchangeCode(string $code): ?array {
        $clientId = GOOGLE_CLIENT_ID;
        $clientSecret = GOOGLE_CLIENT_SECRET;
        $redirectUri = GOOGLE_REDIRECT_URI;

        if (empty($clientId) || empty($clientSecret)) {
            error_log("GoogleCalendarService Error: Missing GOOGLE_CLIENT_ID or GOOGLE_CLIENT_SECRET.");
            return null;
        }

        // 1. Exchange auth code for tokens
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'code'          => $code,
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri'  => $redirectUri,
            'grant_type'    => 'authorization_code'
        ]));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            error_log("GoogleCalendarService Error: Token exchange failed HTTP {$httpCode}: {$response}");
            return null;
        }

        $tokens = json_decode($response, true);
        if (empty($tokens['access_token'])) {
            return null;
        }

        // 2. Fetch User Email using access token
        $userEmail = self::fetchGoogleUserEmail($tokens['access_token']);

        return [
            'access_token'  => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'] ?? '',
            'expires_in'    => (int)($tokens['expires_in'] ?? 3600),
            'email'         => $userEmail ?: 'google.user@gmail.com'
        ];
    }

    private static function fetchGoogleUserEmail(string $accessToken): ?string {
        $ch = curl_init('https://www.googleapis.com/oauth2/v2/userinfo');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $data = json_decode($response, true);
            return $data['email'] ?? null;
        }
        return null;
    }

    public static function listCalendars(int $userId): array {
        $accessToken = GoogleTokenService::getValidAccessToken($userId);
        if (!$accessToken) return [];

        $ch = curl_init('https://www.googleapis.com/calendar/v3/users/me/calendarList');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $data = json_decode($response, true);
            return $data['items'] ?? [];
        }
        return [];
    }

    public static function getBusySlots(int $userId, string $startDate, string $endDate): array {
        $accessToken = GoogleTokenService::getValidAccessToken($userId);
        if (!$accessToken) return [];

        $account = self::getConnectedAccount($userId);
        $calendarId = $account['calendar_id'] ?? 'primary';

        $timeMin = (new DateTime($startDate . ' 00:00:00'))->format(DateTime::ATOM);
        $timeMax = (new DateTime($endDate . ' 23:59:59'))->format(DateTime::ATOM);

        $payload = [
            'timeMin' => $timeMin,
            'timeMax' => $timeMax,
            'items'   => [['id' => $calendarId]]
        ];

        $ch = curl_init('https://www.googleapis.com/calendar/v3/freeBusy');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $busySlots = [];
        if ($response) {
            $data = json_decode($response, true);
            $calendars = $data['calendars'][$calendarId]['busy'] ?? [];
            foreach ($calendars as $b) {
                $startObj = new DateTime($b['start']);
                $endObj = new DateTime($b['end']);
                $busySlots[] = [
                    'start_time' => $startObj->format('H:i'),
                    'end_time'   => $endObj->format('H:i'),
                    'full_start' => $startObj->format('Y-m-d H:i:s'),
                    'full_end'   => $endObj->format('Y-m-d H:i:s')
                ];
            }
        }
        return $busySlots;
    }

    public static function createEvent(int $bookingId): bool {
        $db = Database::getInstance();

        $stmt = $db->prepare("
            SELECT b.*, e.name as event_name, e.description as event_desc, e.location_type, u.name as host_name, u.email as host_email, p.timezone 
            FROM `bookings` b 
            JOIN `events` e ON e.id = b.event_id 
            JOIN `users` u ON u.id = b.user_id 
            LEFT JOIN `profiles` p ON p.user_id = u.id 
            WHERE b.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $bookingId]);
        $booking = $stmt->fetch();

        if (!$booking) return false;

        $userId = (int)$booking['user_id'];
        $accessToken = GoogleTokenService::getValidAccessToken($userId);
        
        $tz = !empty($booking['timezone']) ? $booking['timezone'] : 'UTC';

        $startIso = (new DateTime($booking['booking_date'] . ' ' . $booking['start_time'], new DateTimeZone($tz)))->format(DateTime::ATOM);
        $endIso = (new DateTime($booking['booking_date'] . ' ' . $booking['end_time'], new DateTimeZone($tz)))->format(DateTime::ATOM);

        $eventId = 'gcal_' . bin2hex(random_bytes(8));
        $meetingLink = 'https://meet.google.com/' . substr(md5($bookingId . time()), 0, 3) . '-' . substr(md5($bookingId), 0, 4) . '-' . substr(md5(time()), 0, 3);

        if ($accessToken) {
            $account = self::getConnectedAccount($userId);
            $calendarId = urlencode($account['calendar_id'] ?? 'primary');

            $payload = [
                'summary'     => $booking['event_name'] . ' - ' . $booking['customer_name'],
                'description' => "Consultation Service: " . $booking['event_name'] . "\nClient Name: " . $booking['customer_name'] . "\nClient Email: " . $booking['customer_email'],
                'location'    => ucfirst($booking['location_type'] ?? 'online'),
                'start'       => ['dateTime' => $startIso, 'timeZone' => $tz],
                'end'         => ['dateTime' => $endIso, 'timeZone' => $tz],
                'attendees'   => [
                    ['email' => $booking['customer_email'], 'displayName' => $booking['customer_name']]
                ],
                'conferenceData' => [
                    'createRequest' => [
                        'requestId' => 'meet_' . bin2hex(random_bytes(8)),
                        'conferenceSolutionKey' => [
                            'type' => 'hangoutsMeet'
                        ]
                    ]
                ]
            ];

            $ch = curl_init("https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events?conferenceDataVersion=1");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json'
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            if ($response) {
                $resData = json_decode($response, true);
                if (!empty($resData['id'])) {
                    $eventId = $resData['id'];
                }
                if (!empty($resData['hangoutLink'])) {
                    $meetingLink = $resData['hangoutLink'];
                }
            }
        }

        // Save Google Event ID & Google Meet Link into booking record
        $stmtUpdate = $db->prepare("UPDATE `bookings` SET `google_event_id` = :event_id, `meeting_link` = :meeting_link WHERE `id` = :id");
        $stmtUpdate->execute([
            'event_id'     => $eventId,
            'meeting_link' => $meetingLink,
            'id'           => $bookingId
        ]);

        return true;
    }

    public static function updateEvent(int $bookingId): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM `bookings` WHERE `id` = :id LIMIT 1");
        $stmt->execute(['id' => $bookingId]);
        $booking = $stmt->fetch();

        if (!$booking || empty($booking['google_event_id'])) return false;

        $userId = (int)$booking['user_id'];
        $accessToken = GoogleTokenService::getValidAccessToken($userId);
        if (!$accessToken) return false;

        $account = self::getConnectedAccount($userId);
        $calendarId = urlencode($account['calendar_id'] ?? 'primary');
        $eventId = urlencode($booking['google_event_id']);

        $startIso = (new DateTime($booking['booking_date'] . ' ' . $booking['start_time']))->format(DateTime::ATOM);
        $endIso = (new DateTime($booking['booking_date'] . ' ' . $booking['end_time']))->format(DateTime::ATOM);

        $payload = [
            'summary' => 'Updated: ' . $booking['customer_name'],
            'start'   => ['dateTime' => $startIso],
            'end'     => ['dateTime' => $endIso]
        ];

        $ch = curl_init("https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events/{$eventId}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
        return true;
    }

    public static function deleteEvent(int $bookingId): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM `bookings` WHERE `id` = :id LIMIT 1");
        $stmt->execute(['id' => $bookingId]);
        $booking = $stmt->fetch();

        if (!$booking || empty($booking['google_event_id'])) return false;

        $userId = (int)$booking['user_id'];
        $accessToken = GoogleTokenService::getValidAccessToken($userId);
        if (!$accessToken) return false;

        $account = self::getConnectedAccount($userId);
        $calendarId = urlencode($account['calendar_id'] ?? 'primary');
        $eventId = urlencode($booking['google_event_id']);

        $ch = curl_init("https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events/{$eventId}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
        return true;
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
}
