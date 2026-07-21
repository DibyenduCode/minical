<?php

namespace App\Models;

use App\Core\Model;

class Event extends Model {
    protected string $table = 'events';

    public function findByUserId(int $userId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM `events` WHERE `user_id` = :user_id AND `status` = 'active' ORDER BY `id` ASC LIMIT 1");
        $stmt->execute(['user_id' => $userId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function getByUserId(int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM `events` WHERE `user_id` = :user_id ORDER BY `id` ASC");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function findByIdAndUserId(int $id, int $userId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM `events` WHERE `id` = :id AND `user_id` = :user_id LIMIT 1");
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function findBySlugAndUserId(string $slug, int $userId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM `events` WHERE `slug` = :slug AND `user_id` = :user_id AND `status` = 'active' LIMIT 1");
        $stmt->execute(['slug' => $slug, 'user_id' => $userId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function createEvent(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO `events` (`user_id`, `name`, `slug`, `description`, `duration_minutes`, `booking_window_days`, `location_type`, `is_paid`, `price`, `currency`, `status`)
            VALUES (:user_id, :name, :slug, :description, :duration_minutes, :booking_window_days, :location_type, :is_paid, :price, :currency, :status)
        ");
        $stmt->execute([
            'user_id'             => $data['user_id'],
            'name'                => trim($data['name']),
            'slug'                => strtolower(trim($data['slug'])),
            'description'         => trim($data['description'] ?? ''),
            'duration_minutes'    => (int)($data['duration_minutes'] ?? 30),
            'booking_window_days' => (int)($data['booking_window_days'] ?? 30),
            'location_type'       => $data['location_type'] ?? 'online',
            'is_paid'             => isset($data['is_paid']) ? 1 : 0,
            'price'               => (float)($data['price'] ?? 0.00),
            'currency'            => $data['currency'] ?? 'USD',
            'status'              => $data['status'] ?? 'active'
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function updateEvent(int $id, int $userId, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE `events` 
            SET `name` = :name, `slug` = :slug, `description` = :description, 
                `duration_minutes` = :duration_minutes, `booking_window_days` = :booking_window_days, 
                `location_type` = :location_type, `is_paid` = :is_paid, `price` = :price, 
                `currency` = :currency, `status` = :status
            WHERE `id` = :id AND `user_id` = :user_id
        ");
        return $stmt->execute([
            'name'                => trim($data['name']),
            'slug'                => strtolower(trim($data['slug'])),
            'description'         => trim($data['description'] ?? ''),
            'duration_minutes'    => (int)($data['duration_minutes'] ?? 30),
            'booking_window_days' => (int)($data['booking_window_days'] ?? 30),
            'location_type'       => $data['location_type'] ?? 'online',
            'is_paid'             => isset($data['is_paid']) ? 1 : 0,
            'price'               => (float)($data['price'] ?? 0.00),
            'currency'            => $data['currency'] ?? 'USD',
            'status'              => $data['status'] ?? 'active',
            'id'                  => $id,
            'user_id'             => $userId
        ]);
    }
}
