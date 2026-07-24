<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Availability;
use App\Models\Booking;
use App\Models\FormField;
use App\Models\Profile;
use App\Models\User;
use App\Models\Event;

class ApiController extends Controller {
    private User $userModel;
    private Profile $profileModel;
    private Availability $availabilityModel;
    private Booking $bookingModel;
    private FormField $fieldModel;
    private Event $eventModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->profileModel = new Profile();
        $this->availabilityModel = new Availability();
        $this->bookingModel = new Booking();
        $this->fieldModel = new FormField();
        $this->eventModel = new Event();
    }

    private function authenticateToken(): array {
        $token = $this->request->getBearerToken();
        if (!$token) {
            $this->response->json(['status' => 'error', 'message' => 'Unauthorized. Bearer Token missing.'], 401);
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT user_id FROM `api_tokens` WHERE `token` = :token LIMIT 1");
        $stmt->execute(['token' => $token]);
        $row = $stmt->fetch();

        if (!$row) {
            $this->response->json(['status' => 'error', 'message' => 'Invalid API token.'], 401);
        }

        $user = $this->userModel->findById((int)$row['user_id']);
        if (!$user || $user['status'] !== 'active') {
            $this->response->json(['status' => 'error', 'message' => 'User account disabled or not found.'], 401);
        }

        unset($user['password_hash']);
        return $user;
    }

    public function login(): void {
        $data = $this->request->getBody();
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $user = $this->userModel->findByEmail($email);
        if (!$user || !$this->userModel->verifyPassword($password, $user['password_hash'])) {
            $this->response->json(['status' => 'error', 'message' => 'Please enter correct email and password.'], 200);
        }

        // Generate API Bearer Token
        $tokenStr = bin2hex(random_bytes(32));
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO `api_tokens` (`user_id`, `name`, `token`) VALUES (:user_id, 'Mobile Auth Token', :token)");
        $stmt->execute(['user_id' => $user['id'], 'token' => $tokenStr]);

        unset($user['password_hash']);
        $this->response->json([
            'status' => 'success',
            'token'  => $tokenStr,
            'user'   => $user
        ]);
    }

    public function getDashboard(): void {
        $user = $this->authenticateToken();
        $stats = $this->bookingModel->getDashboardStats($user['id']);
        $this->response->json(['status' => 'success', 'data' => $stats]);
    }

    public function getProfile(): void {
        $user = $this->authenticateToken();
        $profile = $this->profileModel->findByUserId($user['id']);
        $this->response->json(['status' => 'success', 'user' => $user, 'profile' => $profile]);
    }

    public function getAvailability(): void {
        $user = $this->authenticateToken();
        $schedule = $this->availabilityModel->getByUserId($user['id']);
        $this->response->json(['status' => 'success', 'schedule' => $schedule]);
    }

    public function getBookings(): void {
        $user = $this->authenticateToken();
        $bookings = $this->bookingModel->getBookingsForUser($user['id']);
        $this->response->json(['status' => 'success', 'bookings' => $bookings]);
    }

    public function getFormFields(): void {
        $user = $this->authenticateToken();
        $fields = $this->fieldModel->getByUserId($user['id']);
        $this->response->json(['status' => 'success', 'fields' => $fields]);
    }

    public function checkUsername(): void {
        $username = strtolower(trim($_GET['username'] ?? ''));
        if (empty($username)) {
            $this->response->json(['available' => false, 'message' => 'Username cannot be empty.']);
            return;
        }

        if (!preg_match('/^[a-z0-9_-]+$/i', $username)) {
            $this->response->json(['available' => false, 'message' => 'Letters, numbers, underscores & hyphens only.']);
            return;
        }

        $excludeUserId = isset($_GET['exclude_user_id']) ? (int)$_GET['exclude_user_id'] : 0;

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM `users` WHERE `username` = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user) {
            if ($excludeUserId > 0 && (int)$user['id'] === $excludeUserId) {
                $this->response->json(['available' => true, 'message' => 'Username is available.']);
                return;
            }
            $this->response->json(['available' => false, 'message' => 'Username is already taken.']);
        } else {
            $this->response->json(['available' => true, 'message' => 'Username is available.']);
        }
    }

    public function getEvents(): void {
        $user = $this->authenticateToken();
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM `events` WHERE `user_id` = :user_id ORDER BY `id` DESC");
        $stmt->execute(['user_id' => $user['id']]);
        $events = $stmt->fetchAll();
        $this->response->json(['status' => 'success', 'events' => $events]);
    }

    public function updateAvailability(): void {
        $user = $this->authenticateToken();
        $data = $this->request->getBody();

        $schedule = $data['schedule'] ?? [];
        if (!is_array($schedule)) {
            $this->response->json(['status' => 'error', 'message' => 'Invalid schedule format.'], 400);
            return;
        }

        try {
            foreach ($schedule as $day) {
                $dayOfWeek = (int)($day['day_of_week'] ?? 0);
                $isEnabled = (int)($day['is_enabled'] ?? 0);
                
                $startTime = trim($day['start_time'] ?? '09:00');
                if (strlen($startTime) === 5) $startTime .= ':00';
                
                $endTime = trim($day['end_time'] ?? '17:00');
                if (strlen($endTime) === 5) $endTime .= ':00';

                $breakEnabled = (int)($day['break_enabled'] ?? 0);
                $breakStartTime = null;
                $breakEndTime = null;
                
                if ($breakEnabled) {
                    $breakStartTime = trim($day['break_start_time'] ?? '13:00');
                    if (strlen($breakStartTime) === 5) $breakStartTime .= ':00';
                    
                    $breakEndTime = trim($day['break_end_time'] ?? '14:00');
                    if (strlen($breakEndTime) === 5) $breakEndTime .= ':00';
                }

                $this->availabilityModel->updateDay(
                    $user['id'],
                    $dayOfWeek,
                    $startTime,
                    $endTime,
                    $isEnabled,
                    $breakStartTime,
                    $breakEndTime
                );
            }

            $updated = $this->availabilityModel->getByUserId($user['id']);
            $this->response->json(['status' => 'success', 'message' => 'Weekly availability schedule updated.', 'schedule' => $updated]);
        } catch (\Exception $e) {
            $this->response->json(['status' => 'error', 'message' => 'Failed to save availability: ' . $e->getMessage()], 500);
        }
    }

    public function confirmBookingPayment(): void {
        $user = $this->authenticateToken();
        $data = $this->request->getBody();

        $bookingId = (int)($data['booking_id'] ?? 0);
        $db = Database::getInstance();

        // Check ownership
        $stmt = $db->prepare("SELECT id FROM `bookings` WHERE `id` = :id AND `user_id` = :user_id LIMIT 1");
        $stmt->execute(['id' => $bookingId, 'user_id' => $user['id']]);
        $booking = $stmt->fetch();

        if (!$booking) {
            $this->response->json(['status' => 'error', 'message' => 'Booking not found.'], 404);
            return;
        }

        $up = $db->prepare("UPDATE `bookings` SET `status` = 'confirmed' WHERE `id` = :id");
        $up->execute(['id' => $bookingId]);

        // Auto-create calendar event & send email confirmations
        \App\Services\GoogleCalendarService::createEvent($bookingId);
        \App\Services\EmailService::sendBookingConfirmation($bookingId);

        $this->response->json(['status' => 'success', 'message' => 'Booking payment confirmed and appointment approved.']);
    }

    public function cancelBooking(): void {
        $user = $this->authenticateToken();
        $data = $this->request->getBody();

        $bookingId = (int)($data['booking_id'] ?? 0);
        $reason = trim($data['reason'] ?? 'Cancelled via Mobile App');

        $booking = $this->bookingModel->findById($bookingId);
        if (!$booking || (int)$booking['user_id'] !== (int)$user['id']) {
            $this->response->json(['status' => 'error', 'message' => 'Booking not found.'], 404);
            return;
        }

        // Cancel Google Calendar event
        \App\Services\GoogleCalendarService::deleteEvent($bookingId);

        // Update database booking status
        $this->bookingModel->cancelBooking($bookingId, $reason);

        $this->response->json(['status' => 'success', 'message' => 'Booking cancelled successfully.']);
    }

    public function completeBooking(): void {
        $user = $this->authenticateToken();
        $data = $this->request->getBody();

        $bookingId = (int)($data['booking_id'] ?? 0);

        $booking = $this->bookingModel->findById($bookingId);
        if (!$booking || (int)$booking['user_id'] !== (int)$user['id']) {
            $this->response->json(['status' => 'error', 'message' => 'Booking not found.'], 404);
            return;
        }

        $this->bookingModel->completeBooking($bookingId);
        $this->response->json(['status' => 'success', 'message' => 'Booking marked as completed.']);
    }

    public function createEvent(): void {
        $user = $this->authenticateToken();
        $data = $this->request->getBody();

        $name = trim($data['name'] ?? '');
        $description = trim($data['description'] ?? '');
        $durationMinutes = (int)($data['duration_minutes'] ?? 30);
        $bufferMinutes = (int)($data['buffer_minutes'] ?? 0);
        $bookingWindowDays = (int)($data['booking_window_days'] ?? 30);
        $locationType = trim($data['location_type'] ?? 'online');
        $price = (float)($data['price'] ?? 0.00);
        $currency = strtoupper(trim($data['currency'] ?? 'USD'));
        $isPaid = isset($data['is_paid']) ? (int)$data['is_paid'] : ($price > 0 ? 1 : 0);
        $status = trim($data['status'] ?? 'active');

        if (empty($name)) {
            $this->response->json(['status' => 'error', 'message' => 'Event name is required.'], 400);
            return;
        }

        // Generate unique slug if not explicitly passed
        $slug = trim($data['slug'] ?? '');
        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        } else {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $slug), '-'));
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM `events` WHERE `user_id` = :user_id AND `slug` = :slug LIMIT 1");
        $stmt->execute(['user_id' => $user['id'], 'slug' => $slug]);
        if ($stmt->fetch()) {
            $slug .= '-' . time();
        }

        $eventId = $this->eventModel->createEvent([
            'user_id'             => $user['id'],
            'name'                => $name,
            'slug'                => $slug,
            'description'         => $description,
            'duration_minutes'    => $durationMinutes,
            'buffer_minutes'      => $bufferMinutes,
            'booking_window_days' => $bookingWindowDays,
            'location_type'       => $locationType,
            'is_paid'             => $isPaid,
            'price'               => $price,
            'currency'            => $currency,
            'status'              => $status
        ]);

        $event = $this->eventModel->findByIdAndUserId($eventId, $user['id']);
        $this->response->json(['status' => 'success', 'message' => 'Event created successfully.', 'event' => $event]);
    }

    public function updateEvent(string $id): void {
        $user = $this->authenticateToken();
        $eventId = (int)$id;
        $data = $this->request->getBody();

        $event = $this->eventModel->findByIdAndUserId($eventId, $user['id']);
        if (!$event) {
            $this->response->json(['status' => 'error', 'message' => 'Event not found.'], 404);
            return;
        }

        $name = trim($data['name'] ?? $event['name']);
        $description = trim($data['description'] ?? $event['description']);
        $durationMinutes = (int)($data['duration_minutes'] ?? $event['duration_minutes']);
        $bufferMinutes = (int)($data['buffer_minutes'] ?? $event['buffer_minutes']);
        $bookingWindowDays = (int)($data['booking_window_days'] ?? $event['booking_window_days']);
        $locationType = trim($data['location_type'] ?? $event['location_type']);
        $price = (float)($data['price'] ?? $event['price']);
        $currency = strtoupper(trim($data['currency'] ?? $event['currency']));
        $isPaid = isset($data['is_paid']) ? (int)$data['is_paid'] : ($price > 0 ? 1 : 0);
        $status = trim($data['status'] ?? $event['status']);
        
        $slug = trim($data['slug'] ?? $event['slug']);
        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        } else {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $slug), '-'));
        }

        // Verify slug unique among other events
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM `events` WHERE `user_id` = :user_id AND `slug` = :slug AND `id` != :id LIMIT 1");
        $stmt->execute(['user_id' => $user['id'], 'slug' => $slug, 'id' => $eventId]);
        if ($stmt->fetch()) {
            $slug .= '-' . time();
        }

        $this->eventModel->updateEvent($eventId, $user['id'], [
            'name'                => $name,
            'slug'                => $slug,
            'description'         => $description,
            'duration_minutes'    => $durationMinutes,
            'buffer_minutes'      => $bufferMinutes,
            'booking_window_days' => $bookingWindowDays,
            'location_type'       => $locationType,
            'is_paid'             => $isPaid,
            'price'               => $price,
            'currency'            => $currency,
            'status'              => $status
        ]);

        $updated = $this->eventModel->findByIdAndUserId($eventId, $user['id']);
        $this->response->json(['status' => 'success', 'message' => 'Event updated successfully.', 'event' => $updated]);
    }

    public function deleteEvent(string $id): void {
        $user = $this->authenticateToken();
        $eventId = (int)$id;

        $event = $this->eventModel->findByIdAndUserId($eventId, $user['id']);
        if (!$event) {
            $this->response->json(['status' => 'error', 'message' => 'Event not found.'], 404);
            return;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM `events` WHERE `id` = :id AND `user_id` = :user_id");
        $stmt->execute(['id' => $eventId, 'user_id' => $user['id']]);

        $this->response->json(['status' => 'success', 'message' => 'Event deleted successfully.']);
    }

    public function getPromos(): void {
        $user = $this->authenticateToken();
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM `promo_codes` WHERE `user_id` = :user_id ORDER BY `id` DESC");
        $stmt->execute(['user_id' => $user['id']]);
        $promos = $stmt->fetchAll();
        $this->response->json(['status' => 'success', 'promos' => $promos]);
    }

    public function createPromo(): void {
        $user = $this->authenticateToken();
        $data = $this->request->getBody();

        $code = strtoupper(trim($data['code'] ?? ''));
        $discountType = trim($data['discount_type'] ?? 'percentage');
        $discountValue = (float)($data['discount_value'] ?? 0);
        $maxUses = !empty($data['max_uses']) ? (int)$data['max_uses'] : null;
        $expiresAt = !empty($data['expires_at']) ? trim($data['expires_at']) : null;

        if (empty($code) || $discountValue <= 0) {
            $this->response->json(['status' => 'error', 'message' => 'Code and a positive discount value are required.'], 400);
            return;
        }

        $db = Database::getInstance();
        // Check duplicate
        $stmt = $db->prepare("SELECT id FROM `promo_codes` WHERE `code` = :code AND `user_id` = :user_id LIMIT 1");
        $stmt->execute(['code' => $code, 'user_id' => $user['id']]);
        if ($stmt->fetch()) {
            $this->response->json(['status' => 'error', 'message' => 'Promo code already exists.'], 409);
            return;
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

        $this->response->json(['status' => 'success', 'message' => 'Promo code created successfully.']);
    }

    public function deletePromo(string $id): void {
        $user = $this->authenticateToken();
        $promoId = (int)$id;

        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM `promo_codes` WHERE `id` = :id AND `user_id` = :user_id");
        $stmt->execute(['id' => $promoId, 'user_id' => $user['id']]);

        $this->response->json(['status' => 'success', 'message' => 'Promo code deleted successfully.']);
    }

    public function register(): void {
        $data = $this->request->getBody();

        $name = trim($data['name'] ?? '');
        $username = strtolower(trim($data['username'] ?? ''));
        $email = strtolower(trim($data['email'] ?? ''));
        $password = $data['password'] ?? '';

        if (empty($name) || empty($username) || empty($email) || empty($password)) {
            $this->response->json(['status' => 'error', 'message' => 'Please fill in all required fields.'], 200);
            return;
        }

        if (strlen($password) < 6) {
            $this->response->json(['status' => 'error', 'message' => 'Password must be at least 6 characters long.'], 200);
            return;
        }

        if (!preg_match('/^[a-z0-9_-]+$/i', $username)) {
            $this->response->json(['status' => 'error', 'message' => 'Username can only contain letters, numbers, underscores and hyphens.'], 200);
            return;
        }

        if ($this->userModel->findByEmail($email)) {
            $this->response->json(['status' => 'error', 'message' => 'An account with this email already exists.'], 200);
            return;
        }

        if ($this->userModel->findByUsername($username)) {
            $this->response->json(['status' => 'error', 'message' => 'Username is already taken.'], 200);
            return;
        }

        // Create user
        $userId = $this->userModel->create([
            'name'     => $name,
            'username' => $username,
            'email'    => $email,
            'password' => $password
        ]);

        $user = $this->userModel->findById($userId);

        // Generate API Token
        $tokenStr = bin2hex(random_bytes(32));
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO `api_tokens` (`user_id`, `name`, `token`) VALUES (:user_id, 'Mobile Auth Token', :token)");
        $stmt->execute(['user_id' => $user['id'], 'token' => $tokenStr]);

        unset($user['password_hash']);
        $this->response->json([
            'status'  => 'success',
            'message' => 'Account created successfully!',
            'token'   => $tokenStr,
            'user'    => $user
        ]);
    }

    public function updateProfile(): void {
        $user = $this->authenticateToken();
        $data = $this->request->getBody();

        $name = trim($data['name'] ?? '');
        $newUsername = strtolower(trim($data['username'] ?? ''));
        $phone = trim($data['phone'] ?? '');
        $timezone = trim($data['timezone'] ?? 'UTC');
        $companyName = trim($data['company_name'] ?? '');
        $customDomain = trim($data['custom_domain'] ?? '');
        $bio = trim($data['bio'] ?? '');
        $upiId = trim($data['upi_id'] ?? '');

        if (empty($name)) {
            $this->response->json(['status' => 'error', 'message' => 'Name cannot be empty.'], 400);
            return;
        }

        if (empty($newUsername)) {
            $this->response->json(['status' => 'error', 'message' => 'Username cannot be empty.'], 400);
            return;
        }

        if (!preg_match('/^[a-z0-9_-]+$/i', $newUsername)) {
            $this->response->json(['status' => 'error', 'message' => 'Username can only contain letters, numbers, underscores and hyphens.'], 400);
            return;
        }

        // Check if username is already taken by another user
        $chkUser = $this->userModel->findByUsername($newUsername);
        if ($chkUser && (int)$chkUser['id'] !== (int)$user['id']) {
            $this->response->json(['status' => 'error', 'message' => 'Username is already taken by another user.'], 409);
            return;
        }

        // Get current profile
        $profile = $this->profileModel->findByUserId($user['id']);
        $avatarUrl = $profile['avatar'] ?? null;
        $qrCodeUrl = $profile['qr_code'] ?? null;

        if (!empty($_FILES['avatar_file']['name'])) {
            $file = $_FILES['avatar_file'];
            $uploadDir = PUBLIC_DIR . '/uploads/logos/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = 'logo_' . $user['id'] . '_' . time() . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $newFileName)) {
                $avatarUrl = 'public/uploads/logos/' . $newFileName;
            }
        }

        if (!empty($_FILES['qr_code_file']['name'])) {
            $file = $_FILES['qr_code_file'];
            $uploadDir = PUBLIC_DIR . '/uploads/qrcodes/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = 'qr_' . $user['id'] . '_' . time() . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $newFileName)) {
                $qrCodeUrl = 'public/uploads/qrcodes/' . $newFileName;
            }
        }

        // Update user
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE `users` SET `name` = :name, `username` = :username WHERE `id` = :id");
        $stmt->execute([
            'name'     => $name,
            'username' => $newUsername,
            'id'       => $user['id']
        ]);

        // Update Profile
        $this->profileModel->updateByUserId($user['id'], [
            'phone'         => $phone,
            'timezone'      => $timezone,
            'company_name'  => $companyName,
            'custom_domain' => $customDomain,
            'bio'           => $bio,
            'avatar'        => $avatarUrl,
            'upi_id'        => $upiId,
            'qr_code'       => $qrCodeUrl
        ]);

        $updatedProfile = $this->profileModel->findByUserId($user['id']);
        $this->response->json([
            'status'  => 'success',
            'message' => 'Profile updated successfully.',
            'profile' => $updatedProfile
        ]);
    }
}
