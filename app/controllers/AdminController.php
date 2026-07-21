<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\Plan;

class AdminController extends Controller {
    private Plan $planModel;

    public function __construct() {
        parent::__construct();
        $this->planModel = new Plan();
    }

    protected function renderAdmin(string $view, array $params = []): void {
        extract($params);
        $csrf_token = Session::generateCsrfToken();
        
        $viewFile = TEMPLATES_DIR . '/admin/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("Admin view file `{$view}` not found.");
        }
    }

    public function index(): void {
        $adminUser = $this->requireAdmin();
        $db = Database::getInstance();

        // System Statistics
        $usersCount = (int)$db->query("SELECT COUNT(*) FROM `users`")->fetchColumn();
        $bookingsCount = (int)$db->query("SELECT COUNT(*) FROM `bookings`")->fetchColumn();
        $eventsCount = (int)$db->query("SELECT COUNT(*) FROM `events`")->fetchColumn();
        $revenueTotal = (float)$db->query("SELECT COALESCE(SUM(amount), 0) FROM `payments` WHERE status = 'success'")->fetchColumn();

        // Recent 5 Users
        $users = $db->query("SELECT * FROM `users` ORDER BY id DESC LIMIT 5")->fetchAll();

        // Recent 5 Bookings
        $allBookings = $db->query("
            SELECT b.*, u.name as host_name, u.username as host_username, e.name as event_name 
            FROM `bookings` b 
            JOIN `users` u ON u.id = b.user_id 
            JOIN `events` e ON e.id = b.event_id 
            ORDER BY b.id DESC LIMIT 5
        ")->fetchAll();

        $this->renderAdmin('index', [
            'admin'         => $adminUser,
            'adminTab'      => 'overview',
            'usersCount'    => $usersCount,
            'bookingsCount' => $bookingsCount,
            'eventsCount'   => $eventsCount,
            'revenueTotal'  => $revenueTotal,
            'users'         => $users,
            'allBookings'   => $allBookings,
            'success'       => Session::flash('success'),
            'error'         => Session::flash('error')
        ]);
    }

    public function plans(): void {
        $adminUser = $this->requireAdmin();
        $plans = $this->planModel->getAllPlans();

        $this->renderAdmin('plans', [
            'admin'    => $adminUser,
            'adminTab' => 'plans',
            'plans'    => $plans,
            'success'  => Session::flash('success'),
            'error'    => Session::flash('error')
        ]);
    }

    public function createPlan(): void {
        $this->requireAdmin();
        $data = $this->request->getBody();

        if (!Session::verifyCsrfToken($data['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid security token.');
            $this->response->redirect(APP_URL . '/admin/plans');
        }

        $name = trim($data['name'] ?? '');
        $price = trim($data['price'] ?? '0');

        if (empty($name)) {
            Session::flash('error', 'Plan Name is required.');
            $this->response->redirect(APP_URL . '/admin/plans');
        }

        $this->planModel->createPlan([
            'name'          => $name,
            'slug'          => strtolower(trim($data['slug'] ?? $name)),
            'price'         => $price,
            'billing_cycle' => $data['billing_cycle'] ?? 'per month',
            'description'   => $data['description'] ?? '',
            'features_raw'  => $data['features_raw'] ?? '',
            'badge'         => $data['badge'] ?? '',
            'button_text'   => $data['button_text'] ?? 'Get Started',
            'is_featured'   => isset($data['is_featured']) ? 1 : 0,
            'display_order' => (int)($data['display_order'] ?? 0),
            'status'        => $data['status'] ?? 'active'
        ]);

        Session::flash('success', 'New pricing plan created successfully.');
        $this->response->redirect(APP_URL . '/admin/plans');
    }

    public function deletePlan(string $id): void {
        $this->requireAdmin();
        $planId = (int)$id;

        $this->planModel->delete($planId);
        Session::flash('success', 'Plan deleted successfully.');
        $this->response->redirect(APP_URL . '/admin/plans');
    }

    public function users(): void {
        $adminUser = $this->requireAdmin();
        $db = Database::getInstance();

        $users = $db->query("
            SELECT u.*, 
                   (SELECT COUNT(*) FROM `bookings` b WHERE b.user_id = u.id) as total_bookings,
                   (SELECT COUNT(*) FROM `events` e WHERE e.user_id = u.id) as total_events
            FROM `users` u 
            ORDER BY u.id DESC
        ")->fetchAll();

        $this->renderAdmin('users', [
            'admin'    => $adminUser,
            'adminTab' => 'users',
            'users'    => $users,
            'success'  => Session::flash('success'),
            'error'    => Session::flash('error')
        ]);
    }

    public function bookings(): void {
        $adminUser = $this->requireAdmin();
        $db = Database::getInstance();

        $allBookings = $db->query("
            SELECT b.*, u.name as host_name, u.username as host_username, e.name as event_name 
            FROM `bookings` b 
            JOIN `users` u ON u.id = b.user_id 
            JOIN `events` e ON e.id = b.event_id 
            ORDER BY b.id DESC
        ")->fetchAll();

        $this->renderAdmin('bookings', [
            'admin'       => $adminUser,
            'adminTab'    => 'bookings',
            'allBookings' => $allBookings,
            'success'     => Session::flash('success'),
            'error'       => Session::flash('error')
        ]);
    }

    public function settings(): void {
        $adminUser = $this->requireAdmin();
        $db = Database::getInstance();

        $settingsStmt = $db->query("SELECT * FROM `settings`")->fetchAll();
        $settings = [];
        foreach ($settingsStmt as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        $this->renderAdmin('settings', [
            'admin'    => $adminUser,
            'adminTab' => 'settings',
            'settings' => $settings,
            'success'  => Session::flash('success'),
            'error'    => Session::flash('error')
        ]);
    }

    public function toggleUserStatus(string $id): void {
        $admin = $this->requireAdmin();
        $userId = (int)$id;

        if ($userId === (int)$admin['id']) {
            Session::flash('error', 'You cannot disable your own admin account.');
            $this->response->redirect(APP_URL . '/admin/users');
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT `status` FROM `users` WHERE `id` = :id");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();

        if ($user) {
            $newStatus = ($user['status'] === 'active') ? 'disabled' : 'active';
            $update = $db->prepare("UPDATE `users` SET `status` = :status WHERE `id` = :id");
            $update->execute(['status' => $newStatus, 'id' => $userId]);
            Session::flash('success', 'User status updated successfully.');
        } else {
            Session::flash('error', 'User not found.');
        }
        $this->response->redirect(APP_URL . '/admin/users');
    }

    public function deleteUser(string $id): void {
        $admin = $this->requireAdmin();
        $userId = (int)$id;

        if ($userId === (int)$admin['id']) {
            Session::flash('error', 'You cannot delete your own admin account.');
            $this->response->redirect(APP_URL . '/admin/users');
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM `users` WHERE `id` = :id");
        $stmt->execute(['id' => $userId]);

        Session::flash('success', 'User account and associated data deleted.');
        $this->response->redirect(APP_URL . '/admin/users');
    }

    public function saveSettings(): void {
        $this->requireAdmin();
        $data = $this->request->getBody();

        if (!Session::verifyCsrfToken($data['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid security token.');
            $this->response->redirect(APP_URL . '/admin/settings');
        }

        $db = Database::getInstance();
        $keys = ['site_name', 'smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass'];

        foreach ($keys as $key) {
            if (isset($data[$key])) {
                $stmt = $db->prepare("
                    INSERT INTO `settings` (`setting_key`, `setting_value`)
                    VALUES (:key, :val)
                    ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`)
                ");
                $stmt->execute(['key' => $key, 'val' => trim($data[$key])]);
            }
        }

        Session::flash('success', 'System configurations saved successfully.');
        $this->response->redirect(APP_URL . '/admin/settings');
    }
}
