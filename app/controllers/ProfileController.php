<?php

namespace App\Controllers;

use App\Core\Controller;
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
        $profile = $this->profileModel->findByUserId($user['id']);

        $this->render('profile/index', [
            'user'    => $user,
            'profile' => $profile,
            'success' => Session::flash('success'),
            'error'   => Session::flash('error')
        ]);
    }

    public function update(): void {
        $user = $this->requireAuth();
        $data = $this->request->getBody();

        if (!Session::verifyCsrfToken($data['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid security token.');
            $this->response->redirect(APP_URL . '/profile');
        }

        $name = trim($data['name'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $timezone = trim($data['timezone'] ?? 'UTC');
        $bio = trim($data['bio'] ?? '');

        if (empty($name)) {
            Session::flash('error', 'Name cannot be empty.');
            $this->response->redirect(APP_URL . '/profile');
        }

        // Update user name
        $db = \App\Core\Database::getInstance();
        $stmt = $db->prepare("UPDATE `users` SET `name` = :name WHERE `id` = :id");
        $stmt->execute(['name' => $name, 'id' => $user['id']]);

        // Update session user name
        $user['name'] = $name;
        Session::set('user', $user);

        // Update Profile
        $this->profileModel->updateByUserId($user['id'], [
            'phone'    => $phone,
            'timezone' => $timezone,
            'bio'      => $bio,
            'avatar'   => null
        ]);

        Session::flash('success', 'Profile updated successfully.');
        $this->response->redirect(APP_URL . '/profile');
    }
}
