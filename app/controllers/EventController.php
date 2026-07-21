<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Event;

class EventController extends Controller {
    private Event $eventModel;

    public function __construct() {
        parent::__construct();
        $this->eventModel = new Event();
    }

    public function index(): void {
        $user = $this->requireAuth();
        $events = $this->eventModel->getByUserId($user['id']);

        $this->render('event/index', [
            'user'    => $user,
            'events'  => $events,
            'success' => Session::flash('success'),
            'error'   => Session::flash('error')
        ]);
    }

    public function create(): void {
        $user = $this->requireAuth();
        $data = $this->request->getBody();

        if (!Session::verifyCsrfToken($data['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid security token.');
            $this->response->redirect(APP_URL . '/event');
        }

        $name = trim($data['name'] ?? '');
        $slug = strtolower(trim($data['slug'] ?? ''));

        if (empty($name) || empty($slug)) {
            Session::flash('error', 'Event Name and URL Slug are required.');
            $this->response->redirect(APP_URL . '/event');
        }

        $this->eventModel->createEvent([
            'user_id'             => $user['id'],
            'name'                => $name,
            'slug'                => $slug,
            'description'         => $data['description'] ?? '',
            'duration_minutes'    => (int)($data['duration_minutes'] ?? 30),
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

        $name = trim($data['name'] ?? '');
        $slug = strtolower(trim($data['slug'] ?? ''));

        if (empty($name) || empty($slug)) {
            Session::flash('error', 'Event Name and URL Slug are required.');
            $this->response->redirect(APP_URL . '/event');
        }

        $this->eventModel->updateEvent($eventId, $user['id'], [
            'name'                => $name,
            'slug'                => $slug,
            'description'         => $data['description'] ?? '',
            'duration_minutes'    => (int)($data['duration_minutes'] ?? 30),
            'booking_window_days' => (int)($data['booking_window_days'] ?? 30),
            'location_type'       => $data['location_type'] ?? 'online',
            'is_paid'             => isset($data['is_paid']) ? 1 : 0,
            'price'               => (float)($data['price'] ?? 0.00),
            'currency'            => $data['currency'] ?? 'USD',
            'status'              => $data['status'] ?? 'active'
        ]);

        Session::flash('success', 'Event type updated successfully.');
        $this->response->redirect(APP_URL . '/event');
    }

    public function delete(string $id): void {
        $user = $this->requireAuth();
        $eventId = (int)$id;

        $event = $this->eventModel->findByIdAndUserId($eventId, $user['id']);
        if ($event) {
            $this->eventModel->delete($eventId);
            Session::flash('success', 'Event type deleted successfully.');
        } else {
            Session::flash('error', 'Event type not found.');
        }

        $this->response->redirect(APP_URL . '/event');
    }
}
