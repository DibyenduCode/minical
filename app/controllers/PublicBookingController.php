<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use App\Models\Availability;
use App\Models\FormField;
use App\Services\GoogleCalendarService;

class PublicBookingController extends Controller {
    private Booking $bookingModel;
    private Event $eventModel;
    private User $userModel;
    private Availability $availabilityModel;
    private FormField $fieldModel;

    public function __construct() {
        parent::__construct();
        $this->bookingModel = new Booking();
        $this->eventModel = new Event();
        $this->userModel = new User();
        $this->availabilityModel = new Availability();
        $this->fieldModel = new FormField();
    }

    public function showPublicBooking(string $username): void {
        $user = $this->userModel->findByUsername($username);
        if (!$user || $user['status'] !== 'active') {
            die("Host booking page not found or has been deactivated.");
        }

        $eventSlug = $_GET['event'] ?? '';
        $event = null;
        if (!empty($eventSlug)) {
            $event = $this->eventModel->findBySlugAndUserId($eventSlug, $user['id']);
        } else {
            $events = $this->eventModel->getByUserId($user['id']);
            if (!empty($events)) {
                $event = $events[0];
            }
        }

        if (!$event || $event['status'] !== 'active') {
            die("No active consultation service available.");
        }

        $profile = $dbUser = $this->userModel->findById($user['id']);
        $customFields = $this->fieldModel->getByUserId($user['id'], $event['id']);
        $allEvents = $this->eventModel->getByUserId($user['id']);
        $allEvents = array_filter($allEvents, function($ev) {
            return $ev['status'] === 'active';
        });

        $this->render('booking/public', [
            'hostUser'     => $user,
            'profile'      => $profile,
            'event'        => $event,
            'customFields' => $customFields,
            'allEvents'    => $allEvents
        ]);
    }

