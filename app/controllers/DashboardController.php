<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Booking;
use App\Services\GoogleCalendarService;

class DashboardController extends Controller {
    private Booking $bookingModel;

    public function __construct() {
        parent::__construct();
        $this->bookingModel = new Booking();
    }

    public function index(): void {
        $user = $this->requireAuth();

        $stats = $this->bookingModel->getDashboardStats($user['id']);

        $filter = $_GET['filter'] ?? null;
        $search = $_GET['search'] ?? null;

        $bookings = $this->bookingModel->getBookingsForUser($user['id'], $filter, $search);

        $this->render('dashboard/index', [
            'user'     => $user,
            'stats'    => $stats,
            'bookings' => $bookings,
            'filter'   => $filter,
            'search'   => $search,
            'success'  => Session::flash('success'),
            'error'    => Session::flash('error')
        ]);
    }

    public function cancelBooking(): void {
        $user = $this->requireAuth();
        $data = $this->request->getBody();

        if (!Session::verifyCsrfToken($data['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid security token.');
            $this->response->redirect(APP_URL . '/dashboard');
        }

        $bookingId = (int)($data['booking_id'] ?? 0);
        $reason = trim($data['reason'] ?? 'Cancelled by host');

        $booking = $this->bookingModel->findById($bookingId);
        if ($booking && (int)$booking['user_id'] === (int)$user['id']) {
            // Cancel Google Calendar event
            GoogleCalendarService::deleteEvent($bookingId);

            // Update database booking status
            $this->bookingModel->cancelBooking($bookingId, $reason);

            Session::flash('success', 'Booking has been cancelled and removed from Google Calendar.');
        } else {
            Session::flash('error', 'Booking not found.');
        }

        $this->response->redirect(APP_URL . '/dashboard');
    }
}
