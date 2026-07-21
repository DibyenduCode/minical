<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model {
    protected string $table = 'users';

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM `users` WHERE `email` = :email LIMIT 1");
        $stmt->execute(['email' => strtolower(trim($email))]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function findByUsername(string $username): ?array {
        $stmt = $this->db->prepare("SELECT * FROM `users` WHERE `username` = :username LIMIT 1");
        $stmt->execute(['username' => strtolower(trim($username))]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO `users` (`name`, `username`, `email`, `password_hash`, `role`, `status`)
            VALUES (:name, :username, :email, :password_hash, :role, :status)
        ");
        $stmt->execute([
            'name'          => trim($data['name']),
            'username'      => strtolower(trim($data['username'])),
            'email'         => strtolower(trim($data['email'])),
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role'          => $data['role'] ?? 'user',
            'status'        => $data['status'] ?? 'active'
        ]);

        $userId = (int)$this->db->lastInsertId();

        // Create default profile
        $stmtProfile = $this->db->prepare("INSERT INTO `profiles` (`user_id`, `timezone`) VALUES (:user_id, 'UTC')");
        $stmtProfile->execute(['user_id' => $userId]);

        // Create default 30-min event
        $stmtEvent = $this->db->prepare("
            INSERT INTO `events` (`user_id`, `name`, `slug`, `description`, `duration_minutes`, `location_type`, `is_paid`, `price`, `currency`, `status`)
            VALUES (:user_id, '30 Minute Meeting', '30-min-meeting', 'A 30-minute meeting.', 30, 'online', 0, 0.00, 'USD', 'active')
        ");
        $stmtEvent->execute(['user_id' => $userId]);

        // Create default availability (Mon-Fri 9-17)
        for ($day = 0; $day <= 6; $day++) {
            $isEnabled = ($day >= 1 && $day <= 5) ? 1 : 0;
            $stmtAvail = $this->db->prepare("
                INSERT INTO `availability` (`user_id`, `day_of_week`, `start_time`, `end_time`, `is_enabled`)
                VALUES (:user_id, :day_of_week, '09:00:00', '17:00:00', :is_enabled)
            ");
            $stmtAvail->execute([
                'user_id' => $userId,
                'day_of_week' => $day,
                'is_enabled' => $isEnabled
            ]);
        }

        return $userId;
    }

    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
}
