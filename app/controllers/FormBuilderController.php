<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\FormField;
use App\Models\Event;

class FormBuilderController extends Controller {
    private FormField $formFieldModel;
    private Event $eventModel;

    public function __construct() {
        parent::__construct();
        $this->formFieldModel = new FormField();
        $this->eventModel = new Event();
    }

    public function index(): void {
        $user = $this->requireAuth();
        $fields = $this->formFieldModel->getByUserId($user['id']);
        $events = $this->eventModel->getByUserId($user['id']);

        $this->render('form_builder/index', [
            'user'     => $user,
            'fields'   => $fields,
            'events'   => $events,
            'success'  => Session::flash('success'),
            'error'    => Session::flash('error')
        ]);
    }

    public function create(): void {
        $user = $this->requireAuth();
        $data = $this->request->getBody();

        if (!Session::verifyCsrfToken($data['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid security token.');
            $this->response->redirect(APP_URL . '/form-builder');
        }

        $label = trim($data['label'] ?? '');
        $fieldType = $data['field_type'] ?? 'text';
        $eventId = !empty($data['event_id']) ? (int)$data['event_id'] : null;

        if (empty($label)) {
            Session::flash('error', 'Field label is required.');
            $this->response->redirect(APP_URL . '/form-builder');
        }

        $options = [];
        if (in_array($fieldType, ['select', 'radio', 'checkbox']) && !empty($data['options_raw'])) {
            $lines = explode("\n", $data['options_raw']);
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (!empty($trimmed)) {
                    $options[] = $trimmed;
                }
            }
        }

        $this->formFieldModel->createField([
            'user_id'       => $user['id'],
            'event_id'      => $eventId,
            'label'         => $label,
            'field_type'    => $fieldType,
            'options'       => $options,
            'placeholder'   => $data['placeholder'] ?? '',
            'help_text'     => $data['help_text'] ?? '',
            'is_required'   => isset($data['is_required']) ? 1 : 0,
            'display_order' => (int)($data['display_order'] ?? 0)
        ]);

        Session::flash('success', 'Custom form field added successfully.');
        $this->response->redirect(APP_URL . '/form-builder');
    }

    public function delete(string $id): void {
        $user = $this->requireAuth();
        $fieldId = (int)$id;

        $stmt = $this->db->prepare("SELECT `user_id` FROM `booking_form_fields` WHERE `id` = :id");
        $stmt->execute(['id' => $fieldId]);
        $field = $stmt->fetch();

        if ($field && (int)$field['user_id'] === (int)$user['id']) {
            $this->formFieldModel->delete($fieldId);
            Session::flash('success', 'Form field deleted successfully.');
        } else {
            Session::flash('error', 'Form field not found or permission denied.');
        }

        $this->response->redirect(APP_URL . '/form-builder');
    }
}
