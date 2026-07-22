<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Availability;
use App\Models\Booking;
use App\Models\Event;
use App\Models\FormField;
use App\Models\Profile;
use App\Models\User;
use App\Services\GoogleCalendarService;
use DateTime;
use DateTimeZone;
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
        
        // Fetch host working hours availability
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

        // 1. Fetch DB Existing Bookings
        $existingBookings = $this->bookingModel->getBookedSlots($user['id'], $dateStr);
        $bookedMap = [];
        foreach ($existingBookings as $b) {
            $key = substr($b['start_time'], 0, 5) . '-' . substr($b['end_time'], 0, 5);
            $bookedMap[$key] = true;
        }

        // 2. Read Busy Slots from connected Google Calendar to prevent double bookings
        $googleBusy = GoogleCalendarService::getBusySlots($user['id'], $dateStr, $dateStr);

        $eventSlug = $_GET['event'] ?? '';
        $event = !empty($eventSlug) ? $this->eventModel->findBySlugAndUserId($eventSlug, $user['id']) : $this->eventModel->findByUserId($user['id']);

        $duration = (int)($event['duration_minutes'] ?? 30);
        if ($duration <= 0) {
            $duration = 30;
        }

        $profile = $this->profileModel->findByUserId($user['id']);
        $tz = !empty($profile['timezone']) ? $profile['timezone'] : 'UTC';
        $now = new DateTime('now', new DateTimeZone($tz));

        $startTime = new DateTime($dateStr . ' ' . $avail['start_time'], new DateTimeZone($tz));
        $endTime = new DateTime($dateStr . ' ' . $avail['end_time'], new DateTimeZone($tz));
        $interval = new DateInterval('PT' . $duration . 'M');

        $slots = [];
        $curr = clone $startTime;

        while ($curr < $endTime) {
            $slotStart = $curr->format('H:i');
            $slotEndObj = (clone $curr)->add($interval);

            if ($slotEndObj > $endTime) break;

            $slotEnd = $slotEndObj->format('H:i');
            $slotKey = $slotStart . '-' . $slotEnd;

            // Prevent booking slots that are in the past
            if ($curr <= $now) {
                $curr->add($interval);
                continue;
            }

            // Check if slot overlaps with DB booking
            $isBookedInDb = isset($bookedMap[$slotKey]);

            // Check if slot overlaps with Google Calendar Busy event
            $isBusyInGoogle = false;
            foreach ($googleBusy as $gb) {
                if ($slotStart < $gb['end_time'] && $slotEnd > $gb['start_time']) {
                    $isBusyInGoogle = true;
                    break;
                }
            }

            // Check if slot overlaps with daily break time
            $isOverlapBreak = false;
            if (!empty($avail['break_start_time']) && !empty($avail['break_end_time'])) {
                $breakStart = substr($avail['break_start_time'], 0, 5);
                $breakEnd = substr($avail['break_end_time'], 0, 5);
                if ($slotStart < $breakEnd && $slotEnd > $breakStart) {
                    $isOverlapBreak = true;
                }
            }

            if (!$isBookedInDb && !$isBusyInGoogle && !$isOverlapBreak) {
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

        // Validate daily break time overlap
        $dayOfWeek = (int)date('w', strtotime($bookingDate));
        $allAvail = $this->availabilityModel->getByUserId($user['id']);
        $avail = null;
        foreach ($allAvail as $a) {
            if ((int)$a['day_of_week'] === $dayOfWeek) {
                $avail = $a;
                break;
            }
        }

        if ($avail && !empty($avail['is_enabled'])) {
            if (!empty($avail['break_start_time']) && !empty($avail['break_end_time'])) {
                $bStart = substr($avail['break_start_time'], 0, 5);
                $bEnd = substr($avail['break_end_time'], 0, 5);
                $sStart = substr($startTime, 0, 5);
                $sEnd = substr($endTime, 0, 5);

                if ($sStart < $bEnd && $sEnd > $bStart) {
                    $this->response->json(['status' => 'error', 'message' => 'Selected slot falls within host\'s break time.'], 400);
                }
            }
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

        // Auto-create event on connected Google Calendar
        GoogleCalendarService::createEvent($bookingId);

        // Send booking confirmation emails (with Google Meet join link)
        \App\Services\EmailService::sendBookingConfirmation($bookingId);

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
        $googleConnected = GoogleCalendarService::isConnected((int)$booking['user_id']);
        $googleCalendarUrl = GoogleCalendarService::generateGoogleCalendarUrl($booking, $event, $user);

        $this->render('booking/confirmation', [
            'booking'           => $booking,
            'hostUser'          => $user,
            'event'             => $event,
            'googleConnected'   => $googleConnected,
            'googleCalendarUrl' => $googleCalendarUrl
        ]);
    }
}
