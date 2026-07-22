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

        $this->render('profile/index', [
            'user'              => $user,
            'dbUser'            => $dbUser,
            'planDetails'       => $planDetails ?: null,
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
        $phone = trim($data['phone'] ?? '');
        $timezone = trim($data['timezone'] ?? 'UTC');
        $companyName = trim($data['company_name'] ?? '');
        $customDomain = trim($data['custom_domain'] ?? '');
        $bio = trim($data['bio'] ?? '');

        if (empty($name)) {
            Session::flash('error', 'Name cannot be empty.');
            $this->response->redirect(APP_URL . '/profile');
        }

        // Fetch current profile for avatar fallback
        $profile = $this->profileModel->findByUserId($user['id']);
        $avatarUrl = $profile['avatar'] ?? null;

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

        // Update user name and plan
        $plan = trim($data['plan'] ?? 'free');
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE `users` SET `name` = :name, `plan` = :plan WHERE `id` = :id");
        $stmt->execute([
            'name' => $name,
            'plan' => $plan,
            'id'   => $user['id']
        ]);

        // Update session user name and plan
        $user['name'] = $name;
        $user['plan'] = $plan;
        Session::set('user', $user);

        // Update Profile
        $this->profileModel->updateByUserId($user['id'], [
            'phone'         => $phone,
            'timezone'      => $timezone,
            'company_name'  => $companyName,
            'custom_domain' => $customDomain,
            'bio'           => $bio,
            'avatar'        => $avatarUrl
        ]);

        Session::flash('success', 'Branding & Profile settings updated successfully.');
        $this->response->redirect(APP_URL . '/profile');
    }
}
