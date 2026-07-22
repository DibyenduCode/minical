<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Availability;

class AvailabilityController extends Controller {
    private Availability $availabilityModel;

    public function __construct() {
        parent::__construct();
        $this->availabilityModel = new Availability();
    }

    public function index(): void {
        $user = $this->requireAuth();
        $schedule = $this->availabilityModel->getByUserId($user['id']);

        $this->render('availability/index', [
            'user'     => $user,
            'schedule' => $schedule,
            'success'  => Session::flash('success'),
            'error'    => Session::flash('error')
        ]);
    }

    public function update(): void {
        $user = $this->requireAuth();
        $data = $this->request->getBody();

        if (!Session::verifyCsrfToken($data['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid security token.');
            $this->response->redirect(APP_URL . '/availability');
        }

        $days = $data['days'] ?? [];
        for ($day = 0; $day <= 6; $day++) {
            $isEnabled = isset($days[$day]['enabled']) ? 1 : 0;
            $startTime = $days[$day]['start_time'] ?? '09:00';
            $endTime = $days[$day]['end_time'] ?? '17:00';

            $breakEnabled = isset($days[$day]['break_enabled']) ? 1 : 0;
            $breakStartTime = null;
            $breakEndTime = null;
            if ($breakEnabled) {
                $breakStartTime = ($days[$day]['break_start'] ?? '13:00') . ':00';
                $breakEndTime = ($days[$day]['break_end'] ?? '14:00') . ':00';
            }

            $this->availabilityModel->updateDay(
                $user['id'],
                $day,
                $startTime . ':00',
                $endTime . ':00',
                $isEnabled,
                $breakStartTime,
                $breakEndTime
            );
        }

        Session::flash('success', 'Weekly availability schedule updated.');
        $this->response->redirect(APP_URL . '/availability');
    }
}
