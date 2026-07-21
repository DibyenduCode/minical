<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Availability;
use App\Models\Booking;
use App\Models\Event;
use App\Models\FormField;
use App\Models\Profile;
use App\Models\User;
use DateTime;
use DateInterval;

class PublicBookingController extends Controller {
    private User $userModel;
    private Profile $profileModel;
    private Event $eventModel;
    private Availability $availabilityModel;
    private Booking $bookingModel;
    private FormField $fieldModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->profileModel = new Profile();
        $this->eventModel = new Event();
        $this->availabilityModel = new Availability();
        $this->bookingModel = new Booking();
        $this->fieldModel = new FormField();
    }

    public function showPublicBooking(string $username): void {
        $user = $this->userModel->findByUsername($username);
        if (!$user || $user['status'] !== 'active') {
            $this->response->setStatusCode(404);
            die("Host user not found.");
        }

        $profile = $this->profileModel->findByUserId($user['id']);
        $allEvents = $this->eventModel->getByUserId($user['id']);

        $eventSlug = $_GET['event'] ?? '';
        $selectedEvent = null;

        if (!empty($eventSlug)) {
            $selectedEvent = $this->eventModel->findBySlugAndUserId($eventSlug, $user['id']);
        }

        if (!$selectedEvent && !empty($allEvents)) {
            $selectedEvent = $allEvents[0];
        }

        $customFields = $selectedEvent ? $this->fieldModel->getByUserId($user['id'], $selectedEvent['id']) : [];

        $this->render('booking/public', [
            'hostUser'     => $user,
            'profile'      => $profile,
            'allEvents'    => $allEvents,
            'event'        => $selectedEvent,
            'customFields' => $customFields
        ]);
    }

    public function getAvailableSlots(string $username): void {
        $user = $this->userModel->findByUsername($username);
        if (!$user) {
            $this->response->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        $dateStr = $_GET['date'] ?? date('Y-m-d');
        $dateObj = DateTime::createFromFormat('Y-m-d', $dateStr);

        if (!$dateObj) {
            $this->response->json(['status' => 'error', 'message' => 'Invalid date format'], 400);
        }

        $dayOfWeek = (int)$dateObj->format('w');
        
        // Fail-safe fetch availability using getByUserId
        $allAvail = $this->availabilityModel->getByUserId($user['id']);
        $avail = null;
        foreach ($allAvail as $a) {
            if ((int)$a['day_of_week'] === $dayOfWeek) {
                $avail = $a;
                break;
            }
        }

        if (!$avail || empty($avail['is_enabled'])) {
            $this->response->json(['date' => $dateStr, 'slots' => []]);
            return;
        }

        $existingBookings = $this->bookingModel->getBookedSlots($user['id'], $dateStr);
        $bookedMap = [];
        foreach ($existingBookings as $b) {
            $key = substr($b['start_time'], 0, 5) . '-' . substr($b['end_time'], 0, 5);
            $bookedMap[$key] = true;
        }

        $eventSlug = $_GET['event'] ?? '';
        $event = !empty($eventSlug) ? $this->eventModel->findBySlugAndUserId($eventSlug, $user['id']) : $this->eventModel->findByUserId($user['id']);

        $duration = (int)($event['duration_minutes'] ?? 30);
        if ($duration <= 0) {
            $duration = 30;
        }

        $startTime = new DateTime($dateStr . ' ' . $avail['start_time']);
        $endTime = new DateTime($dateStr . ' ' . $avail['end_time']);
        $interval = new DateInterval('PT' . $duration . 'M');

        $slots = [];
        $curr = clone $startTime;

        while ($curr < $endTime) {
            $slotStart = $curr->format('H:i');
            $slotEndObj = (clone $curr)->add($interval);

            if ($slotEndObj > $endTime) break;

            $slotEnd = $slotEndObj->format('H:i');
            $slotKey = $slotStart . '-' . $slotEnd;

            if (!isset($bookedMap[$slotKey])) {
                $slots[] = [
                    'start_time' => $slotStart . ':00',
                    'end_time'   => $slotEnd . ':00',
                    'display'    => $curr->format('h:i A') . ' - ' . $slotEndObj->format('h:i A')
                ];
            }
            $curr->add($interval);
        }

        $this->response->json(['date' => $dateStr, 'slots' => $slots]);
    }

    public function submitBooking(string $username): void {
        $user = $this->userModel->findByUsername($username);
        if (!$user || $user['status'] !== 'active') {
            $this->response->json(['status' => 'error', 'message' => 'Invalid host.'], 400);
        }

        $data = $this->request->getBody();

        $customerName = trim($data['customer_name'] ?? '');
        $customerEmail = trim($data['customer_email'] ?? '');
        $bookingDate = trim($data['booking_date'] ?? '');
        $startTime = trim($data['start_time'] ?? '');
        $endTime = trim($data['end_time'] ?? '');
        $eventSlug = trim($data['event_slug'] ?? '');

        if (empty($customerName) || empty($customerEmail) || empty($bookingDate) || empty($startTime) || empty($endTime)) {
            $this->response->json(['status' => 'error', 'message' => 'Please fill in all required booking fields.'], 400);
        }

        $event = !empty($eventSlug) ? $this->eventModel->findBySlugAndUserId($eventSlug, $user['id']) : $this->eventModel->findByUserId($user['id']);

        if (!$event) {
            $this->response->json(['status' => 'error', 'message' => 'Selected consultation event not found.'], 400);
        }

        if ($this->bookingModel->isSlotBooked($user['id'], $bookingDate, $startTime, $endTime)) {
            $this->response->json(['status' => 'error', 'message' => 'Selected slot is no longer available. Please choose another slot.'], 400);
        }

        $bookingId = $this->bookingModel->createBooking([
            'user_id'       => $user['id'],
            'event_id'      => $event['id'],
            'customer_name'  => $customerName,
            'customer_email' => $customerEmail,
            'booking_date'  => $bookingDate,
            'start_time'    => $startTime,
            'end_time'      => $endTime,
            'status'        => $event['is_paid'] ? 'awaiting_payment' : 'confirmed'
        ]);

        // Save Custom Form Field Responses
        $customFields = $this->fieldModel->getByUserId($user['id'], $event['id']);
        foreach ($customFields as $field) {
            $fieldKey = 'field_' . $field['id'];
            $val = $data[$fieldKey] ?? null;
            if (is_array($val)) {
                $val = implode(', ', $val);
            }
            if ($val !== null) {
                $this->fieldModel->saveResponse($bookingId, $field['id'], $field['label'], trim($val));
            }
        }

        $this->response->json([
            'status'   => 'success',
            'message'  => 'Booking confirmed successfully!',
            'redirect' => APP_URL . '/booking/confirmation/' . $bookingId
        ]);
    }

    public function showConfirmation(string $id): void {
        $bookingId = (int)$id;
        $booking = $this->bookingModel->findById($bookingId);

        if (!$booking) {
            die("Booking record not found.");
        }

        $user = $this->userModel->findById($booking['user_id']);
        $event = $this->eventModel->findByIdAndUserId($booking['event_id'], $booking['user_id']);

        $this->render('booking/confirmation', [
            'booking'  => $booking,
            'hostUser' => $user,
            'event'    => $event
        ]);
    }
}
