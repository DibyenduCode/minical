<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\Profile;
use App\Models\User;

class ProfileController extends Controller {
    private Profile $profileModel;
    private User $userModel;

    public function __construct() {
        parent::__construct();
        $this->profileModel = new Profile();
        $this->userModel = new User();
    }

    public function index(): void {
        $user = $this->requireAuth();
        $dbUser = $this->userModel->findById($user['id']);
        $profile = $this->profileModel->findByUserId($user['id']);

        $userPlanSlug = $dbUser['plan'] ?? 'free';
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM `plans` WHERE `slug` = :slug LIMIT 1");
        $stmt->execute(['slug' => $userPlanSlug]);
        $planDetails = $stmt->fetch();

        $stmtGoogle = $db->prepare("SELECT * FROM `google_accounts` WHERE `user_id` = :user_id LIMIT 1");
        $stmtGoogle->execute(['user_id' => $user['id']]);
        $googleAccount = $stmtGoogle->fetch();

        $isGoogleConnected = !empty($googleAccount);

        $plans = $db->query("SELECT * FROM `plans` WHERE `slug` != 'system' ORDER BY id ASC")->fetchAll();

        $this->render('profile/index', [
            'user'              => $user,
            'dbUser'            => $dbUser,
            'planDetails'       => $planDetails ?: null,
            'plans'             => $plans,
            'profile'           => $profile,
            'googleAccount'     => $googleAccount ?: null,
            'isGoogleConnected' => $isGoogleConnected,
            'success'           => Session::flash('success'),
            'error'             => Session::flash('error')
        ]);
    }

