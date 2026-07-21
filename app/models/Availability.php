<?php

namespace App\Models;

use App\Core\Model;

class Availability extends Model {
    protected string $table = 'availability';

    public function getByUserId(int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM `availability` WHERE `user_id` = :user_id ORDER BY `day_of_week` ASC");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function updateDay(int $userId, int $dayOfWeek, string $startTime, string $endTime, int $isEnabled): bool {
        $stmt = $this->db->prepare("
            INSERT INTO `availability` (`user_id`, `day_of_week`, `start_time`, `end_time`, `is_enabled`)
            VALUES (:user_id, :day_of_week, :start_time, :end_time, :is_enabled)
            ON DUPLICATE KEY UPDATE `start_time` = VALUES(`start_time`), `end_time` = VALUES(`end_time`), `is_enabled` = VALUES(`is_enabled`)
        ");
        return $stmt->execute([
            'user_id'     => $userId,
            'day_of_week' => $dayOfWeek,
            'start_time'  => $startTime,
            'end_time'    => $endTime,
            'is_enabled'  => $isEnabled
        ]);
    }
}
