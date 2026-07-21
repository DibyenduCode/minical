<?php

namespace App\Models;

use App\Core\Model;

class Profile extends Model {
    protected string $table = 'profiles';

    public function findByUserId(int $userId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM `profiles` WHERE `user_id` = :user_id LIMIT 1");
        $stmt->execute(['user_id' => $userId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function updateByUserId(int $userId, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE `profiles` 
            SET `phone` = :phone, `timezone` = :timezone, `bio` = :bio, `avatar` = :avatar
            WHERE `user_id` = :user_id
        ");
        return $stmt->execute([
            'phone'    => $data['phone'] ?? null,
            'timezone' => $data['timezone'] ?? 'UTC',
            'bio'      => $data['bio'] ?? null,
            'avatar'   => $data['avatar'] ?? null,
            'user_id'  => $userId
        ]);
    }
}