    public function getAvailableSlots(string $username): void {
        $user = $this->userModel->findByUsername($username);
        if (!$user || $user['status'] !== 'active') {
            $this->response->json(['slots' => []]);
            return;
        }

        $dateStr = $_GET['date'] ?? date('Y-m-d');
        $eventSlug = $_GET['event'] ?? '';

        $db = Database::getInstance();
        $stmtProfile = $db->prepare("SELECT timezone FROM `profiles` WHERE `user_id` = :user_id LIMIT 1");
        $stmtProfile->execute(['user_id' => $user['id']]);
        $profileTimezone = $stmtProfile->fetchColumn() ?: 'UTC';

        $hostTz = new \DateTimeZone($profileTimezone);
        $now = new \DateTime('now', $hostTz);

        $event = !empty($eventSlug) ? $this->eventModel->findBySlugAndUserId($eventSlug, $user['id']) : $this->eventModel->findByUserId($user['id']);
        if (!$event) {
            $this->response->json(['slots' => []]);
            return;
        }

        $dayOfWeek = (int)date('w', strtotime($dateStr));
        $allAvail = $this->availabilityModel->getByUserId($user['id']);
        $avail = null;
        foreach ($allAvail as $a) {
            if ((int)$a['day_of_week'] === $dayOfWeek) {
                $avail = $a;
                break;
            }
        }

        if (!$avail || !$avail['is_enabled']) {
            $this->response->json(['slots' => []]);
            return;
        }

        $duration = (int)($event['duration_minutes'] ?? 30);
        $startStr = $avail['start_time'];
        $endStr = $avail['end_time'];

        $startTime = new \DateTime($dateStr . ' ' . $startStr);
        $endTime = new \DateTime($dateStr . ' ' . $endStr);

        $slots = [];
        $curr = clone $startTime;
        $interval = new \DateInterval('PT' . $duration . 'M');

        // Fetch bookings for this host on this day
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT start_time, end_time FROM `bookings` WHERE `user_id` = :user_id AND `booking_date` = :date AND `status` != 'cancelled'");
        $stmt->execute(['user_id' => $user['id'], 'date' => $dateStr]);
        $existingBookings = $stmt->fetchAll();

        // Fetch Google Calendar busy events if integrated
        $googleBusyEvents = GoogleCalendarService::getBusySlots((int)$user['id'], $dateStr, $dateStr);

        while ($curr < $endTime) {
            $slotEndObj = clone $curr;
            $slotEndObj->add($interval);
            if ($slotEndObj > $endTime) {
                break;
            }

            $slotStart = $curr->format('H:i');
            $slotEnd = $slotEndObj->format('H:i');

            if ($dateStr === $now->format('Y-m-d')) {
                $slotStartDateTime = new \DateTime($dateStr . ' ' . $slotStart, $hostTz);
                if ($slotStartDateTime <= $now) {
                    $curr->add($interval);
                    continue;
                }
            }

            // Check if slot overlaps with booked consultations
            $isBookedInDb = false;
            foreach ($existingBookings as $eb) {
                $ebStart = substr($eb['start_time'], 0, 5);
                $ebEnd = substr($eb['end_time'], 0, 5);
                if ($slotStart < $ebEnd && $slotEnd > $ebStart) {
                    $isBookedInDb = true;
                    break;
                }
            }

            // Check if slot overlaps with Google Calendar busy slots
            $isBusyInGoogle = false;
            foreach ($googleBusyEvents as $gSlot) {
                $gStart = substr($gSlot['start_time'], 0, 5);
                $gEnd = substr($gSlot['end_time'], 0, 5);
                if ($slotStart < $gEnd && $slotEnd > $gStart) {
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

    public function applyPromo(string $username): void {
        $data = $this->request->getBody();
        $code = strtoupper(trim($data['promo_code'] ?? ''));
        $price = (float)($data['price'] ?? 0);

        if (empty($code)) {
            $this->response->json(['status' => 'error', 'message' => 'Please enter a promo code.'], 400);
            return;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM `promo_codes` WHERE `code` = :code LIMIT 1");
        $stmt->execute(['code' => $code]);
        $promo = $stmt->fetch();

        if (!$promo) {
            $this->response->json(['status' => 'error', 'message' => 'Invalid promo code.'], 400);
            return;
        }

        $user = $this->userModel->findByUsername($username);
        if (!$user) {
            $this->response->json(['status' => 'error', 'message' => 'Invalid host.'], 400);
            return;
        }

        if (!empty($promo['user_id']) && (int)$promo['user_id'] !== (int)$user['id']) {
            $this->response->json(['status' => 'error', 'message' => 'Invalid promo code for this host\'s appointments.'], 400);
            return;
        }

        if ($promo['status'] !== 'active') {
            $this->response->json(['status' => 'error', 'message' => 'This promo code is inactive.'], 400);
            return;
        }

        if (!empty($promo['expires_at']) && strtotime($promo['expires_at']) < time()) {
            $this->response->json(['status' => 'error', 'message' => 'This promo code has expired.'], 400);
            return;
        }

        if (!empty($promo['max_uses']) && $promo['used_count'] >= $promo['max_uses']) {
            $this->response->json(['status' => 'error', 'message' => 'This promo code limit has been reached.'], 400);
            return;
        }

        // Calculate discount
        $discount = 0.00;
        if ($promo['discount_type'] === 'percentage') {
            $discount = round($price * ($promo['discount_value'] / 100), 2);
        } else {
            $discount = (float)$promo['discount_value'];
        }

        if ($discount > $price) {
            $discount = $price;
        }

        $finalPrice = max(0, $price - $discount);

        $this->response->json([
            'status'        => 'success',
            'message'       => 'Promo code applied successfully!',
            'discount'      => $discount,
            'final_price'   => $finalPrice,
            'discount_text' => $promo['discount_type'] === 'percentage' ? "({$promo['discount_value']}% Off)" : "(\${$promo['discount_value']} Off)"
        ]);
    }

    public function submitBooking(string $username): void {
        $user = $this->userModel->findByUsername($username);
        if (!$user || $user['status'] !== 'active') {
            $this->response->json(['status' => 'error', 'message' => 'Invalid host.'], 400);
            return;
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
            return;
        }

        $event = !empty($eventSlug) ? $this->eventModel->findBySlugAndUserId($eventSlug, $user['id']) : $this->eventModel->findByUserId($user['id']);

        if (!$event) {
            $this->response->json(['status' => 'error', 'message' => 'Selected consultation event not found.'], 400);
            return;
        }

        if ($this->bookingModel->isSlotBooked($user['id'], $bookingDate, $startTime, $endTime)) {
            $this->response->json(['status' => 'error', 'message' => 'Selected slot is no longer available. Please choose another slot.'], 400);
            return;
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
                    return;
                }
            }
        }

        $promoCode = strtoupper(trim($data['promo_code'] ?? ''));
        $discountAmount = 0.00;
        $finalPrice = !empty($event['is_paid']) ? (float)$event['price'] : 0.00;

        if (!empty($promoCode) && !empty($event['is_paid'])) {
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT * FROM `promo_codes` WHERE `code` = :code LIMIT 1");
            $stmt->execute(['code' => $promoCode]);
            $promo = $stmt->fetch();

            if ($promo && $promo['status'] === 'active') {
                $isExpired = !empty($promo['expires_at']) && strtotime($promo['expires_at']) < time();
                $isLimitReached = !empty($promo['max_uses']) && $promo['used_count'] >= $promo['max_uses'];
                $isCorrectHost = empty($promo['user_id']) || (int)$promo['user_id'] === (int)$user['id'];

                if (!$isExpired && !$isLimitReached && $isCorrectHost) {
                    if ($promo['discount_type'] === 'percentage') {
                        $discountAmount = round($finalPrice * ($promo['discount_value'] / 100), 2);
                    } else {
                        $discountAmount = (float)$promo['discount_value'];
                    }

                    if ($discountAmount > $finalPrice) {
                        $discountAmount = $finalPrice;
                    }

                    $finalPrice = max(0, $finalPrice - $discountAmount);

                    // Increment promo code used count
                    $up = $db->prepare("UPDATE `promo_codes` SET `used_count` = `used_count` + 1 WHERE `id` = :id");
                    $up->execute(['id' => $promo['id']]);
                }
            }
        }

        $bookingStatus = $event['is_paid'] && $finalPrice > 0 ? 'awaiting_payment' : 'confirmed';

        $bookingId = $this->bookingModel->createBooking([
            'user_id'         => $user['id'],
            'event_id'        => $event['id'],
            'customer_name'   => $customerName,
            'customer_email'  => $customerEmail,
            'booking_date'    => $bookingDate,
            'start_time'      => $startTime,
            'end_time'        => $endTime,
            'status'          => $bookingStatus,
            'promo_code'      => !empty($promoCode) ? $promoCode : null,
            'discount_amount' => $discountAmount,
            'final_price'     => $finalPrice
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
