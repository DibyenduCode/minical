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

    public function findByCustomDomain(string $domain): ?array {
        $domain = strtolower(trim($domain));
        if (empty($domain)) return null;

        $stmt = $this->db->prepare("
            SELECT p.*, u.username, u.name, u.email, u.status 
            FROM `profiles` p 
            JOIN `users` u ON u.id = p.user_id 
            WHERE LOWER(p.`custom_domain`) = :domain AND u.`status` = 'active' 
            LIMIT 1
        ");
        $stmt->execute(['domain' => $domain]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function updateByUserId(int $userId, array $data): bool {
        $customDomain = !empty($data['custom_domain']) ? strtolower(trim($data['custom_domain'])) : null;
        // Strip http:// or https:// or trailing slashes if user typed full URL
        if ($customDomain) {
            $customDomain = preg_replace('#^https?://#', '', $customDomain);
            $customDomain = rtrim($customDomain, '/');
        }

        $stmt = $this->db->prepare("
            UPDATE `profiles` 
            SET `phone` = :phone, `timezone` = :timezone, `custom_domain` = :custom_domain, `bio` = :bio, `avatar_url` = :avatar_url
            WHERE `user_id` = :user_id
        ");
        return $stmt->execute([
            'phone'         => $data['phone'] ?? null,
            'timezone'      => $data['timezone'] ?? 'UTC',
            'custom_domain' => $customDomain,
            'bio'           => $data['bio'] ?? null,
            'avatar_url'    => $data['avatar_url'] ?? null,
            'user_id'       => $userId
        ]);
    }
}
