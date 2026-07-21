<?php

namespace App\Core;

use PDO;

abstract class Model {
    protected PDO $db;
    protected string $table = '';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findAll(): array {
        $stmt = $this->db->query("SELECT * FROM `{$this->table}`");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `id` = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM `{$this->table}` WHERE `id` = :id");
        return $stmt->execute(['id' => $id]);
    }
}
