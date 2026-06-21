<?php
require_once APP . '/Core/Model.php';

class Tag extends Model {
    protected static string $table = 'tags';

    public static function create(string $name, string $color = '#3b82f6'): int {
        return Database::insert('INSERT INTO tags (name, color) VALUES (?,?)', [$name, $color]);
    }

    public static function forTicket(int $ticketId): array {
        return Database::fetchAll(
            'SELECT tg.* FROM tags tg JOIN ticket_tags tt ON tt.tag_id=tg.id WHERE tt.ticket_id=?',
            [$ticketId]
        );
    }

    public static function syncTicket(int $ticketId, array $tagIds): void {
        Database::execute('DELETE FROM ticket_tags WHERE ticket_id=?', [$ticketId]);
        foreach ($tagIds as $tid) {
            Database::execute('INSERT INTO ticket_tags (ticket_id, tag_id) VALUES (?,?)', [$ticketId, $tid]);
        }
    }
}
