<?php
require_once APP . '/Core/Model.php';

class User extends Model {
    protected static string $table = 'users';

    public static function create(array $data): int {
        return Database::insert(
            'INSERT INTO users (name, email, password, role, department, phone, status, timezone, theme, permissions, notification_prefs, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())',
            [
                $data['name'],
                $data['email'],
                password_hash($data['password'], PASSWORD_BCRYPT),
                $data['role'],
                $data['department'] ?? '',
                $data['phone'] ?? '',
                $data['status'] ?? 'active',
                $data['timezone'] ?? 'UTC',
                $data['theme'] ?? 'light',
                json_encode($data['permissions'] ?? []),
                json_encode($data['notification_prefs'] ?? ['email' => true, 'discord' => false]),
            ]
        );
    }

    public static function update(int $id, array $data): void {
        Database::execute(
            'UPDATE users SET name=?, email=?, role=?, department=?, phone=?, status=?, timezone=?, theme=?, permissions=?, notification_prefs=? WHERE id=?',
            [
                $data['name'], $data['email'], $data['role'],
                $data['department'] ?? '', $data['phone'] ?? '',
                $data['status'], $data['timezone'] ?? 'UTC',
                $data['theme'] ?? 'light',
                json_encode($data['permissions'] ?? []),
                json_encode($data['notification_prefs'] ?? []),
                $id,
            ]
        );
    }

    public static function updatePassword(int $id, string $password): void {
        Database::execute('UPDATE users SET password=? WHERE id=?', [password_hash($password, PASSWORD_BCRYPT), $id]);
    }

    public static function setAvatar(int $id, string $path): void {
        Database::execute('UPDATE users SET avatar=? WHERE id=?', [$path, $id]);
    }

    public static function findByEmail(string $email): ?array {
        return Database::fetch('SELECT * FROM users WHERE email = ?', [$email]);
    }

    public static function active(): array {
        return Database::fetchAll('SELECT * FROM users WHERE status = ? ORDER BY name', ['active']);
    }

    public static function agents(): array {
        return Database::fetchAll("SELECT * FROM users WHERE role IN ('agent','sub_admin','admin') AND status='active' ORDER BY name");
    }
}
