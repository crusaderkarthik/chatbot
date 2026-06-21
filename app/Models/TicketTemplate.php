<?php
require_once APP . '/Core/Model.php';

class TicketTemplate extends Model {
    protected static string $table = 'ticket_templates';

    public static function create(array $d): int {
        return Database::insert(
            'INSERT INTO ticket_templates (name, subject, message, category_id, priority) VALUES (?,?,?,?,?)',
            [$d['name'], $d['subject'], $d['message'], $d['category_id'] ?? null, $d['priority']]
        );
    }
}
