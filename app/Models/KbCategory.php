<?php
require_once APP . '/Core/Model.php';

class KbCategory extends Model {
    protected static string $table = 'kb_categories';

    public static function create(string $name, ?int $parentId = null): int {
        return Database::insert('INSERT INTO kb_categories (name, parent_id) VALUES (?,?)', [$name, $parentId]);
    }

    public static function tree(): array {
        $all = Database::fetchAll('SELECT * FROM kb_categories ORDER BY name');
        $tree = [];
        foreach ($all as $cat) {
            if (!$cat['parent_id']) {
                $cat['children'] = [];
                $tree[$cat['id']] = $cat;
            }
        }
        foreach ($all as $cat) {
            if ($cat['parent_id'] && isset($tree[$cat['parent_id']])) {
                $tree[$cat['parent_id']]['children'][] = $cat;
            }
        }
        return array_values($tree);
    }
}
