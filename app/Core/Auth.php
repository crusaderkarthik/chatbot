<?php
class Auth {
    public static function check(): bool {
        return isset($_SESSION['user_id']);
    }

    public static function user(): ?array {
        if (!self::check()) return null;
        static $cache = null;
        if ($cache === null) {
            $cache = Database::fetch('SELECT * FROM users WHERE id = ?', [$_SESSION['user_id']]);
        }
        return $cache;
    }

    public static function id(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    public static function role(): ?string {
        return self::user()['role'] ?? null;
    }

    public static function can(string $permission): bool {
        $user = self::user();
        if (!$user) return false;
        if ($user['role'] === 'admin') return true;

        $perms = json_decode($user['permissions'] ?? '[]', true);
        return in_array($permission, $perms ?? []);
    }

    public static function login(array $user): void {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
    }

    public static function logout(): void {
        session_destroy();
    }

    public static function attempt(string $email, string $password): bool {
        $user = Database::fetch('SELECT * FROM users WHERE email = ? AND status = ?', [$email, 'active']);
        if (!$user || !password_verify($password, $user['password'])) return false;
        self::login($user);
        return true;
    }
}
