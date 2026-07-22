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

        $this->render('dashboard/index', [
            'user'     => $user,
            'stats'    => $stats,
            'success'  => Session::flash('success'),
            'error'    => Session::flash('error')
        ]);
    }

    public function bookingsList(): void {
        $user = $this->requireAuth();
        $filter = $_GET['filter'] ?? null;
        $search = $_GET['search'] ?? null;

        $bookings = $this->bookingModel->getBookingsForUser($user['id'], $filter, $search);

        $this->render('dashboard/bookings', [
            'user'     => $user,
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
            $this->response->redirect(APP_URL . '/bookings');
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

        $this->response->redirect(APP_URL . '/bookings');
    }

    public function completeBooking(): void {
        $user = $this->requireAuth();
        $data = $this->request->getBody();

        if (!Session::verifyCsrfToken($data['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid security token.');
            $this->response->redirect(APP_URL . '/bookings');
        }

        $bookingId = (int)($data['booking_id'] ?? 0);

        $booking = $this->bookingModel->findById($bookingId);
        if ($booking && (int)$booking['user_id'] === (int)$user['id']) {
            $this->bookingModel->completeBooking($bookingId);
            Session::flash('success', 'Booking has been marked as completed.');
        } else {
            Session::flash('error', 'Booking not found.');
        }

        $this->response->redirect(APP_URL . '/bookings');
    }

    public function promosList(): void {
        $user = $this->requireAuth();
        $db = \App\Core\Database::getInstance();

        $promoCodes = $db->prepare("SELECT * FROM `promo_codes` WHERE `user_id` = :user_id ORDER BY id DESC");
        $promoCodes->execute(['user_id' => $user['id']]);
        $codes = $promoCodes->fetchAll();

        $this->render('dashboard/promos', [
            'user'       => $user,
            'activeTab'  => 'promos',
            'promoCodes' => $codes,
            'success'    => Session::flash('success'),
            'error'      => Session::flash('error')
        ]);
    }

    public function createPromo(): void {
        $user = $this->requireAuth();
        $data = $this->request->getBody();

        if (!Session::verifyCsrfToken($data['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid security token.');
            $this->response->redirect(APP_URL . '/promo-codes');
        }

        $code = strtoupper(trim($data['code'] ?? ''));
        $discountType = $data['discount_type'] ?? 'percentage';
        $discountValue = (float)($data['discount_value'] ?? 0);
        $maxUses = !empty($data['max_uses']) ? (int)$data['max_uses'] : null;
        $expiresAt = !empty($data['expires_at']) ? $data['expires_at'] : null;

        if (empty($code) || $discountValue <= 0) {
            Session::flash('error', 'Promo code and positive discount value are required.');
            $this->response->redirect(APP_URL . '/promo-codes');
        }

        $db = \App\Core\Database::getInstance();
        
        $chk = $db->prepare("SELECT id FROM `promo_codes` WHERE `code` = :code LIMIT 1");
        $chk->execute(['code' => $code]);
        if ($chk->fetch()) {
            Session::flash('error', 'Promo code already exists.');
            $this->response->redirect(APP_URL . '/promo-codes');
        }

        $stmt = $db->prepare("
            INSERT INTO `promo_codes` (`user_id`, `code`, `discount_type`, `discount_value`, `plan_slug`, `max_uses`, `expires_at`, `status`)
            VALUES (:user_id, :code, :discount_type, :discount_value, NULL, :max_uses, :expires_at, 'active')
        ");
        $stmt->execute([
            'user_id'        => $user['id'],
            'code'           => $code,
            'discount_type'  => $discountType,
            'discount_value' => $discountValue,
            'max_uses'       => $maxUses,
            'expires_at'     => $expiresAt
        ]);

        Session::flash('success', 'Promo code created successfully.');
        $this->response->redirect(APP_URL . '/promo-codes');
    }

    public function deletePromo(string $id): void {
        $user = $this->requireAuth();
        $promoId = (int)$id;

        $db = \App\Core\Database::getInstance();
        $stmt = $db->prepare("DELETE FROM `promo_codes` WHERE `id` = :id AND `user_id` = :user_id");
        $stmt->execute(['id' => $promoId, 'user_id' => $user['id']]);

        Session::flash('success', 'Promo code deleted successfully.');
        $this->response->redirect(APP_URL . '/promo-codes');
    }

    public function confirmPayment(): void {
        $user = $this->requireAuth();
        $data = $this->request->getBody();

        if (!Session::verifyCsrfToken($data['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid security token.');
            $this->response->redirect(APP_URL . '/bookings');
        }

        $bookingId = (int)($data['booking_id'] ?? 0);
        $db = \App\Core\Database::getInstance();

        // Verify the booking belongs to this host
        $stmt = $db->prepare("SELECT id FROM `bookings` WHERE `id` = :id AND `user_id` = :user_id LIMIT 1");
        $stmt->execute(['id' => $bookingId, 'user_id' => $user['id']]);
        $booking = $stmt->fetch();

        if (!$booking) {
            Session::flash('error', 'Appointment not found.');
            $this->response->redirect(APP_URL . '/bookings');
        }

        // Update booking status to confirmed (representing manual payment completed)
        $up = $db->prepare("UPDATE `bookings` SET `status` = 'confirmed' WHERE `id` = :id");
        $up->execute(['id' => $bookingId]);

        // Auto-create event on connected Google Calendar
        \App\Services\GoogleCalendarService::createEvent($bookingId);

        // Send booking confirmation emails (with Google Meet join link)
        \App\Services\EmailService::sendBookingConfirmation($bookingId);

        Session::flash('success', 'Payment confirmed manually and appointment successfully approved.');
        $this->response->redirect(APP_URL . '/bookings');
    }
}
