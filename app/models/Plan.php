<?php

namespace App\Models;

use App\Core\Model;

class Plan extends Model {
    protected string $table = 'plans';

    public function getActivePlans(): array {
        $stmt = $this->db->prepare("SELECT * FROM `plans` WHERE `status` = 'active' ORDER BY `display_order` ASC, `id` ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllPlans(): array {
        $stmt = $this->db->prepare("SELECT * FROM `plans` ORDER BY `display_order` ASC, `id` ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function createPlan(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO `plans` (`name`, `slug`, `price`, `billing_cycle`, `description`, `features`, `badge`, `button_text`, `is_featured`, `display_order`, `status`)
            VALUES (:name, :slug, :price, :billing_cycle, :description, :features, :badge, :button_text, :is_featured, :display_order, :status)
        ");

        $featuresArr = [];
        if (!empty($data['features_raw'])) {
            $lines = explode("\n", $data['features_raw']);
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (!empty($trimmed)) {
                    $featuresArr[] = $trimmed;
                }
            }
        }

        $stmt->execute([
            'name'          => trim($data['name']),
            'slug'          => strtolower(trim($data['slug'])),
            'price'         => trim($data['price']),
            'billing_cycle' => trim($data['billing_cycle'] ?? 'per month'),
            'description'   => trim($data['description'] ?? ''),
            'features'      => json_encode($featuresArr),
            'badge'         => trim($data['badge'] ?? ''),
            'button_text'   => trim($data['button_text'] ?? 'Get Started'),
            'is_featured'   => isset($data['is_featured']) ? 1 : 0,
            'display_order' => (int)($data['display_order'] ?? 0),
            'status'        => $data['status'] ?? 'active'
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function updatePlan(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE `plans`
            SET `name` = :name, `slug` = :slug, `price` = :price, `billing_cycle` = :billing_cycle, 
                `description` = :description, `features` = :features, `badge` = :badge, 
                `button_text` = :button_text, `is_featured` = :is_featured, `display_order` = :display_order, `status` = :status
            WHERE `id` = :id
        ");

        $featuresArr = [];
        if (!empty($data['features_raw'])) {
            $lines = explode("\n", $data['features_raw']);
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (!empty($trimmed)) {
                    $featuresArr[] = $trimmed;
                }
            }
        }

        return $stmt->execute([
            'name'          => trim($data['name']),
            'slug'          => strtolower(trim($data['slug'])),
            'price'         => trim($data['price']),
            'billing_cycle' => trim($data['billing_cycle'] ?? 'per month'),
            'description'   => trim($data['description'] ?? ''),
            'features'      => json_encode($featuresArr),
            'badge'         => trim($data['badge'] ?? ''),
            'button_text'   => trim($data['button_text'] ?? 'Get Started'),
            'is_featured'   => isset($data['is_featured']) ? 1 : 0,
            'display_order' => (int)($data['display_order'] ?? 0),
            'status'        => $data['status'] ?? 'active',
            'id'            => $id
        ]);
    }
}
