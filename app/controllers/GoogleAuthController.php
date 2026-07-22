<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Services\GoogleCalendarService;

class GoogleAuthController extends Controller {

    public function connect(): void {
        $user = $this->requireAuth();
        
        $dbUser = (new \App\Models\User())->findById($user['id']);
        if (($dbUser['plan'] ?? 'free') === 'free') {
            Session::flash('error', 'Google Calendar Sync is not available on the Free plan. Please upgrade.');
            $this->response->redirect(APP_URL . '/integrations');
        }
        
        // Generate secure random CSRF state token
        $state = bin2hex(random_bytes(16));
        Session::set('oauth_google_state', $state);

        $authUrl = GoogleCalendarService::getAuthUrl($state);

        if (empty($authUrl)) {
            Session::flash('error', 'Google OAuth credentials missing. Please configure GOOGLE_CLIENT_ID in config.');
            $this->response->redirect(APP_URL . '/integrations');
        }

        $this->response->redirect($authUrl);
    }

    public function callback(): void {
        $user = $this->requireAuth();
        $code = $_GET['code'] ?? '';
        $state = $_GET['state'] ?? '';

        // Validate state token to prevent CSRF attacks
        $savedState = Session::get('oauth_google_state');
        Session::remove('oauth_google_state');

        if (empty($state) || $state !== $savedState) {
            Session::flash('error', 'Security check failed. Invalid state token.');
            $this->response->redirect(APP_URL . '/integrations');
        }

        if (empty($code)) {
            Session::flash('error', 'Google Calendar authorization failed or was denied.');
            $this->response->redirect(APP_URL . '/integrations');
        }

        $tokenData = GoogleCalendarService::exchangeCode($code);

        if (!$tokenData || empty($tokenData['access_token'])) {
            Session::flash('error', 'Failed to exchange authorization code for access tokens from Google.');
            $this->response->redirect(APP_URL . '/integrations');
        }

        $expiresAt = date('Y-m-d H:i:s', time() + $tokenData['expires_in']);

        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO `google_accounts` (`user_id`, `google_email`, `access_token`, `refresh_token`, `expires_at`, `calendar_id`)
            VALUES (:user_id, :google_email, :access_token, :refresh_token, :expires_at, 'primary')
            ON DUPLICATE KEY UPDATE 
                `google_email` = VALUES(`google_email`),
                `access_token` = VALUES(`access_token`),
                `refresh_token` = VALUES(`refresh_token`),
                `expires_at` = VALUES(`expires_at`)
        ");
        $stmt->execute([
            'user_id'       => $user['id'],
            'google_email'  => $tokenData['email'],
            'access_token'  => $tokenData['access_token'],
            'refresh_token' => $tokenData['refresh_token'],
            'expires_at'    => $expiresAt
        ]);

        Session::flash('success', "Google Calendar account ({$tokenData['email']}) connected successfully! Future bookings will auto-sync.");
        $this->response->redirect(APP_URL . '/integrations');
    }

    public function selectCalendar(): void {
        $user = $this->requireAuth();
        $data = $this->request->getBody();

        if (!Session::verifyCsrfToken($data['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid security token.');
            $this->response->redirect(APP_URL . '/integrations');
        }

        $calendarId = trim($data['calendar_id'] ?? 'primary');

        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE `google_accounts` SET `calendar_id` = :calendar_id WHERE `user_id` = :user_id");
        $stmt->execute(['calendar_id' => $calendarId, 'user_id' => $user['id']]);

        Session::flash('success', "Target Google Calendar updated to `{$calendarId}`.");
        $this->response->redirect(APP_URL . '/integrations');
    }

    public function disconnect(): void {
        $user = $this->requireAuth();
        
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM `google_accounts` WHERE `user_id` = :user_id");
        $stmt->execute(['user_id' => $user['id']]);

        Session::flash('success', 'Google Calendar account disconnected successfully.');
        $this->response->redirect(APP_URL . '/integrations');
    }
}
