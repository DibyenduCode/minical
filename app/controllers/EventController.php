<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\Event;
use App\Models\User;

class EventController extends Controller {
    private Event $eventModel;
    private User $userModel;

    public function __construct() {
        parent::__construct();
        $this->eventModel = new Event();
        $this->userModel = new User();
    }

    public function index(): void {
        $user = $this->requireAuth();
        $dbUser = $this->userModel->findById($user['id']);
        $events = $this->eventModel->getByUserId($user['id']);

        $userPlanSlug = $dbUser['plan'] ?? 'free';
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM `plans` WHERE `slug` = :slug LIMIT 1");
        $stmt->execute(['slug' => $userPlanSlug]);
        $planDetails = $stmt->fetch();

        if (($user['role'] ?? '') === 'admin' && $planDetails) {
            $planDetails['max_events'] = -1;
        }

        $this->render('event/index', [
            'user'        => $user,
            'dbUser'      => $dbUser,
            'planDetails' => $planDetails ?: null,
            'events'      => $events,
            'success'     => Session::flash('success'),
            'error'       => Session::flash('error')
        ]);
    }

    public function create(): void {
        $user = $this->requireAuth();
        $data = $this->request->getBody();

        if (!Session::verifyCsrfToken($data['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid security token.');
            $this->response->redirect(APP_URL . '/event');
        }

        $dbUser = $this->userModel->findById($user['id']);
        $userPlanSlug = $dbUser['plan'] ?? 'free';
        
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM `plans` WHERE `slug` = :slug LIMIT 1");
        $stmt->execute(['slug' => $userPlanSlug]);
        $planDetails = $stmt->fetch();

        if (($user['role'] ?? '') === 'admin') {
            $maxEvents = -1;
        } else {
            $maxEvents = isset($planDetails['max_events']) ? (int)$planDetails['max_events'] : -1;
        }

        if ($maxEvents !== -1) {
            $existingEvents = $this->eventModel->getByUserId($user['id']);
            if (count($existingEvents) >= $maxEvents) {
                Session::flash('error', "You have reached the maximum limit of {$maxEvents} event type(s) allowed on your plan. Please upgrade.");
                $this->response->redirect(APP_URL . '/event');
            }
        }

        $name = trim($data['name'] ?? '');
        $slug = strtolower(trim($data['slug'] ?? ''));

        if (empty($name)) {
            Session::flash('error', 'Event Name is required.');
            $this->response->redirect(APP_URL . '/event');
        }

        if (empty($slug)) {
            $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($name));
            $slug = trim($slug, '-');
        }
        if (empty($slug)) {
            $slug = 'event-' . time();
        }

        // Ensure unique slug per user
        $existing = $this->eventModel->findBySlugAndUserId($slug, $user['id']);
        if ($existing) {
            $slug = $slug . '-' . rand(100, 999);
        }

        $this->eventModel->createEvent([
            'user_id'             => $user['id'],
            'name'                => $name,
            'slug'                => $slug,
            'description'         => $data['description'] ?? '',
            'duration_minutes'    => (int)($data['duration_minutes'] ?? 30),
            'buffer_minutes'      => (int)($data['buffer_minutes'] ?? 0),
            'booking_window_days' => (int)($data['booking_window_days'] ?? 30),
            'location_type'       => $data['location_type'] ?? 'online',
            'is_paid'             => isset($data['is_paid']) ? 1 : 0,
            'price'               => (float)($data['price'] ?? 0.00),
            'currency'            => $data['currency'] ?? 'USD',
            'status'              => $data['status'] ?? 'active'
        ]);

        Session::flash('success', 'New Event Type created successfully.');
        $this->response->redirect(APP_URL . '/event');
    }

    public function update(string $id): void {
        $user = $this->requireAuth();
        $eventId = (int)$id;
        $data = $this->request->getBody();

        if (!Session::verifyCsrfToken($data['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid security token.');
            $this->response->redirect(APP_URL . '/event');
        }

        $event = $this->eventModel->findById($eventId);
        if ($event && (int)$event['user_id'] === (int)$user['id']) {
            $this->eventModel->updateEvent($eventId, $user['id'], [
                'name'                => trim($data['name']),
                'slug'                => strtolower(trim($data['slug'])),
                'description'         => $data['description'] ?? '',
                'duration_minutes'    => (int)($data['duration_minutes'] ?? 30),
                'buffer_minutes'      => (int)($data['buffer_minutes'] ?? 0),
                'booking_window_days' => (int)($data['booking_window_days'] ?? 30),
                'location_type'       => $data['location_type'] ?? 'online',
                'is_paid'             => isset($data['is_paid']) ? 1 : 0,
                'price'               => (float)($data['price'] ?? 0.00),
                'currency'            => $data['currency'] ?? 'USD',
                'status'              => $data['status'] ?? 'active'
            ]);
            Session::flash('success', 'Event Type updated successfully.');
        } else {
            Session::flash('error', 'Event Type not found.');
        }

        $this->response->redirect(APP_URL . '/event');
    }

    public function delete(string $id): void {
        $user = $this->requireAuth();
        $eventId = (int)$id;

        $event = $this->eventModel->findById($eventId);
        if ($event && (int)$event['user_id'] === (int)$user['id']) {
            $this->eventModel->delete($eventId);
            Session::flash('success', 'Event Type deleted successfully.');
        } else {
            Session::flash('error', 'Event Type not found.');
        }

        $this->response->redirect(APP_URL . '/event');
    }
}
