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

        $this->render('integrations/index', [
            'user'              => $user,
            'dbUser'            => $dbUser,
            'googleAccount'     => $googleAccount,
            'isGoogleConnected' => $isGoogleConnected,
            'calendars'         => $calendars,
            'activeTab'         => 'integrations',
            'success'           => Session::flash('success'),
            'error'             => Session::flash('error')
        ]);
    }
}
