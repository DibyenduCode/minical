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
        $event = $this->eventModel->findByUserId($user['id']);
        $customFields = $this->fieldModel->getByUserId($user['id']);

        $this->render('booking/public', [
            'hostUser'     => $user,
            'profile'      => $profile,
            'event'        => $event,
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

        $dayOfWeek = (int)$dateObj->format('w'); // 0=Sun, 1=Mon, ..., 6=Sat

        // Get Host Availability for this day
        $schedule = $this->availabilityModel->getByUserId($user['id']);
        $dayAvail = null;
        foreach ($schedule as $row) {
            if ((int)$row['day_of_week'] === $dayOfWeek) {
                $dayAvail = $row;
                break;
            }
        }

        if (!$dayAvail || !$dayAvail['is_enabled']) {
            $this->response->json(['slots' => []]);
            return;
        }

        $event = $this->eventModel->findByUserId($user['id']);
        $durationMinutes = (int)($event['duration_minutes'] ?? 30);

        // Calculate time slots between start_time and end_time
        $startTimeStr = $dateStr . ' ' . $dayAvail['start_time'];
        $endTimeStr = $dateStr . ' ' . $dayAvail['end_time'];

        $current = new DateTime($startTimeStr);
        $end = new DateTime($endTimeStr);

        // Fetch existing bookings for this date
        $existingBookings = $this->bookingModel->getExistingBookingsForDate($user['id'], $dateStr);

        $slots = [];
        $interval = new DateInterval("PT{$durationMinutes}M");

        while ($current < $end) {
            $slotStart = clone $current;
            $slotEnd = clone $current;
            $slotEnd->add($interval);

            if ($slotEnd > $end) {
                break;
            }

            $startFormatted = $slotStart->format('H:i:s');
            $endFormatted = $slotEnd->format('H:i:s');

            // Check if slot overlaps with any existing booking
            $isOccupied = false;
            foreach ($existingBookings as $b) {
                if ($startFormatted < $b['end_time'] && $endFormatted > $b['start_time']) {
                    $isOccupied = true;
                    break;
                }
            }

            if (!$isOccupied) {
                $slots[] = [
                    'start_time' => $startFormatted,
                    'end_time'   => $endFormatted,
                    'display'    => $slotStart->format('h:i A') . ' - ' . $slotEnd->format('h:i A')
                ];
            }

            $current->add($interval);
        }

        $this->response->json(['slots' => $slots]);
    }

    public function submitBooking(string $username): void {
        $user = $this->userModel->findByUsername($username);
        if (!$user) {
            $this->response->json(['status' => 'error', 'message' => 'Host not found'], 404);
        }

        $data = $this->request->getBody();
        $customerName = trim($data['customer_name'] ?? '');
        $customerEmail = trim($data['customer_email'] ?? '');
        $bookingDate = $data['booking_date'] ?? '';
        $startTime = $data['start_time'] ?? '';
        $endTime = $data['end_time'] ?? '';

        if (empty($customerName) || empty($customerEmail) || empty($bookingDate) || empty($startTime) || empty($endTime)) {
            $this->response->json(['status' => 'error', 'message' => 'Please fill in all required fields.'], 400);
        }

        $event = $this->eventModel->findByUserId($user['id']);

        // Determine status (Paid vs Free)
        $status = (!empty($event['is_paid']) && (float)$event['price'] > 0) ? 'awaiting_payment' : 'confirmed';

        $bookingId = $this->bookingModel->createBooking([
            'user_id'        => $user['id'],
            'event_id'       => $event['id'],
            'customer_name'  => $customerName,
            'customer_email' => $customerEmail,
            'booking_date'   => $bookingDate,
            'start_time'     => $startTime,
            'end_time'       => $endTime,
            'status'         => $status
        ]);

        // Save Custom Form Field Responses
        $customFields = $this->fieldModel->getByUserId($user['id']);
        foreach ($customFields as $field) {
            $fieldKey = 'field_' . $field['id'];
            if (isset($data[$fieldKey])) {
                $val = is_array($data[$fieldKey]) ? implode(', ', $data[$fieldKey]) : (string)$data[$fieldKey];
                $this->fieldModel->saveResponse($bookingId, $field['id'], $field['label'], $val);
            }
        }

        $redirectUrl = APP_URL . '/booking/confirmation/' . $bookingId;
        $this->response->json(['status' => 'success', 'booking_id' => $bookingId, 'redirect' => $redirectUrl]);
    }

    public function showConfirmation(string $id): void {
        $bookingId = (int)$id;
        $booking = $this->bookingModel->findById($bookingId);

        if (!$booking) {
            die("Booking not found.");
        }

        $event = $this->eventModel->findById($booking['event_id']);
        $hostUser = $this->userModel->findById($booking['user_id']);
        $profile = $this->profileModel->findByUserId($booking['user_id']);

        $this->render('booking/confirmation', [
            'booking'  => $booking,
            'event'    => $event,
            'hostUser' => $hostUser,
            'profile'  => $profile
        ]);
    }
}