    public function update(): void {
        $user = $this->requireAuth();
        $data = $this->request->getBody();

        if (!Session::verifyCsrfToken($data['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid security token.');
            $this->response->redirect(APP_URL . '/profile');
        }

        $dbUser = $this->userModel->findById($user['id']);
        $userPlanSlug = $dbUser['plan'] ?? 'free';
        
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM `plans` WHERE `slug` = :slug LIMIT 1");
        $stmt->execute(['slug' => $userPlanSlug]);
        $planDetails = $stmt->fetch();
        
        $allowCustomDomain = isset($planDetails['allow_custom_domain']) ? (int)$planDetails['allow_custom_domain'] : 0;
        if (!$allowCustomDomain) {
            $data['custom_domain'] = '';
        }

        $name = trim($data['name'] ?? '');
        $newUsername = strtolower(trim($data['username'] ?? ''));
        $phone = trim($data['phone'] ?? '');
        $timezone = trim($data['timezone'] ?? 'UTC');
        $companyName = trim($data['company_name'] ?? '');
        $customDomain = trim($data['custom_domain'] ?? '');
        $bio = trim($data['bio'] ?? '');
        $upiId = trim($data['upi_id'] ?? '');

        if (empty($name)) {
            Session::flash('error', 'Name cannot be empty.');
            $this->response->redirect(APP_URL . '/profile');
        }

        if (empty($newUsername)) {
            Session::flash('error', 'Username cannot be empty.');
            $this->response->redirect(APP_URL . '/profile');
        }

        if (!preg_match('/^[a-z0-9_-]+$/i', $newUsername)) {
            Session::flash('error', 'Username can only contain letters, numbers, underscores and hyphens.');
            $this->response->redirect(APP_URL . '/profile');
        }

        // Check if username is already taken by another user
        $chkUser = $this->userModel->findByUsername($newUsername);
        if ($chkUser && (int)$chkUser['id'] !== (int)$user['id']) {
            Session::flash('error', 'Username is already taken by another user.');
            $this->response->redirect(APP_URL . '/profile');
        }

        // Fetch current profile for avatar and qr fallbacks
        $profile = $this->profileModel->findByUserId($user['id']);
        $avatarUrl = $profile['avatar'] ?? null;
        $qrCodeUrl = $profile['qr_code'] ?? null;

        // Process File Upload for Logo / Avatar
        if (!empty($_FILES['avatar_file']['name'])) {
            $file = $_FILES['avatar_file'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            if (!in_array($file['type'], $allowedTypes)) {
                Session::flash('error', 'Only JPG, PNG, GIF, and WEBP image file formats are allowed.');
                $this->response->redirect(APP_URL . '/profile');
            }

            $uploadDir = PUBLIC_DIR . '/uploads/logos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = 'logo_' . $user['id'] . '_' . time() . '.' . $ext;
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $avatarUrl = 'public/uploads/logos/' . $newFileName;
            } else {
                error_log("ProfileController Error: Failed to upload logo to " . $destination);
            }
        }

        // Process File Upload for Payment QR Code
        if (!empty($_FILES['qr_code_file']['name'])) {
            $file = $_FILES['qr_code_file'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            if (!in_array($file['type'], $allowedTypes)) {
                Session::flash('error', 'Only JPG, PNG, GIF, and WEBP image file formats are allowed for QR Code.');
                $this->response->redirect(APP_URL . '/profile');
            }

            $uploadDir = PUBLIC_DIR . '/uploads/qrcodes/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = 'qr_' . $user['id'] . '_' . time() . '.' . $ext;
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $qrCodeUrl = 'public/uploads/qrcodes/' . $newFileName;
            } else {
                error_log("ProfileController Error: Failed to upload QR code to " . $destination);
            }
        }

        // Update user name & username
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE `users` SET `name` = :name, `username` = :username WHERE `id` = :id");
        $stmt->execute([
            'name'     => $name,
            'username' => $newUsername,
            'id'       => $user['id']
        ]);

        // Update session user name, username and plan
        $user['name'] = $name;
        $user['username'] = $newUsername;
        $user['plan'] = $dbUser['plan'] ?? 'free';
        Session::set('user', $user);

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

        Session::flash('success', 'Branding & Profile settings updated successfully.');
        $this->response->redirect(APP_URL . '/profile');
    }

    public function changePassword(): void {
        $user = $this->requireAuth();
        $data = $this->request->getBody();

        if (!Session::verifyCsrfToken($data['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid security token.');
            $this->response->redirect(APP_URL . '/profile');
        }

        $currentPassword = $data['current_password'] ?? '';
        $newPassword = $data['new_password'] ?? '';
        $confirmPassword = $data['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            Session::flash('error', 'Please fill in all password fields.');
            $this->response->redirect(APP_URL . '/profile');
        }

        if (strlen($newPassword) < 6) {
            Session::flash('error', 'New password must be at least 6 characters long.');
            $this->response->redirect(APP_URL . '/profile');
        }

        if ($newPassword !== $confirmPassword) {
            Session::flash('error', 'New password confirmation does not match.');
            $this->response->redirect(APP_URL . '/profile');
        }

        $dbUser = $this->userModel->findById($user['id']);
        if (!$this->userModel->verifyPassword($currentPassword, $dbUser['password_hash'])) {
            Session::flash('error', 'Current password is incorrect.');
            $this->response->redirect(APP_URL . '/profile');
        }

        $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE `users` SET `password_hash` = :hash WHERE `id` = :id");
        $stmt->execute(['hash' => $newHash, 'id' => $user['id']]);

        Session::flash('success', 'Your password has been updated successfully.');
        $this->response->redirect(APP_URL . '/profile');
    }

    public function verifyPlanPromo(): void {
        $user = $this->requireAuth();
        $data = $this->request->getBody();

        $code = strtoupper(trim($data['promo_code'] ?? ''));
        $planSlug = trim($data['plan_slug'] ?? '');

        if (empty($code) || empty($planSlug)) {
            $this->response->json(['status' => 'error', 'message' => 'Please enter a promo code and select a plan.'], 400);
            return;
        }

        $db = Database::getInstance();
        $stmtPlan = $db->prepare("SELECT * FROM `plans` WHERE `slug` = :slug LIMIT 1");
        $stmtPlan->execute(['slug' => $planSlug]);
        $plan = $stmtPlan->fetch();

        if (!$plan) {
            $this->response->json(['status' => 'error', 'message' => 'Selected plan tier is invalid.'], 400);
            return;
        }

        $price = (float)$plan['price'];

        $stmtPromo = $db->prepare("SELECT * FROM `promo_codes` WHERE `code` = :code LIMIT 1");
        $stmtPromo->execute(['code' => $code]);
        $promo = $stmtPromo->fetch();

        if (!$promo) {
            $this->response->json(['status' => 'error', 'message' => 'Invalid promo code.'], 400);
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

        if (!empty($promo['plan_slug']) && $promo['plan_slug'] !== $planSlug) {
            $this->response->json(['status' => 'error', 'message' => 'This promo code does not apply to the selected plan.'], 400);
            return;
        }

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

    public function upgradePlan(): void {
        $user = $this->requireAuth();
        $data = $this->request->getBody();

        if (!Session::verifyCsrfToken($data['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid security token.');
            $this->response->redirect(APP_URL . '/profile');
        }

        $planSlug = trim($data['plan_slug'] ?? '');
        $promoCode = strtoupper(trim($data['promo_code'] ?? ''));

        if (empty($planSlug)) {
            Session::flash('error', 'Please select a plan to upgrade.');
            $this->response->redirect(APP_URL . '/profile');
        }

        $db = Database::getInstance();
        $stmtPlan = $db->prepare("SELECT * FROM `plans` WHERE `slug` = :slug LIMIT 1");
        $stmtPlan->execute(['slug' => $planSlug]);
        $plan = $stmtPlan->fetch();

        if (!$plan || $plan['slug'] === 'system') {
            Session::flash('error', 'Invalid subscription plan selected.');
            $this->response->redirect(APP_URL . '/profile');
        }

        if (!empty($promoCode)) {
            $stmtPromo = $db->prepare("SELECT * FROM `promo_codes` WHERE `code` = :code LIMIT 1");
            $stmtPromo->execute(['code' => $promoCode]);
            $promo = $stmtPromo->fetch();

            if (!$promo || $promo['status'] !== 'active') {
                Session::flash('error', 'Invalid or inactive promo code.');
                $this->response->redirect(APP_URL . '/profile');
            }

            if (!empty($promo['expires_at']) && strtotime($promo['expires_at']) < time()) {
                Session::flash('error', 'Expired promo code.');
                $this->response->redirect(APP_URL . '/profile');
            }

            if (!empty($promo['max_uses']) && $promo['used_count'] >= $promo['max_uses']) {
                Session::flash('error', 'Promo code usage limit reached.');
                $this->response->redirect(APP_URL . '/profile');
            }

            if (!empty($promo['plan_slug']) && $promo['plan_slug'] !== $planSlug) {
                Session::flash('error', 'This promo code does not apply to the selected plan.');
                $this->response->redirect(APP_URL . '/profile');
            }

            $up = $db->prepare("UPDATE `promo_codes` SET `used_count` = `used_count` + 1 WHERE `id` = :id");
            $up->execute(['id' => $promo['id']]);
        }

        $update = $db->prepare("UPDATE `users` SET `plan` = :plan WHERE `id` = :id");
        $update->execute(['plan' => $planSlug, 'id' => $user['id']]);

        $updatedUser = $this->userModel->findById($user['id']);
        unset($updatedUser['password_hash']);
        Session::set('user', $updatedUser);

        Session::flash('success', "Congratulations! Your subscription has been upgraded to the " . htmlspecialchars($plan['name']) . " tier.");
        $this->response->redirect(APP_URL . '/profile');
    }
}
