<?php
require_once APP . '/Core/Model.php';

class Category extends Model {
    protected static string $table = 'categories';

    public static function create(string $name, string $description = ''): int {
        return Database::insert('INSERT INTO categories (name, description) VALUES (?,?)', [$name, $description]);
    }

    public static function allWithCount(): array {
        return Database::fetchAll(
            'SELECT c.*, COUNT(t.id) as ticket_count FROM categories c LEFT JOIN tickets t ON t.category_id=c.id GROUP BY c.id ORDER BY c.name'
        );
    }
}
