<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Availability;
use App\Models\Booking;
use App\Models\FormField;
use App\Models\Profile;
use App\Models\User;

class ApiController extends Controller {
    private User $userModel;
    private Profile $profileModel;
    private Availability $availabilityModel;
    private Booking $bookingModel;
    private FormField $fieldModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->profileModel = new Profile();
        $this->availabilityModel = new Availability();
        $this->bookingModel = new Booking();
        $this->fieldModel = new FormField();
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

        return $user;
    }

    public function login(): void {
        $data = $this->request->getBody();
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $user = $this->userModel->findByEmail($email);
        if (!$user || !$this->userModel->verifyPassword($password, $user['password_hash'])) {
            $this->response->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
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
}
