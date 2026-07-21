<?php

namespace App\Models;

use App\Core\Model;

class Event extends Model {
    protected string $table = 'events';

    public function findByUserId(int $userId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM `events` WHERE `user_id` = :user_id LIMIT 1");
        $stmt->execute(['user_id' => $userId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function updateByUserId(int $userId, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE `events` 
            SET `name` = :name, `slug` = :slug, `description` = :description, 
                `duration_minutes` = :duration_minutes, `location_type` = :location_type,
                `is_paid` = :is_paid, `price` = :price, `currency` = :currency, `status` = :status
            WHERE `user_id` = :user_id
        ");
        return $stmt->execute([
            'name'             => trim($data['name']),
            'slug'             => strtolower(trim($data['slug'])),
            'description'      => trim($data['description'] ?? ''),
            'duration_minutes' => (int)($data['duration_minutes'] ?? 30),
            'location_type'    => $data['location_type'] ?? 'online',
            'is_paid'          => isset($data['is_paid']) ? 1 : 0,
            'price'            => (float)($data['price'] ?? 0.00),
            'currency'         => $data['currency'] ?? 'USD',
            'status'           => $data['status'] ?? 'active',
            'user_id'          => $userId
        ]);
    }
}
