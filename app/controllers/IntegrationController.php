<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Services\GoogleCalendarService;

class IntegrationController extends Controller {

    public function index(): void {
        $user = $this->requireAuth();
        $dbUser = (new \App\Models\User())->findById($user['id']);

        $googleAccount = GoogleCalendarService::getConnectedAccount($user['id']);
        $isGoogleConnected = GoogleCalendarService::isConnected($user['id']);
        $calendars = $isGoogleConnected ? GoogleCalendarService::listCalendars($user['id']) : [];

        // Fetch Developer API Key
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT token FROM `api_tokens` WHERE `user_id` = :user_id LIMIT 1");
        $stmt->execute(['user_id' => $user['id']]);
        $apiKey = $stmt->fetchColumn() ?: null;

        $this->render('integrations/index', [
            'user'              => $user,
            'dbUser'            => $dbUser,
            'googleAccount'     => $googleAccount,
            'isGoogleConnected' => $isGoogleConnected,
            'calendars'         => $calendars,
            'apiKey'            => $apiKey,
            'activeTab'         => 'integrations',
            'success'           => Session::flash('success'),
            'error'             => Session::flash('error')
        ]);
    }

    public function generateApiKey(): void {
        $user = $this->requireAuth();
        $data = $this->request->getBody();

        if (!Session::verifyCsrfToken($data['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid security token.');
            $this->response->redirect(APP_URL . '/integrations');
        }

        $db = Database::getInstance();
        
        // Delete old keys first
        $stmt = $db->prepare("DELETE FROM `api_tokens` WHERE `user_id` = :user_id");
        $stmt->execute(['user_id' => $user['id']]);

        // Generate a cryptographically secure key
        $newToken = bin2hex(random_bytes(32));

        // Insert new token
        $ins = $db->prepare("
            INSERT INTO `api_tokens` (`user_id`, `name`, `token`, `expires_at`)
            VALUES (:user_id, 'Developer API Key', :token, NULL)
        ");
        $ins->execute([
            'user_id' => $user['id'],
            'token'   => $newToken
        ]);

        Session::flash('success', 'API Key generated successfully! Make sure to copy it.');
        $this->response->redirect(APP_URL . '/integrations');
    }

    public function revokeApiKey(): void {
        $user = $this->requireAuth();
        $data = $this->request->getBody();

        if (!Session::verifyCsrfToken($data['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid security token.');
            $this->response->redirect(APP_URL . '/integrations');
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM `api_tokens` WHERE `user_id` = :user_id");
        $stmt->execute(['user_id' => $user['id']]);

        Session::flash('success', 'API Key revoked successfully.');
        $this->response->redirect(APP_URL . '/integrations');
    }
}
