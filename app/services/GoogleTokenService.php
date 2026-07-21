<?php

namespace App\Services;

use App\Core\Database;

class GoogleTokenService {

    public static function getValidAccessToken(int $userId): ?string {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM `google_accounts` WHERE `user_id` = :user_id LIMIT 1");
        $stmt->execute(['user_id' => $userId]);
        $account = $stmt->fetch();

        if (!$account || empty($account['access_token'])) {
            return null;
        }

        $now = time();
        $expiresAt = strtotime($account['expires_at'] ?? '1970-01-01');

        // If access token is still valid (with 5 min buffer), return it
        if ($expiresAt > ($now + 300)) {
            return $account['access_token'];
        }

        // Token is expired, attempt to refresh using refresh_token
        if (!empty($account['refresh_token'])) {
            $newToken = self::refreshToken($userId, $account['refresh_token']);
            if ($newToken) {
                return $newToken;
            }
        }

        // Fallback: If refresh failed or missing, mark as disconnected gracefully
        self::markDisconnected($userId);
        return null;
    }

    public static function refreshToken(int $userId, string $refreshToken): ?string {
        $clientId = GOOGLE_CLIENT_ID;
        $clientSecret = GOOGLE_CLIENT_SECRET;

        if (empty($clientId) || empty($clientSecret)) {
            return null;
        }

        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $refreshToken,
            'grant_type'    => 'refresh_token'
        ]));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            error_log("GoogleTokenService Error: Failed to refresh token for user {$userId}. HTTP {$httpCode}: {$response}");
            return null;
        }

        $data = json_decode($response, true);
        if (empty($data['access_token'])) {
            return null;
        }

        $accessToken = $data['access_token'];
        $expiresIn = (int)($data['expires_in'] ?? 3600);
        $expiresAt = date('Y-m-d H:i:s', time() + $expiresIn);

        // Update database
        $db = Database::getInstance();
        $stmt = $db->prepare("
            UPDATE `google_accounts` 
            SET `access_token` = :access_token, `expires_at` = :expires_at 
            WHERE `user_id` = :user_id
        ");
        $stmt->execute([
            'access_token' => $accessToken,
            'expires_at'   => $expiresAt,
            'user_id'      => $userId
        ]);

        return $accessToken;
    }

    public static function markDisconnected(int $userId): void {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE `google_accounts` SET `expires_at` = NULL WHERE `user_id` = :user_id");
        $stmt->execute(['user_id' => $userId]);
    }
}
