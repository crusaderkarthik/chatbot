<?php
require_once APP . '/Core/Model.php';

class Setting extends Model {
    protected static string $table = 'settings';
    private static array $cache = [];

    public static function get(string $key, mixed $default = null): mixed {
        if (!isset(self::$cache[$key])) {
            $row = Database::fetch('SELECT value FROM settings WHERE `key`=?', [$key]);
            self::$cache[$key] = $row ? $row['value'] : null;
        }
        return self::$cache[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void {
        self::$cache[$key] = $value;
        Database::execute(
            'INSERT INTO settings (`key`, value) VALUES (?,?) ON DUPLICATE KEY UPDATE value=?',
            [$key, $value, $value]
        );
    }

    public static function bulk(array $data): void {
        foreach ($data as $key => $val) self::set($key, $val);
    }

    public static function allAsArray(): array {
        $rows = Database::fetchAll('SELECT `key`, value FROM settings');
        $out = [];
        foreach ($rows as $r) $out[$r['key']] = $r['value'];
        return $out;
    }
}
