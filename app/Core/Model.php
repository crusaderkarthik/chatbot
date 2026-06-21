<?php
abstract class Model {
    protected static string $table = '';

    public static function find(int $id): ?array {
        return Database::fetch('SELECT * FROM ' . static::$table . ' WHERE id = ?', [$id]);
    }

    public static function all(string $order = 'id DESC'): array {
        return Database::fetchAll('SELECT * FROM ' . static::$table . ' ORDER BY ' . $order);
    }

    public static function where(string $col, mixed $val): array {
        return Database::fetchAll('SELECT * FROM ' . static::$table . ' WHERE ' . $col . ' = ?', [$val]);
    }

    public static function first(string $col, mixed $val): ?array {
        return Database::fetch('SELECT * FROM ' . static::$table . ' WHERE ' . $col . ' = ?', [$val]);
    }

    public static function delete(int $id): int {
        return Database::execute('DELETE FROM ' . static::$table . ' WHERE id = ?', [$id]);
    }

    public static function count(): int {
        return (int) Database::fetch('SELECT COUNT(*) as c FROM ' . static::$table)['c'];
    }
}
