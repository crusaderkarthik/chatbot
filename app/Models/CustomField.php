<?php
require_once APP . '/Core/Model.php';

class CustomField extends Model {
    protected static string $table = 'custom_fields';

    public static function create(array $d): int {
        return Database::insert(
            'INSERT INTO custom_fields (label, type, options, is_required, category_id) VALUES (?,?,?,?,?)',
            [$d['label'], $d['type'], $d['options'] ?? '', $d['is_required'] ? 1 : 0, $d['category_id'] ?? null]
        );
    }

    public static function forCategory(?int $categoryId): array {
        if ($categoryId) {
            return Database::fetchAll(
                'SELECT * FROM custom_fields WHERE category_id IS NULL OR category_id=? ORDER BY id',
                [$categoryId]
            );
        }
        return Database::fetchAll('SELECT * FROM custom_fields WHERE category_id IS NULL ORDER BY id');
    }

    public static function saveValues(int $ticketId, array $values): void {
        foreach ($values as $fieldId => $value) {
            Database::execute(
                'INSERT INTO custom_field_values (ticket_id, field_id, value) VALUES (?,?,?) ON DUPLICATE KEY UPDATE value=?',
                [$ticketId, $fieldId, $value, $value]
            );
        }
    }

    public static function valuesForTicket(int $ticketId): array {
        return Database::fetchAll(
            'SELECT cf.label, cf.type, cfv.value FROM custom_field_values cfv
             JOIN custom_fields cf ON cf.id=cfv.field_id
             WHERE cfv.ticket_id=?',
            [$ticketId]
        );
    }
}
