<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

class AuthController extends Controller {
    private User $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }

    public function showLogin(): void {
        if (Session::has('user')) {
            $this->response->redirect(APP_URL . '/dashboard');
        }
        $this->render('auth/login', [
            'error'   => Session::flash('error'),
            'success' => Session::flash('success')
        ]);
    }

    public function login(): void {
        $data = $this->request->getBody();
        $csrfToken = $data['csrf_token'] ?? '';

        if (!Session::verifyCsrfToken($csrfToken)) {
            Session::flash('error', 'Invalid security token.');
            $this->response->redirect(APP_URL . '/login');
        }

        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            Session::flash('error', 'Please fill in all fields.');
            $this->response->redirect(APP_URL . '/login');
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user || !$this->userModel->verifyPassword($password, $user['password_hash'])) {
            Session::flash('error', 'Invalid email or password.');
            $this->response->redirect(APP_URL . '/login');
        }

        if ($user['status'] !== 'active') {
            Session::flash('error', 'Your account has been disabled.');
            $this->response->redirect(APP_URL . '/login');
        }

        unset($user['password_hash']);
        Session::set('user', $user);
        Session::flash('success', 'Welcome back, ' . htmlspecialchars($user['name']));

        if ($user['role'] === 'admin') {
            $this->response->redirect(APP_URL . '/admin');
        } else {
            $this->response->redirect(APP_URL . '/dashboard');
        }
    }

    public function showRegister(): void {
        if (Session::has('user')) {
            $this->response->redirect(APP_URL . '/dashboard');
        }
        $this->render('auth/register', [
            'error'   => Session::flash('error'),
            'success' => Session::flash('success')
        ]);
    }

    public function register(): void {
        $data = $this->request->getBody();
        $csrfToken = $data['csrf_token'] ?? '';

        if (!Session::verifyCsrfToken($csrfToken)) {
            Session::flash('error', 'Invalid security token.');
            $this->response->redirect(APP_URL . '/register');
        }

        $name = trim($data['name'] ?? '');
        $username = strtolower(trim($data['username'] ?? ''));
        $email = strtolower(trim($data['email'] ?? ''));
        $password = $data['password'] ?? '';

        if (empty($name) || empty($username) || empty($email) || empty($password)) {
            Session::flash('error', 'Please fill in all required fields.');
            $this->response->redirect(APP_URL . '/register');
        }

        if (strlen($password) < 6) {
            Session::flash('error', 'Password must be at least 6 characters long.');
            $this->response->redirect(APP_URL . '/register');
        }

        if (!preg_match('/^[a-z0-9_-]+$/i', $username)) {
            Session::flash('error', 'Username can only contain letters, numbers, underscores and hyphens.');
            $this->response->redirect(APP_URL . '/register');
        }

        if ($this->userModel->findByEmail($email)) {
            Session::flash('error', 'An account with this email already exists.');
            $this->response->redirect(APP_URL . '/register');
        }

        if ($this->userModel->findByUsername($username)) {
            Session::flash('error', 'Username is already taken.');
            $this->response->redirect(APP_URL . '/register');
        }

        $userId = $this->userModel->create([
            'name'     => $name,
            'username' => $username,
            'email'    => $email,
            'password' => $password
        ]);

        $user = $this->userModel->findById($userId);
        unset($user['password_hash']);
        Session::set('user', $user);
        Session::flash('success', 'Account created successfully! Welcome to MiniCal.');
        $this->response->redirect(APP_URL . '/dashboard');
    }

    public function logout(): void {
        Session::destroy();
        header("Location: " . APP_URL . "/login");
        exit;
    }

    public function showForgotPassword(): void {
        if (Session::has('user')) {
            $this->response->redirect(APP_URL . '/dashboard');
        }
        $this->render('auth/forgot_password', [
            'error'   => Session::flash('error'),
            'success' => Session::flash('success')
        ]);
    }

    public function forgotPassword(): void {
        $data = $this->request->getBody();
        if (!Session::verifyCsrfToken($data['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid security token.');
            $this->response->redirect(APP_URL . '/forgot-password');
        }

        $email = strtolower(trim($data['email'] ?? ''));
        if (empty($email)) {
            Session::flash('error', 'Please enter your email address.');
            $this->response->redirect(APP_URL . '/forgot-password');
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            Session::flash('success', 'If this email is registered, a password reset link has been sent.');
            $this->response->redirect(APP_URL . '/forgot-password');
        }

        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + 3600);

        $db = \App\Core\Database::getInstance();
        $del = $db->prepare("DELETE FROM `password_resets` WHERE `email` = :email");
        $del->execute(['email' => $email]);

        $ins = $db->prepare("INSERT INTO `password_resets` (`email`, `token`, `expires_at`) VALUES (:email, :token, :expires_at)");
        $ins->execute([
            'email'      => $email,
            'token'      => $token,
            'expires_at' => $expiresAt
        ]);

        $resetLink = APP_URL . '/reset-password/' . $token;

        if (\App\Services\EmailService::sendPasswordReset($email, $resetLink)) {
            Session::flash('success', 'If this email is registered, a password reset link has been sent.');
        } else {
            Session::flash('error', 'Failed to send password reset email. Please contact administrator.');
        }

        $this->response->redirect(APP_URL . '/forgot-password');
    }

    public function showResetPassword(string $token): void {
        if (Session::has('user')) {
            $this->response->redirect(APP_URL . '/dashboard');
        }
        $this->render('auth/reset_password', [
            'token'   => $token,
            'error'   => Session::flash('error'),
            'success' => Session::flash('success')
        ]);
    }

    public function resetPassword(): void {
        $data = $this->request->getBody();
        if (!Session::verifyCsrfToken($data['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid security token.');
            $this->response->redirect(APP_URL . '/login');
        }

        $token = trim($data['token'] ?? '');
        $password = $data['password'] ?? '';
        $passwordConfirm = $data['password_confirm'] ?? '';

        if (empty($token) || empty($password) || empty($passwordConfirm)) {
            Session::flash('error', 'Please fill in all fields.');
            $this->response->redirect(APP_URL . '/reset-password/' . $token);
        }

        if (strlen($password) < 6) {
            Session::flash('error', 'Password must be at least 6 characters long.');
            $this->response->redirect(APP_URL . '/reset-password/' . $token);
        }

        if ($password !== $passwordConfirm) {
            Session::flash('error', 'Passwords do not match.');
            $this->response->redirect(APP_URL . '/reset-password/' . $token);
        }

        $db = \App\Core\Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM `password_resets` WHERE `token` = :token LIMIT 1");
        $stmt->execute(['token' => $token]);
        $reset = $stmt->fetch();

        if (!$reset || strtotime($reset['expires_at']) < time()) {
            Session::flash('error', 'Invalid or expired password reset link.');
            $this->response->redirect(APP_URL . '/forgot-password');
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $update = $db->prepare("UPDATE `users` SET `password_hash` = :hash WHERE `email` = :email");
        $update->execute(['hash' => $passwordHash, 'email' => $reset['email']]);

        $del = $db->prepare("DELETE FROM `password_resets` WHERE `email` = :email");
        $del->execute(['email' => $reset['email']]);

        Session::flash('success', 'Your password has been reset successfully. You can now sign in.');
        $this->response->redirect(APP_URL . '/login');
    }
}
