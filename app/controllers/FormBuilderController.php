<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\FormField;

class FormBuilderController extends Controller {
    private FormField $fieldModel;

    public function __construct() {
        parent::__construct();
        $this->fieldModel = new FormField();
    }

    public function index(): void {
        $user = $this->requireAuth();
        $fields = $this->fieldModel->getByUserId($user['id']);

        $this->render('form_builder/index', [
            'user'    => $user,
            'fields'  => $fields,
            'success' => Session::flash('success'),
            'error'   => Session::flash('error')
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
        $type = $data['field_type'] ?? 'text';

        if (empty($label)) {
            Session::flash('error', 'Field Label is required.');
            $this->response->redirect(APP_URL . '/form-builder');
        }

        $options = [];
        if (!empty($data['options_raw'])) {
            $options = array_map('trim', explode(',', $data['options_raw']));
        }

        $this->fieldModel->createField([
            'user_id'       => $user['id'],
            'label'         => $label,
            'field_type'    => $type,
            'options'       => $options,
            'placeholder'   => $data['placeholder'] ?? '',
            'help_text'     => $data['help_text'] ?? '',
            'is_required'   => isset($data['is_required']) ? 1 : 0,
            'display_order' => (int)($data['display_order'] ?? 0)
        ]);

        Session::flash('success', 'Custom field added.');
        $this->response->redirect(APP_URL . '/form-builder');
    }

    public function delete(string $id): void {
        $user = $this->requireAuth();
        $fieldId = (int)$id;

        $field = $this->fieldModel->findById($fieldId);
        if ($field && (int)$field['user_id'] === (int)$user['id']) {
            $this->fieldModel->delete($fieldId);
            Session::flash('success', 'Custom field deleted.');
        } else {
            Session::flash('error', 'Field not found.');
        }
        $this->response->redirect(APP_URL . '/form-builder');
    }
}
