<?php

namespace App\Core;

class Session {
    public static function init(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_start();
        }
    }

    public static function set(string $key, mixed $value): void {
        self::init();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed {
        self::init();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool {
        self::init();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void {
        self::init();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function destroy(): void {
        self::init();
        session_unset();
        session_destroy();
    }

    public static function flash(string $key, mixed $value = null): mixed {
        self::init();
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return null;
        }
        $val = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $val;
    }

    public static function generateCsrfToken(): string {
        self::init();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrfToken(?string $token): bool {
        self::init();
        if (!$token || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}
