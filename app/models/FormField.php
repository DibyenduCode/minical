<?php

namespace App\Models;

use App\Core\Model;

class FormField extends Model {
    protected string $table = 'booking_form_fields';

    public function getByUserId(int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM `booking_form_fields` WHERE `user_id` = :user_id ORDER BY `display_order` ASC, `id` ASC");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function createField(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO `booking_form_fields` (`user_id`, `label`, `field_type`, `options`, `placeholder`, `help_text`, `is_required`, `display_order`)
            VALUES (:user_id, :label, :field_type, :options, :placeholder, :help_text, :is_required, :display_order)
        ");
        $stmt->execute([
            'user_id'       => $data['user_id'],
            'label'         => trim($data['label']),
            'field_type'    => $data['field_type'],
            'options'       => !empty($data['options']) ? json_encode($data['options']) : null,
            'placeholder'   => trim($data['placeholder'] ?? ''),
            'help_text'     => trim($data['help_text'] ?? ''),
            'is_required'   => isset($data['is_required']) ? 1 : 0,
            'display_order' => (int)($data['display_order'] ?? 0)
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function saveResponse(int $bookingId, ?int $fieldId, string $fieldLabel, ?string $value): bool {
        $stmt = $this->db->prepare("
            INSERT INTO `booking_form_responses` (`booking_id`, `field_id`, `field_label`, `value`)
            VALUES (:booking_id, :field_id, :field_label, :value)
        ");
        return $stmt->execute([
            'booking_id'  => $bookingId,
            'field_id'    => $fieldId,
            'field_label' => $fieldLabel,
            'value'       => $value
        ]);
    }
}
