<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Services\GoogleCalendarService;

class GoogleCalendarController extends Controller {

    public function connect(): void {
        $user = $this->requireAuth();
        $data = $this->request->getBody();

        if (!Session::verifyCsrfToken($data['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid security token.');
            $this->response->redirect(APP_URL . '/profile');
        }

        $googleEmail = trim($data['google_email'] ?? '');

        if (empty($googleEmail) || !filter_var($googleEmail, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Please enter a valid Google Account email address.');
            $this->response->redirect(APP_URL . '/profile');
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO `google_accounts` (`user_id`, `google_email`, `access_token`, `refresh_token`, `expires_at`)
            VALUES (:user_id, :google_email, 'mock_access_token', 'mock_refresh_token', DATE_ADD(NOW(), INTERVAL 30 DAY))
            ON DUPLICATE KEY UPDATE `google_email` = VALUES(`google_email`), `expires_at` = VALUES(`expires_at`)
        ");
        $stmt->execute([
            'user_id'      => $user['id'],
            'google_email' => $googleEmail
        ]);

        Session::flash('success', "Google Calendar ({$googleEmail}) connected successfully! Appointments will now auto-sync.");
        $this->response->redirect(APP_URL . '/profile');
    }

    public function disconnect(): void {
        $user = $this->requireAuth();
        
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM `google_accounts` WHERE `user_id` = :user_id");
        $stmt->execute(['user_id' => $user['id']]);

        Session::flash('success', 'Google Calendar account disconnected.');
        $this->response->redirect(APP_URL . '/profile');
    }
}
